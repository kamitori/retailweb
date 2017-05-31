<?php
namespace RW\Cart;

use Phalcon\Mvc\User\Component;

class Cart extends Component
{

    private $cart;

    public function __construct()
    {
        $this->cart = $this->session->has('cart') ? $this->session->get('cart') : [];
        if (empty($this->cart)) {
            $this->cart = $this->getDefaul();
        }
    }

    private function getDefaul()
    {
        $company_default = \RW\Models\JTCompany::getCompanyDefault();
        return [
                'items' => [],
                'total' => 0,
                'quantity' => 0,
                'taxper' => $company_default['tax'],
                'taxkey' => $company_default['taxkey'],
                'tax_no' => $company_default['tax_no'],
                'tax' => 0,
                'main_total' => 0,
                'note'  => '',
                'order_id'  => '',
                'order_type'  => 0,
                'use_combo' => 0,
                'update_combo_id' => '',
                'combo_step' => 0,
                'combo_id' => '',
                'combo_sales' => 100,
                'combo_list'=>[],
                'use_group'=>0,
                'group_order'=>[],
                'payment_method' =>array(),
                'amount_tendered'=>0,
                'last_your_order_code' =>'',
                'voucher_code' =>'',
                'voucher_value' =>0,
                'voucher_type' =>'$',
                'promo_code' =>'',
                'promo_check' =>0,
                'discount_total' =>0,
                'finalize'=>'',
                'pos_id'=>0,
                'main_total_promo'=>0,
                'free_item_qty'=>0,
                'free_list'=>array()
            ];
    }

    public function buildItems($items)
    {
        if (!is_array($items)) {
            return false;
        }
        $this->cart['items'] = $items;
        return $this->rebuild();
    }

    public function add($data)
    {
        if(isset($this->cart['promo_code']) && $this->cart['promo_code']!='')
            $this->ResetPromo(1); //xoa bo va tinh lai promo
        $data = array_merge(['_id' => '', 'options' => []], $data);
        $user_id = '';
        if( $this->cart['use_group'] == 1){
            $user_id = 'g'.$this->cart['group_order']['step']['user_id'];
        }
        $cartKey = md5((string)$data['_id'].serialize($data['options']).(string)$this->cart['combo_id'].$user_id);
        if (isset($this->cart['items'][$cartKey])) {
            $quantity = $this->cart['items'][$cartKey]['quantity'] + $data['quantity'];
            $priceData = \RW\Models\JTProduct::getPrice([
                                            '_id'   => $data['_id'],
                                            'sizew' => 0,
                                            'sizeh' => 0,
                                            'quantity'  => $quantity,
                                            'companyId' => '',
                                            'options'   => $data['options'],
                                            'fields'    => ['name', 'description','category']
                                        ]);
            $this->update($cartKey, [
                            '_id'       => new \MongoId($data['_id']),
                            'name'      => $priceData['name'],
                            'description'=> $priceData['description'],
                            'category'  => $priceData['category'],
                            'note'      => $data['note'],
                            'image'     => $data['image'],
                            'quantity'  => $quantity,
                            'options'   => $data['options'],
                            'total' => $priceData['sub_total']
                        ]);
        } else {
            if($this->cart['use_combo'] == 1){
                $data['combo_id'] = $this->cart['combo_id'];
                $data['combo_step'] = $this->cart['combo_step'];
                $this->cart['combo_step'] = (int)$this->cart['combo_step']+1;
            }
            if($this->cart['use_group'] == 1){
                $data['user_id'] = $this->cart['group_order']['step']['user_id'];                
            }
            $data['discount'] = isset($this->cart['combo_sales'])?($this->cart['combo_sales']/100):0;
            $this->cart['items'][$cartKey] = $data;
            if($this->cart['update_combo_id']!='' && isset($this->cart['items'][$this->cart['update_combo_id']])){
                unset($this->cart['items'][$this->cart['update_combo_id']]);
                $this->cart['use_combo'] = 0;
                $this->cart['update_combo_id'] = '';
                $this->cart['combo_id'] = '';
                $this->cart['combo_sales'] = 100;
                $this->cart['combo_step'] = 0;
            }

            //re-sort the cart items, by user_id
            uasort($this->cart['items'], function($a, $b) {
                if(!isset($a['user_id'])) $a['user_id'] = -1;                
                if(!isset($b['user_id'])) $b['user_id'] = -1;                
                return intval($a['user_id']) - intval($b['user_id']);
            });
        }
        $this->rebuild();
        return $cartKey;
    }

