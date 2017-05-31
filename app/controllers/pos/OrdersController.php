<?php
namespace RW\Controllers\Pos;

use RW\Models\JTOrder;
use RW\Models\JTQuotation;
use RW\Models\JTCompany;
use RW\Models\JTContact;


class OrdersController extends ControllerBase
{
    public $is_process;
    public function indexAction()
    {
        $orders = JTOrder::find();
        $order_list = array();
        foreach ($orders as $key => $order) {
            $order_list[] = $order->toArray();
        }
        // pr($order_list);
    }

    public function repeatLastOrderAction()
    {
        if ($items = (new \RW\Models\JTOrder)->getLast($this->session->get('user'))){
            (new \RW\Cart\Cart)->buildItems($items);
        }
        return $this->response->redirect(URL.'/pos/carts');
    }
    public function savedOrdersAction(){
        $order_list = array();
        $items = (new \RW\Models\JTQuotation)->getPosList();
        foreach ($items as $key => $value) {
            $value->order_id =  (string)$value->_id;
            $value->time = date("d-m-Y H:i:s",$value->date_modified->sec);
            $order_list[] = $value->toArray();
        }
        // pr($order_list);die;
        $this->view->disable();
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        // pr($items);die;
        $this->view->partial('pos/blocks/saved_orders');
    }
    public function createOrderAction(){
        $provinceId = '';
        if ($province = $this->request->getPost('province')) {
            list($provinceId, $province) = explode('-', $province);
        }
        $cash_tend = $this->request->hasPost("cash_tend")?$this->request->getPost("cash_tend"):0;
        $Paidby = $this->request->hasPost("Paidby")?$this->request->getPost("Paidby"):0;
        $cash_tend = floatval($cash_tend);
        $soStatus = $this->request->hasPost("soStatus")?$this->request->getPost("soStatus"):'';
        $orderType = $this->request->hasPost("orderType")?$this->request->getPost("orderType"):0;
        $pos_delay = 0;

        if($soStatus=='In progress' && $orderType==0){
            $pos_delay = 1;
            $somongo = new \RW\Models\JTQuotation;
        }else{
            $pos_delay = 0;
            $somongo = new \RW\Models\JTOrder;
        }
        $emailInfo = $this->request->hasPost("emailInfo")?$this->request->getPost("emailInfo"):'';
        $nameInfo = $this->request->hasPost("nameInfo")?$this->request->getPost("nameInfo"):'';
         $arr_name = explode(' ', $nameInfo);
         $first_name = '';
         $last_name = '';
         foreach ($arr_name as $key => $value) {
            if($key==0){
                $first_name = $value;
            }else{
                $last_name .= $value.' ';
            }
         }
        $contact = JTContact::findFirst(array(array('email'=>$emailInfo)));
        if(!$contact){
            $contact = new JTContact();
            $contact->email = $emailInfo;
            $contact->fullname = $nameInfo;
            $contact->first_name = $first_name;
            $contact->last_name = $last_name;
            try {
                $contact->save();
            } catch (\RW\Cart\Exception $e) {
            }
        }
        $contact = $contact->toArray();
        $invoiceAddress =[[
            'deleted'   => false,
            'shipping_address_1' => $this->request->getPost('address', null, ''),
            'shipping_address_2' => '',
            'shipping_address_3' => '',
            'shipping_town_city' => $this->request->getPost('town_city', null, ''),
            'shipping_zip_postcode' => $this->request->getPost('postal_code', null, ''),
            'shipping_province_state_id' => $provinceId,
            'shipping_province_state' => $province,
            'shipping_country'    => 'Canada',
            'shipping_country_id' => 'CA',
        ]];
        $shippingAddress =[[
            'deleted'   => false,
            'shipping_address_1' => $this->request->getPost('address', null, ''),
            'shipping_address_2' => '',
            'shipping_address_3' => '',
            'shipping_town_city' => $this->request->getPost('town_city', null, ''),
            'shipping_zip_postcode' => $this->request->getPost('postal_code', null, ''),
            'shipping_province_state_id' => $provinceId,
            'shipping_province_state' => $province,
            'shipping_country'    => 'Canada',
            'shipping_country_id' => 'CA',
        ]];
        $deliveryMethod = '';
        if ($this->request->getPost('chk_type_cart') == 'pickup') {
            $deliveryMethod = 'Call for Pick Up';
        }
        if($contact){
            if ($somongo->add($contact, [
                'delivery_method' => $deliveryMethod,
                'invoice_address'  => $invoiceAddress,
                'shipping_address'  => $shippingAddress,
                'cash_tend' => $cash_tend,
                'paid_by' => $Paidby,
                'pos_delay'=>$pos_delay
            ])) {
                (new \RW\Cart\Cart)->destroy();
            }
        }else{
            $user = array('_id'=>new \MongoId('564694b0124dca8603f4d46f'));
            if ($somongo->add($user, [
                'delivery_method' => $deliveryMethod,
                'invoice_address'  => $invoiceAddress,
                'shipping_address'  => $shippingAddress,
                'paid_by' => $Paidby,
                'cash_tend' => $cash_tend, //payment
                'pos_delay'=>$pos_delay
            ])) {
                (new \RW\Cart\Cart)->destroy();
            }
        }
        if($this->is_process=='call_from_edit'){
            return '';
        }else if ($this->request->isAjax()){
           $this->view->disable();
           return '';
        }else{
            return $this->response->redirect(URL.'/pos/orders');
        }

    }
    public function editOrderAction(){
        $this->is_process=='call_from_edit';
        $this->createOrderAction();
        $this->is_process=='';
        $order_id = $this->request->hasPost("orderId")?$this->request->getPost("orderId"):'';
        if($order_id=='')
            return false;
        $orderList = JTQuotation::findFirst(array(
            'conditions'=>array('_id'=>new \MongoId($order_id))
        ));
        $orderList = $orderList->toArray();
        
        if(isset($orderList['cart'])){
            $orderList['cart']['order_id'] = $order_id;
            (new \RW\Cart\Cart)->setCart($orderList['cart']);
        }
        $arr_pro =array();
        $items = (new \RW\Cart\Cart)->get();
        
        foreach ($items['items'] as $key => $value) {
            if(isset($value['options']))
                foreach ($value['options'] as $kk => $op) {
                    if($op['option_type']!='')
                    $arr_pro[$op['_id']] = $op['option_type'];
                }
        }
        // pr($items);die;
        $this->view->disable();
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->taxlist = $this->taxlist();
        $this->view->cart = $items;
        $this->view->baseURL =URL;
        $this->view->partial('pos/blocks/view_cart');
        
    }
    public function deleteOrderAction(){
        $qt = 1;
        if($this->request->hasPost("qt"))
            $qt = $this->request->getPost("qt");
        if($this->request->hasPost("orderId")){
            $order_id = $this->request->getPost("orderId");
            if($qt==1)
                $order = JTQuotation::findFirst(array(
                    'conditions'=>array('_id'=>new \MongoId($order_id))
                ));
            else
                $order = JTOrder::findFirst(array(
                    'conditions'=>array('_id'=>new \MongoId($order_id))
                ));
            $order->deleted = true;
            $order->save();
            $cart = (new \RW\Cart\Cart)->get();
            if($cart['order_id']==$order_id){
                (new \RW\Cart\Cart)->destroy();
            }
            $this->view->disable();
        }
    }
    public function getContactAction(){
        $this->view->disable();
         $email = $this->request->hasPost("email")?$this->request->getPost("email"):'';
         $email = strtolower($email);
         $contacts = JTContact::find(array(array('email'=>array('$regex'=>$email),'is_customer'=>1)));
         $arrReturn = array();
         foreach ($contacts as $key => $value) {
             $arrReturn[] = $value->toArray();
         }
         return $this->response($arrReturn);
    }