    public function update($cartKey, $data)
    {   if(isset($this->cart['promo_code']) && $this->cart['promo_code']!='')
            $this->ResetPromo(1); //xoa bo va tinh lai promo

        if (!isset($this->cart['items'][$cartKey])) {
            throw new Exception("The item \"{$cartKey}\" did not exist.");
        }
        $this->cart['items'][$cartKey] = array_merge($this->cart['items'][$cartKey], $data);
        
        $this->rebuild();
        return $this->get();
    }
    private function updateTotalQuantity()
    {
        $this->cart['total'] = $this->cart['quantity'] = $this->cart['tax'] = $this->cart['discount_total'] = $this->cart['free_item_qty'] = 0;
        $tmp = array();
        if(!isset($this->cart['taxper']))
            $this->cart['taxper'] = 5;
        // pr($this->cart['items']);die;
        foreach ($this->cart['items'] as $item) {
            
            if(!isset($item['taxper']))
                    $item['taxper'] =  $this->cart['taxper'];
            //tinh total cua combo_list
            if(isset($item['combo_id'])){
                if(!isset($tmp[$item['combo_id']])){
                    $tmp[$item['combo_id']]['total'] = $tmp[$item['combo_id']]['item_qty'] = 0;
                    $tmp[$item['combo_id']]['quantity'] = 0;
                }
                $tmppls = $item['total'] - $item['discount']*$item['total'];
                $tmp[$item['combo_id']]['total'] += $tmppls;
                //tru discount truoc thue
                $disamount = 0;
                if(isset($this->cart['voucher_type']) && $this->cart['voucher_type']=='%'){
                    $disamount = round((float)$tmppls*$this->cart['voucher_value']/100,2);
                    $this->cart['discount_total'] += $disamount;
                    $item['total'] = $tmppls - $disamount;
                    $tmp[$item['combo_id']]['total'] = $tmp[$item['combo_id']]['total'] - $disamount;
                }
                $tmp[$item['combo_id']]['tax'] += $tmp[$item['combo_id']]['total']*$item['taxper']/100;
                $tmp[$item['combo_id']]['item_qty'] += (int)$item['quantity'];
            //tinh toan total group
            }else if(isset($item['user_id'])){
                if(!isset($gtmp[$item['user_id']])){
                    $gtmp[$item['user_id']]['total'] = $gtmp[$item['user_id']]['item_qty'] = 0;
                    $gtmp[$item['user_id']]['quantity'] = 0;
                }
                $gtmp[$item['user_id']]['total'] += $item['total'];
                //tru discount truoc thue
                $disamount = 0;
                if(isset($this->cart['voucher_type']) && $this->cart['voucher_type']=='%'){
                    $disamount = round((float)$item['total']*$this->cart['voucher_value']/100,2);
                    $this->cart['discount_total'] += $disamount;
                    $item['total'] = $item['total'] - $disamount;
                }
                $gtmp[$item['user_id']]['tax'] += $item['total']*$item['taxper']/100;
                $gtmp[$item['user_id']]['quantity'] += (int)$item['quantity'];
            
            }else{
                $this->cart['total'] += $item['total'];
                //tru discount truoc thue
                $disamount = 0;
                if(isset($this->cart['voucher_type']) && $this->cart['voucher_type']=='%'){
                    $disamount = round((float)$item['total']*$this->cart['voucher_value']/100,2);
                    $this->cart['discount_total'] += $disamount;
                    $item['total'] = $item['total'] - $disamount;
                }
                $this->cart['tax'] += $item['total']*$item['taxper']/100;
                $this->cart['quantity'] += $item['quantity'];
            }

            //check BMS 11inch
            if(isset($item['options']) && count($item['options'])>0){
                foreach ($item['options'] as $key => $value) {
                    if(isset($value['_id']) && isset($value['quantity']) && (string)$value['_id'] =='56491b54124dca9460f4d4b0' && $value['quantity'] =='1'){
                            //free 1 nuoc ngot hoac nuoc loc cho cartitem nay
                            $this->cart['free_item_qty'] += (int)$item['quantity'];
                            break;
                    }
                }
            }
        }
        foreach ($this->cart['combo_list'] as $key => $value) {
            if(isset($tmp[$key])){
                $this->cart['combo_list'][$key]['item_qty'] = $tmp[$key]['item_qty'];
                $this->cart['combo_list'][$key]['total'] = round((float)($tmp[$key]['total']*$this->cart['combo_list'][$key]['quantity']),2);
                $this->cart['combo_list'][$key]['tax'] =  $tmp[$key]['tax'];
                $this->cart['quantity'] += $this->cart['combo_list'][$key]['item_qty'];
                $this->cart['total'] += $this->cart['combo_list'][$key]['total'];
                $this->cart['tax'] += $this->cart['combo_list'][$key]['tax'];
            }
        }
        if(isset($this->cart['group_order']['list']))
        foreach ($this->cart['group_order']['list'] as $key => $value) {
            if(isset($gtmp[$key])){
                $this->cart['group_order']['list'][$key]['quantity'] = $gtmp[$key]['quantity'];
                $this->cart['group_order']['list'][$key]['total'] = $gtmp[$key]['total'];
                $this->cart['group_order']['list'][$key]['tax'] = $gtmp[$key]['tax'];
                $this->cart['quantity'] += $gtmp[$key]['quantity'];
                $this->cart['total'] += $gtmp[$key]['total'];
                $this->cart['tax'] += $gtmp[$key]['tax'];
            }
        }
        $this->cart['total'] =  round((float)$this->cart['total'],2);
        if(isset($this->cart['voucher_type']) && $this->cart['voucher_type']=='%'){
            $this->cart['discount_total'] = round($this->cart['discount_total'],2);
        }else if(isset($this->cart['voucher_type']) && $this->cart['voucher_type']!=''){
            $this->cart['discount_total'] = round((float)$this->cart['voucher_value'],2);
        }
        $this->cart['main_total'] = $this->cart['total'] - $this->cart['discount_total'] + $this->cart['tax'];
    }

    private function rebuild($cart = []){
        $cookies_order_pos = trim($this->cookies->get('cookies_order_pos')->getValue());
        if (!empty($cart)) {
            //giu lai cac thong so quan trong khi deploy
            $deploy_data = array();
            $finalize= json_decode(file_get_contents("finalize.json"),true);
            if(isset($cart['last_your_order_code']))
               $cart['last_your_order_code'] = $deploy_data['finalize'] =  $this->cart['last_your_order_code'];
            // else
            //     $deploy_data['finalize'] = $this->cart['finalize'];
            
            if(isset($this->cart['finalize']) && $this->cart['finalize']!='')
                $cart['finalize'] = $deploy_data['finalize'] = $this->cart['finalize'];

            $finalize[$cookies_order_pos] = $deploy_data;
            file_put_contents("finalize.json", json_encode($finalize));
            $this->cart = $cart;
        }
        //check promo
        if(isset($this->cart['promo_check']) && $this->cart['promo_check']==1){
            $this->ProcessPromo();
        }

        if(isset($this->cart['promo_check']) && $this->cart['promo_check']==2){
            $this->ResetPromo(0); //xoa bo va ko tinh lai promo
        }

        $this->updateTotalQuantity();
        //save pos_id
        $arr_cookies_id = json_decode(file_get_contents("cookies_id.json"),true);
        if(count($arr_cookies_id)>0)
        foreach ($arr_cookies_id as $key => $value) {
            if(trim((string)$value)==$cookies_order_pos){
                $this->cart['pos_id'] = $key;
                break;
            }
        }
        //save seesion to file
        $arr_cart= json_decode(file_get_contents("datacart.json"),true);
        $arr_cart[$cookies_order_pos] = $this->cart;
        file_put_contents("datacart.json", json_encode($arr_cart));
        return $this->session->set('cart', $this->cart);
    }

    public function get($cartKey = '')
    {
        if (!empty($cartKey)) {
            if (!isset($this->cart['items'][$cartKey])) {
                throw new Exception("The item \"{$cartKey}\" did not exist.");
            }
            return $this->cart['items'][$cartKey];
        }
        return $this->cart;
    }