    public function createContactAction(){
         $this->view->disable();
         $arrReturn = array();
         $email = $this->request->hasPost("email")?$this->request->getPost("email"):'';
         $fullname = $this->request->hasPost("name")?$this->request->getPost("name"):'';
         $arr_name = explode(' ', $fullname);
         $first_name = '';
         $last_name = '';
         foreach ($arr_name as $key => $value) {
            if($key==0){
                $first_name = $value;
            }else{
                $last_name .= $value.' ';
            }
         }

         $contact = JTContact::findFirst(array(array('email'=>$email)));
         if($contact){
            $arrReturn['has_created'] = 1;
            $arrReturn['email'] = $contact->email;
            $arrReturn['fullname'] = $contact->fullname;
         }else{
            $contact = new JTContact();
            $contact->email = $email;
            $contact->fullname = $fullname;
            $contact->first_name = $first_name;
            $contact->last_name = $last_name;
            try {
                $contact->save();
                $arrReturn['has_created'] = 0;
                $arrReturn['email'] = $contact->email;
                $arrReturn['fullname'] = $contact->fullname;
            } catch (\RW\Cart\Exception $e) {
            }
         }
         return $this->response($arrReturn);
    }
    public function accountOrderAction(){
        $order_list = array();
        $orderList = JTOrder::find(array(
            'conditions'=>array('paid_by'=>'On Account','deleted'=>false),
            'sort'=> array("code" => -1)
        ));
        // $orderList = $orderList->toArray();
        foreach ($orderList as $key => $value) {
            $value->order_id =  (string)$value->_id;
            $value->time = date("d-m-Y H:i:s",$value->date_modified->sec);
            $order_list[] = $value->toArray();
        }

        // $this->view->disable();
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        // pr($items);die;
        $this->view->partial('pos/blocks/account_order');  
    }
    public function adjustmentOrderAction(){
        $order_list = array();
        $orderList = JTOrder::find(array(
            'conditions'=>array('paid_by'=>'On Account','deleted'=>false),
            'sort'=> array("code" => -1)
        ));
        foreach ($orderList as $key => $value) {
            $value->order_id =  (string)$value->_id;
            $value->time = date("d-m-Y H:i:s",$value->date_modified->sec);
            $order_list[] = $value->toArray();
        }
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        $this->view->partial('pos/blocks/adjustment_order');  
    }
    public function dailyHistoryAction(){
        $order_list = array();
        $nowtime = new \MongoDate(strtotime(date('Y-m-d 00:00:00')));
        
        $conditions = array();
        if($this->request->hasPost("codeOrder")){
            $conditions['code'] = array('$regex' => $this->request->getPost("codeOrder"));
        }
        $conditions['status'] = "Completed";
        $conditions['date_modified'] = array('$gte'  => new \MongoDate(strtotime(date('Y-m-d 00:00:00'))),'$lte'  => new \MongoDate(strtotime(date('Y-m-d 23:59:59'))));
        // $conditions['completed'] = 1;
        $conditions['$or'] = array(
                                array(
                                    'create_from' => 'Create from POS',
                                ),
                                array(                                              
                                    'heading' => 'Create from POS',
                                )
                            );
        $conditions['deleted'] = false;
        $orderList = JTOrder::find(array(
            'conditions'=>$conditions,
            'sort'=> array("code" => -1)
        ));
        foreach ($orderList as $key => $value) {
            $value->order_id =  (string)$value->_id;
            $value->time = date("d-m-Y H:i:s",$value->date_modified->sec);
            $order_list[] = $value->toArray();
        }
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        $this->view->partial('pos/blocks/daily_history');  
    }
    public function payAccountOrderAction(){
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $order_id = $this->request->getPost('order_id');
        $cash_tend = $this->request->getPost('cash_tend');
        // $order = new JTOrder();
        $order = JTOrder::findFirst(array(
            'conditions'=>array('_id'=>new \MongoId($order_id))
        ));
        $order->cash_tend = $cash_tend;
        $order->paid_by = "Had paid";
        $order->status = "Completed";
        $order->status_id = "Completed";
        $order->asset_status = "Completed";
        $order->save();
    }
    public function editAdjustmentOrderAction(){
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $order_id = $this->request->hasPost("orderId")?$this->request->getPost("orderId"):'';
        if($order_id=='')
            return false;
        $orderList = JTOrder::findFirst(array(
            'conditions'=>array('_id'=>new \MongoId($order_id))
        ));
        $orderList = $orderList->toArray();
        if(isset($orderList['cart'])){
            $orderList['cart']['order_id'] = $order_id;
            (new \RW\Cart\Cart)->setCart($orderList['cart']);
        }
        (new \RW\Cart\Cart)->setOrderType(1);

        $arr_pro =array();
        $items = (new \RW\Cart\Cart)->get();
        foreach ($items['items'] as $key => $value) {
            if(isset($value['options']))
                foreach ($value['options'] as $kk => $op) {
                    if($op['option_type']!='')
                    $arr_pro[$op['_id']] = $op['option_type'];
                }
        }
        // pr($items);die;
        $this->view->disable();
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->taxlist = $this->taxlist();
        $this->view->cart = $items;
        $this->view->baseURL =URL;
        $this->view->partial('pos/blocks/view_cart');
    }
    public function viewOrderHistoryDetailAction(){
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $order_id = $this->request->hasPost("orderId")?$this->request->getPost("orderId"):'';
        if($order_id=='')
            return false;
        $orderList = JTOrder::findFirst(array(
            'conditions'=>array('_id'=>new \MongoId($order_id))
        ));
        $orderList = $orderList->toArray();
        if(isset($orderList['cart'])){
            $orderList['cart']['order_id'] = $order_id;
        }
        $arr_pro = array();
        foreach ($orderList['cart']['items'] as $key => $value) {
            if(isset($value['options']))
                foreach ($value['options'] as $kk => $op) {
                    if($op['option_type']!='')
                    $arr_pro[$op['_id']] = $op['option_type'];
                }
        }
        // pr($arr_pro);die;
        $this->view->disable();
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->taxlist = $this->taxlist();
        $this->view->cart = $orderList['cart'];
        $this->view->baseURL = URL;
        $this->view->partial('pos/blocks/order_detail');
    }
    
}