    public function combo_list()
    {
        return $this->cart['combo_list'];
    }

    public function getItems()
    {
        return $this->cart['items'];
    }

    public function getTotal()
    {
        return $this->cart['total'];
    }

    public function getQuantity()
    {
        return $this->cart['quantity'];
    }

    public function getDetailTotal()
    {
        return array(   'total' => $this->cart['total'],
                        'taxper' => $this->cart['taxper'],
                        'tax' => $this->cart['tax'],
                        'main_total' => $this->cart['main_total']
                    );
    }

    public function updateNote($note = '')
    {
        $this->cart['note'] = $note;
        return $this->rebuild();
    }

    public function updateTax($taxper=0){
        $this->cart['taxper'] = $taxper;
        if(isset($this->cart['promo_code']) && $this->cart['promo_code']!='')
            $this->ResetPromo(1); //xoa bo va tinh lai promo
        return $this->rebuild();
    }
    public function setCart($data){
        $this->rebuild($data);
    }
    public function setOrderType($type=0){
        $this->cart['order_type'] = $type;
        return $this->rebuild();
    }
    public function remove($cartKey)
    {
        if (isset($this->cart['items'][$cartKey])) {
            if(isset($this->cart['promo_code']) && $this->cart['promo_code']!='')
                $this->ResetPromo(1); //xoa bo va tinh lai promo
            unset($this->cart['items'][$cartKey]);
            return $this->rebuild();
        }
        throw new Exception("The item \"{$cartKey}\" did not exist.");
    }

    public function destroy(){
        $arr_cart= json_decode(file_get_contents("datacart.json"),true);
        unset($arr_cart[trim($this->cookies->get('cookies_order_pos')->getValue())]);
        file_put_contents("datacart.json", json_encode($arr_cart));
        return $this->rebuild($this->getDefaul());
    }

    public function setcombo($value,$product_id='',$combo_sales=1)
    {
        if($value==1){
            $this->cart['combo_id'] = count($this->cart['combo_list']);
            $this->cart['combo_sales'] = $combo_sales;
            $this->cart['combo_step'] = 1;
            $tmp = array();
            $priceData = \RW\Models\JTProduct::getPrice([
                                            '_id'   => $product_id,
                                            'sizew' => 0,
                                            'sizeh' => 0,
                                            'quantity'  => 1,
                                            'companyId' => '',
                                            'options'   => array(),
                                            'fields'    => ['name', 'description']
                                        ]);
            $tmp['product_id']  = $product_id;
            $tmp['name']        = $priceData['name'];
            $tmp['description'] = $priceData['description'];
            $tmp['image'] = '';
            $tmp['quantity'] = 1;
            $tmp['item_qty'] = 0;
            $tmp['combo_sales'] = $combo_sales;
            $tmp['total'] = 0;
            $tmp['tax'] = 0;          
            $this->cart['combo_list'][] = $tmp;
        }
        $this->cart['use_combo'] = $value;
        return $this->rebuild();
    }
    public function cancelcombo($combo_id='')
    {
        //xoa cac item product
        if($combo_id=='')
            $combo_id = $this->cart['combo_id'];
        foreach ($this->cart['items'] as $key => $value){
            if(isset($value['combo_id']) && $value['combo_id']==$this->cart['combo_id']){
                unset($this->cart['items'][$key]);
            }
        }
        if(isset($this->cart['combo_list'][$combo_id])){
            unset($this->cart['combo_list'][$combo_id]);
        }
        $this->cart['use_combo'] = 0;
        $this->cart['combo_id'] = '';
        $this->cart['combo_sales'] = 100;
        $this->cart['combo_step'] = 0;
        return $this->rebuild();
    }
    public function changeqtyCombo($combo_id='',$newqty=''){
        if($combo_id!='' && $newqty!='' && isset($this->cart['combo_list'][$combo_id])){
            $this->cart['combo_list'][$combo_id]['quantity']=$newqty;
        }
        return $this->rebuild();
    }
    public function checkcombo()
    {
        return $this->cart['use_combo'];
    }
    public function combostep()
    {
        return $this->cart['combo_step'];
    }
    public function returnstep($cartKey='')
    {
        if (isset($this->cart['items'][$cartKey])){
            $this->cart['use_combo'] = 1;
            $this->cart['update_combo_id']= $cartKey;
            $this->cart['combo_id'] = $this->cart['items'][$cartKey]['combo_id'];
            $this->cart['combo_step']= $this->cart['items'][$cartKey]['combo_step'];
            $this->cart['combo_sales'] = 100*$this->cart['items'][$cartKey]['discount'];
        }
        return $this->rebuild();
    }
    public function get_voucher(){
        return isset($this->cart['voucher_code'])?$this->cart['voucher_code']:"";
    }
    public function cancelUserGroup($user_id='')
    {
        //xoa cac item product
        // if($user_id=='')
        //     $user_id = $this->cart['user_id'];
        foreach ($this->cart['items'] as $key => $value){
            if(isset($value['user_id']) && $value['user_id']==$user_id){
                unset($this->cart['items'][$key]);
            }
        }

        foreach ($this->cart['group_order']['list'] as $key => $value){
            if(isset($value['user_id']) && $value['user_id']==$user_id){
                unset($this->cart['group_order']['list'][$key]);
            }
        }

        return $this->rebuild();
    }

    public function returnUserGroup($cartKey='', $data)
    {
        if (isset($this->cart['items'][$cartKey])){
            $this->cart['use_group'] = 1;

            $group = $this->cart['group_order'];
            $group['step']['group_product_id'] = $data['group_product_id'];
            $group['step']['user_name'] = $data['user_name'];
            $group['step']['user_id'] = $data['next_uid'];
            $group['step']['quantity'] = 0;
            $group['step']['total'] = 0;
            $group['step']['tax'] = 0;
            
            $this->cart['group_order'] = $group;

        }
        return $this->rebuild();
    }    

    public function setUseGroup($val=0,$product_id='',$username=''){
        $this->cart['use_group'] = $val;
        if($val==1){
            $group = $this->cart['group_order'];
            if(!isset($group['list']))
                $group['list'] = array();
            $group['step']['group_product_id'] = $product_id;
            $group['step']['user_name'] = $username;
            $group['step']['user_id'] = count($group['list']);
            $group['step']['quantity'] = 0;
            $group['step']['tax'] = 0;
            $group['step']['total'] = 0;

            
            $group['list'][] = $group['step'];
            $this->cart['group_order'] = $group;
        }
        return $this->rebuild();
    }
    public function checkgroup(){
        if(isset($this->cart['use_group']))
            return $this->cart['use_group'];
        else
            return '';
    }
    public function group_list(){
        if(isset($this->cart['group_order']))
            return $this->cart['group_order'];
        else
            return array();
    }
    public function user_group_list(){
        if(!isset($this->cart['group_order']['list']))
            return array();
        $arr_return = array();
        foreach ($this->cart['group_order']['list'] as $key => $value) {
            $arr_return[$value['user_id']] = $value['user_name'];
        }
        return $arr_return;
    }
    public function user_group_now(){
        if(isset($this->cart['group_order']['list'])){
            $idkey = count($this->cart['group_order']['list'])-1;
            if(isset($this->cart['group_order']['list'][$idkey]['user_name'])){
                return $this->cart['group_order']['list'][$idkey]['user_name'];
            }
        }
        return '';
    }
    public function next_uid($user_id=''){
        if(!isset($this->cart['group_order']['list']))
            return 1;
        elseif($user_id != ''){
            $group = $this->cart['group_order'];
            $next_uid = 0;
            $user_name = '';
            $group_product_id = '';
            foreach ($group['list'] as $key => $value) {
                if($value['user_id'] == $user_id)
                {
                    $user_name = $value['user_name'];
                    $group_product_id = $value['group_product_id'];
                    break;
                }
            }
            return ['next_uid'=>$user_id, 'user_name'=>$user_name, 'group_product_id'=>$group_product_id];
        }
        else
            return count($this->cart['group_order']['list'])+1;
    }
    public function user_data_list(){
        if(!isset($this->cart['group_order']['list']))
            return array();
        return $this->cart['group_order']['list'];
    }
    public function update_payment_mt($payment_method=array(),$amount_tendered=0){
        $change = 0;
        if(!empty($payment_method)){
            $this->cart['payment_method'] = $payment_method;
            $change = 1;
        }
        if($amount_tendered>0){
            $this->cart['amount_tendered'] = $amount_tendered;
            $change = 1;
        }
        if($change == 1)
            return $this->rebuild();
    }
    public function update_last_code($last_your_order_code=''){
        if($last_your_order_code=='off_finalize'){ 
            $this->cart['finalize'] = '';
            $this->cart['last_your_order_code'] = '';
            return $this->rebuild();
        }else if($last_your_order_code!=''){
            $this->cart['last_your_order_code'] = $last_your_order_code;
            $this->cart['finalize'] = 'done';
            return $this->rebuild();
        }

    }
    public function getDataField($fieldname='total',$default=''){
        if(isset($this->cart[$fieldname]))
            return $this->cart[$fieldname];
        else
            return $default;
    }

    public function updateDataField($fieldname,$value){
        if(!isset($this->cart[$fieldname])){
            throw new Exception("The filed $fieldname did not exist.");
        }else{
            $this->cart[$fieldname] = $value;
            return $this->rebuild();
        }
    }
    public function update_vouchers($arr_voucher=array()){
        $arr =array();
        if(isset($arr_voucher['type']) && isset($arr_voucher['value']) && isset($arr_voucher['name'])){
            if($arr_voucher['type']=='%')
                $arr['value'] = $this->cart['total']*$arr_voucher['value']/100;
            else
                $arr['value'] = $arr_voucher['value'];

            $this->cart['voucher_code']  = $arr_voucher['name'];
            $this->cart['voucher_value'] = $arr_voucher['value'];
            $this->cart['voucher_type']  = $arr_voucher['type'];
            $this->rebuild();
            $arr['voucher_main_total'] = $this->cart['main_total'];
        }
        return $arr;
    }
    public function remove_vouchers(){
        $arr =array();
        $this->cart['voucher_code']  = '';
        $this->cart['voucher_value'] = 0;
        $this->cart['voucher_type']  = '$';
        $this->rebuild();
        $arr['new_main_total'] = $this->cart['main_total'];
        return $arr;
    }
    public function update_promo($arr_promo=array()){
        $arr =array();
        if(isset($this->cart['promo_check']) && $this->cart['promo_check']!=1 && $arr_promo['name']!=''){ //check xem co chua
            $this->cart['promo_code']  = $arr_promo['name'];
            $this->cart['promo_check'] = 1;
            $this->rebuild();
        }
        $arr['promo_main_total'] = $this->cart['main_total'];
        $arr['value'] = $this->cart['main_total_promo'];
        return $arr;
    }
    public function remove_promo(){
        $arr =array();
        // $this->cart['promo_code']  = '';
        $this->cart['promo_check'] = 2;
        $this->rebuild();
        $arr['new_main_total'] = $this->cart['main_total'];
        return $arr;
    }
    public function get_paid_by(){
        $arr_return = array();
        if(isset($this->cart['payment_method'])){
            foreach ($this->cart['payment_method'] as $key => $value) {
               $arr_return[] = $key;
            }
        }
        return $arr_return;
    }
    public function get_paid_by_value(){
        $arr_return = array();
        if(isset($this->cart['payment_method'])){
            foreach ($this->cart['payment_method'] as $key => $value) {
               $arr_return[$key] = $value;
            }
        }
        return $arr_return;
    }

    public function ResetPromo($new_status=0){
        foreach ($this->cart['items'] as $cartkey => $item) {
            $this->cart['items'][$cartkey]['quantity_promo'] = 0;
            $this->cart['items'][$cartkey]['total'] = $item['total'] + $item['total_promo'];
            $this->cart['items'][$cartkey]['total_promo'] = 0;
        }
        $this->cart['main_total_promo'] = 0;
        $this->cart['promo_check'] = $new_status;
        if($new_status==0){
            $this->cart['promo_code'] =   '';     
        }
    }

    public function ProcessPromo(){
        $arr_big = $arr_sort = $arr_sate = $arr_sate_sort = $arr_key_cate = $qty_by_cate = $more_item = array();
        $bms_qty = $promo_qty = $sate_qty = $promo_sate_qty = $more_item_qty = $bms11in = 0;
        $promo_code = $this->cart['promo_code'];
        foreach ($this->cart['items'] as $cartkey => $item) {
            //mua 1 tang 1 cung category
            if($promo_code=='241' && isset($item['category']) && $item['category']!=''){
                $key_cate = round($item['sell_price']*100);
                while (isset($arr_key_cate[$item['category']][$key_cate])) {
                    $key_cate++;
                }
                $arr_key_cate[$item['category']][$key_cate] = $cartkey;
                $arr_big[$cartkey] = $item;
                if(!isset($qty_by_cate[$item['category']]))
                    $qty_by_cate[$item['category']] = 0;
                $qty_by_cate[$item['category']] += (int)$item['quantity'];
            }
            //dem so luong banh mi 11inch
            if(isset($item['options']) && count($item['options'])>0 && $promo_code=='101'){
                foreach ($item['options'] as $key => $value) {
                    if(isset($value['_id']) && isset($value['quantity']) && (string)$value['_id'] =='56491b54124dca9460f4d4b0' && $value['quantity'] =='1'){
                            $bms11in += (int)$item['quantity'];
                            break;
                    }
                }
            }
            //tim cac product free 101
            if($promo_code=='101' && in_array((string)$item['_id'],$this->cart['free_list']) ){
                 $more_item[$cartkey] = $item;
            }

            //giam gia 15% cho banh mi 11 inch
            if(isset($item['options']) && count($item['options'])>0 && $promo_code=='015'){
                foreach ($item['options'] as $key => $value) {
                    if(isset($value['_id']) && isset($value['quantity']) && (string)$value['_id'] =='56491b54124dca9460f4d4b0' && $value['quantity'] =='1'){
                            $item['quantity_promo'] = 0.15*(int)$item['quantity'];
                            $item['total_promo'] = round((float)$item['total']*0.15,2);
                            $item['total'] = $item['total'] - $item['total_promo'];
                            $this->cart['items'][$cartkey] = $item;
                            $this->cart['main_total_promo'] += $item['total_promo'];
                            break;
                    }
                }
            }

            //giam gia cho banh mi nuong mui ot
            if(isset($item['_id']) && (string)$item['_id']=='5789a88b124dcacd2c965269'){
                // $sate_key = $cartkey;
                $arr_sate[$cartkey] = $item;
                
                $price_key = round($item['sell_price']*100);
                while (isset($arr_sate_sort[$price_key])) {
                    $price_key++;
                }
                $arr_sate_sort[$price_key] = $cartkey;
                $sate_qty += (int)$item['quantity'];

            //giam gia cho banh mi 11 inch
            }else if(isset($item['options']) && count($item['options'])>0 && $promo_code=='301'){
                foreach ($item['options'] as $key => $value) {
                    if(isset($value['_id']) && isset($value['quantity']) && (string)$value['_id'] =='56491b54124dca9460f4d4b0' && $value['quantity'] =='1'){
                            $arr_big[$cartkey] = $item;
                            $price_key = round($item['sell_price']*100);
                            while (isset($arr_sort[$price_key])) {
                                $price_key++;
                            }
                            $arr_sort[$price_key] = $cartkey;
                            $bms_qty += (int)$item['quantity'];
                            break;
                    }
                }
            }
            
        }
        //check qty of promo item
        $promo_qty = floor($bms_qty/4);
        $promo_sate_qty = floor($sate_qty/4);
        //sort by sell_price
        ksort($arr_sort);
        ksort($arr_sate_sort);
        
        //loop and get promo item BMS 11 inch
        if($promo_code=='301'){
            foreach ($arr_sort as $key => $cartkey){
                $qty = (int)$arr_big[$cartkey]['quantity'];
                if($qty<$promo_qty){
                    $arr_big[$cartkey]['quantity_promo'] = $qty;
                    $promo_qty = $promo_qty - $qty;
                }else{
                    $arr_big[$cartkey]['quantity_promo'] = $promo_qty;
                    break;
                }
            }
        }
        //check sate bread
        if($promo_code=='301' || $promo_code=='401'){
            foreach ($arr_sate_sort as $key => $cartkey){
                $qty = (int)$arr_sate[$cartkey]['quantity'];
                if($qty<$promo_sate_qty){
                    $arr_sate[$cartkey]['quantity_promo'] = $qty;
                    $promo_sate_qty = $promo_sate_qty - $qty;
                }else{
                    $arr_sate[$cartkey]['quantity_promo'] = $promo_sate_qty;
                    break;
                }
            }
        }
        if($promo_code=='241'){
            foreach ($arr_key_cate as $cate => $arr_keys){
                ksort($arr_keys);
                $promo_cate_qty = floor($qty_by_cate[$cate]/2);
                foreach ($arr_keys as $sortkey => $cartkey){
                    $qty = (int)$arr_big[$cartkey]['quantity'];
                    if($qty<$promo_cate_qty){
                        $arr_big[$cartkey]['quantity_promo'] = $qty;
                        $promo_cate_qty = $promo_cate_qty - $qty;
                    }else{
                        $arr_big[$cartkey]['quantity_promo'] = $promo_cate_qty;
                        break;
                    }
                }
            }
        }
        if($promo_code=='101'){
            foreach ($more_item as $key => $value) {
                if($value['quantity']<$bms11in){
                    $arr_big[$key] = $value;
                    $arr_big[$key]['quantity_promo'] = $value['quantity'];
                    $bms11in = $bms11in - $value['quantity'];
                }else{
                    $arr_big[$key] = $value;
                    $arr_big[$key]['quantity_promo'] = $bms11in;
                     break;
                }
            }
        }

        $arr_big = array_merge($arr_big,$arr_sate);
        foreach ($arr_big as $cartkey => $item){
            //tinh lai $item['total'] khi co promo
            $item['total_promo'] = ($item['total']/(int)$item['quantity'])*$item['quantity_promo'];
            $item['total'] = round((float)$item['total'] - $item['total_promo'],2);
            $this->cart['items'][$cartkey] = $item;
            $this->cart['main_total_promo'] += $item['total_promo'];
        }       

        $this->cart['promo_check'] = 0;
    }


    public function add_free_item($arr_free_item=array(),$arr_promo=array()){
        
        $arr = $list = $free_list = array();

        $list['5681c266124dcae13ab42c3e'] = array(
                'name' => 'POP Drink',
                'description' => '',
                'category' => 'Drink',
                'image' => 'http://jt.banhmisub.com/upload/2016_04/2016_04_21_161955_551925.png',
            );
        $list['5716ff49124dcada0530dc80'] = array(
                'name' => 'Water Bottle',
                'description' => 'Nước Suối',
                'category' => 'Drink',
                'image' => 'http://jt.banhmisub.com/upload/2016_04/2016_04_21_164922_254362.png',
            );

        foreach ($arr_free_item as $key => $value) {
            $free_list[]= $key;
            $priceData = \RW\Models\JTProduct::getPrice([
                            '_id'   => new \MongoId($key),
                            'sizew' => 0,
                            'sizeh' => 0,
                            'quantity'  => (int)$value,
                            'companyId' => '',
                            'options'   => null,
                            'fields'    => ['name', 'description','category','sell_price']
                        ]);
            $data = array(
                '_id'           => new \MongoId($key),
                'name'          => $priceData['name'],
                'description'   => $priceData['description'],
                'category'      => $priceData['category'],
                'note'          => '',
                'image'         => $list[$key]['image'],
                'quantity'      => (int)$value,
                'options'       => null,
                'sell_price'    => $priceData['sell_price'],
                'taxper'        => 0,
                'total'         => $priceData['sub_total']
            );
            $cartKey = md5($key.serialize($data['options']).(string)$this->cart['combo_id'].$user_id);

            
            
            if($data['quantity']==0 && isset($this->cart['items'][$cartKey]))
                $this->remove($cartKey);
            
            // $free_list[$cartKey] = $data;
            if (isset($this->cart['items'][$cartKey]))
                $this->update($cartKey,$data);
            else
                $this->cart['items'][$cartKey] = $data;
        }
        $this->cart['free_list'] = $free_list;
        $this->cart['promo_code']  = $arr_promo['name'];
        $this->cart['promo_check'] = 1;
        $this->rebuild();

        return $free_list;
    }

}
