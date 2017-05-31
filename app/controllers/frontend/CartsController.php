<?php
namespace RW\Controllers;

use RW\Models\Carts;
use RW\Models\Banners;
use RW\Models\JTProduct;
use RW\Models\JTCompany;
use RW\Models\JTProvince;
use RW\Cart\Cart;
use RW\Models\Vouchers;
use RW\Models\JTOrder;
class CartsController extends ControllerBase
{
    private $cart;

    public function initialize()
    {
        parent::initialize();
        $this->cart = new Cart;
    }

    public function indexAction()
    {
        $this->assets
                ->collection('css')
                ->addCss('/bower_components/bootstrap/dist/css/bootstrap.min.css')
                ->addCss('/bower_components/font-awesome/css/font-awesome.css')
                ->addCss('/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')
                ->addCss('/'.THEME.'css/font.css')
                ->addCss('/'.THEME.'css/main.css')
                ->addCss('/'.THEME.'css/custom.css')
                ->setSourcePath(PUBLIC_PATH)
                ->setTargetPath(PUBLIC_PATH.DS.THEME.'/css/app.min.css')
                ->setTargetUri('/'.THEME.'css/app.min.css')
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Cssmin());
        $this->assets
                ->collection('js')
                ->addJs('/bower_components/jquery/dist/jquery.min.js')
                ->addJs('/bower_components/bootstrap/dist/js/bootstrap.min.js')
                ->addJs('/bower_components/iscroll/build/iscroll.js')
                ->addJs('/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')
                ->addJs('/'.THEME.'js/load.js')
                ->addJs('/'.THEME.'js/function.js')
                ->addJs('/'.THEME.'js/pricing.js')
                ->addJs('/'.THEME.'js/common.js')
                ->addJs('/'.THEME.'js/facebook-sdk.js')
                ->addJs('/'.THEME.'js/facebook_login.js')
                ->setSourcePath(PUBLIC_PATH)
                ->setTargetPath(PUBLIC_PATH.DS.THEME.'/js/app.min.js')
                ->setTargetUri('/'.THEME.'js/app.min.js')
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
        $this->assets->collection('pageJS');
        $this->view->baseURL = URL;
        $this->view->session_user = $this->session->has('user') ? $this->session->get('user') : false;
        $this->view->cart = $this->cart->get();
        $this->view->provinces = JTProvince::find(array(
                                                array("country_id" => "CA"),
                                                "sort" => array("name" => 1)
                                            ));       
        
        $this->view->session_user = $this->session->has('user')?$this->session->get('user'):false;
        $this->view->partial('frontend/Carts/index');
        $this->view->setViewsDir($this->view->getViewsDir() . '/frontend/');
    }

    public function addCartAction()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Not enough infomation to process the calculation.'];
        $main = $this->request->getPost('main');
        if( $main ){
            $options = $this->request->getPost('options');
            $options = JTProduct::FilterOptions($options,new \MongoId($main['_id']));
            $data = JTProduct::getPrice([
                                            '_id'   => $main['_id'],
                                            'sizew' => 0,
                                            'sizeh' => 0,
                                            'quantity'  => $main['quantity'],
                                            'note'      => $main['note'],
                                            'companyId' => '',
                                            'options'   => $options,
                                            'fields'    => ['name', 'description','taxper','category']
                                        ]);
            $cartKey = $this->cart->add([
                            '_id'       => new \MongoId($main['_id']),
                            'name'      => $data['name'],
                            'description'=> $data['description'],
                            'category'  => $data['category'],
                            'note'      => $main['note'],
                            'image'     => $main['image'],
                            'quantity'  => $main['quantity'],
                            'options'   => $options,
                            'sell_price' => $data['sell_price'],
                            'taxper'    => $data['taxper'],
                            'total'     => $data['sub_total']
                        ]);
            $item = $this->cart->get($cartKey);
            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                            'error' => 0,
                            'cart' => [
                                'quantity' => $this->cart->getQuantity(),
                                'total' => number_format($total['total'],2),
                                'taxper' => number_format($total['taxper'],2),
                                'tax' => number_format($total['tax'],2),
                                'main_total' => number_format($total['main_total'],2)
                            ],
                            'product' => [
                                'cartKey'       =>  $cartKey,
                                'name'          => $data['name'],
                                'description'   => $data['description'],
                                'note'          => $data['note'],
                                'image'         => $main['image'],
                                'sell_price'    => number_format($data['sell_price'], 2),
                                'quantity'      => $item['quantity']
                            ],
                            'combo_step' => (new \RW\Cart\Cart)->combostep(),
                            'is_use_group' => (new \RW\Cart\Cart)->checkgroup()
                        ];
           // $cart_now = $this->cart->get();
           // $arr_cart= json_decode(file_get_contents("datacart.json"),true);
           // $arr_cart[session_id()] = $cart_now;
           // file_put_contents("datacart.json", json_encode($arr_cart));  
        }
        return $this->response($arrReturn);
    }
    public function updateCartAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Not enough infomation to process the calculation.'];
        $main = $this->request->getPost('main');
        $cart_id = $this->request->getPost('cart_id');
        if( $main ){
            $options = $this->request->getPost('options');
            $options = JTProduct::FilterOptions($options,new \MongoId($main['_id']));
            $data = JTProduct::getPrice([
                                            '_id'   => $main['_id'],
                                            'sizew' => 0,
                                            'sizeh' => 0,
                                            'quantity'  => $main['quantity'],
                                            'note'      => $main['note'],
                                            'companyId' => '',
                                            'options'   => $options,
                                            'fields'    => ['name', 'description']
                                        ]);
            $update_cart = $this->cart->update($cart_id,[
                            '_id'       => new \MongoId($main['_id']),
                            'name'      => $data['name'],
                            'description'=> $data['description'],
                            'note'      => $main['note'],
                            'image'     => $main['image'],
                            'quantity'  => $main['quantity'],
                            'options'   => $options,
                            'sell_price' => $data['sell_price'],
                            'total'     => $data['sub_total'],
                        ]);
            $item = $this->cart->get($cart_id);
            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                            'error' => 0,
                            'cart' => [
                                'quantity' => $this->cart->getQuantity(),
                                'total' => number_format($total['total'],2),
                                'taxper' => number_format($total['taxper'],2),
                                'tax' => number_format($total['tax'],2),
                                'main_total' => number_format($total['main_total'],2)
                            ],
                            'product' => [
                                'cartKey'       =>  $cart_id,
                                'name'          => $data['name'],
                                'description'   => $data['description'],
                                'note'          => $data['note'],
                                'image'         => $main['image'],
                                'sell_price'    => number_format($data['sell_price'], 2),
                                'quantity'      => $item['quantity']
                            ]
                        ];
            // $cart_now = $this->cart->get();
           // $arr_cart= json_decode(file_get_contents("datacart.json"),true);
           // $arr_cart[session_id()] = $cart_now;
           // file_put_contents("datacart.json", json_encode($arr_cart));
        }
        return $this->response($arrReturn);
    }
    public function removeItemAction()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $this->cart->remove($this->request->getPost('cartKey'));
            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                            'error' => 0,
                            'cart' => [
                                'quantity' => $this->cart->getQuantity(),
                                'total' => number_format($total['total'],2),
                                'taxper' => number_format($total['taxper'],2),
                                'tax' => number_format($total['tax'],2),
                                'main_total' => number_format($total['main_total'],2)
                            ],
                        ];
            // $cart_now = $this->cart->get();
           // $arr_cart= json_decode(file_get_contents("datacart.json"),true);
           // $arr_cart[session_id()] = $cart_now;
           // file_put_contents("datacart.json", json_encode($arr_cart));
        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }

    public function updateQuantityAction()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $cartKey = $this->request->getPost('cartKey');
            $item = $this->cart->get($cartKey);
            $item['quantity'] = $this->request->getPost('quantity');
            $data = JTProduct::getPrice([
                                            '_id'   => $item['_id'],
                                            'sizew' => 0,
                                            'sizeh' => 0,
                                            'quantity'  => $item['quantity'],
                                            'companyId' => '',
                                            'options'   => $item['options'],
                                        ]);
            $item['sell_price'] = $data['sell_price'];
            $item['total'] = $data['sub_total'];
            $this->cart->update($cartKey, $item);
            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                            'error' => 0,
                            'cart' => [
                                'quantity' => $this->cart->getQuantity(),
                                'total' => number_format($total['total'],2),
                                'taxper' => number_format($total['taxper'],2),
                                'tax' => number_format($total['tax'],2),
                                'main_total' => number_format($total['main_total'],2)
                            ],
                            'product' => [
                                'total' => number_format($item['total'], 2)
                            ]
                        ];
            $cart_now = $this->cart->get();
            if(isset($item['user_id']))
            {
                $arrReturn['product']['group_total'] = $cart_now['group_order']['list'][$item['user_id']]['total'];
                $arrReturn['product']['user_id'] = $item['user_id'];
            }
            
           // $arr_cart= json_decode(file_get_contents("datacart.json"),true);
           // $arr_cart[session_id()] = $cart_now;
           // file_put_contents("datacart.json", json_encode($arr_cart));
        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }

    public function updateNoteAction()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $this->cart->updateNote($this->request->getPost('note'));
        return $this->response(['error' => 0]);
    }

    public function updateItemAction()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $cartKey = $this->request->getPost('cartKey');
            $item = $this->cart->get($cartKey);
            $item['options'] = $this->request->getPost('options');
            $data = JTProduct::getPrice([
                                            '_id'   => $item['_id'],
                                            'sizew' => 0,
                                            'sizeh' => 0,
                                            'quantity'  => $item['quantity'],
                                            'companyId' => '',
                                            'options'   => $item['options'],
                                        ]);
            $item['sell_price'] = $data['sell_price'];
            $item['total'] = $data['sub_total'];
            $this->cart->update($cartKey, $item);
            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                            'error' => 0,
                            'cart' => [
                                'quantity' => $this->cart->getQuantity(),
                                'total' => number_format($total['total'],2),
                                'taxper' => number_format($total['taxper'],2),
                                'tax' => number_format($total['tax'],2),
                                'main_total' => number_format($total['main_total'],2)
                            ],
                            'product' => [
                                'total' => number_format($item['total'], 2)
                            ]
                        ];
        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }

    public function dropCart()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $this->cart->destroy();
        return $this->response(['error' => 0]);
    }


    public function viewCartAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
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

        // pr($items);exit;
        $this->view->disable();
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->combo_list = $this->cart->combo_list();
        $this->view->user_list = $this->cart->user_group_list();
        $this->view->user_data = $this->cart->user_data_list();
        $this->view->taxlist = $this->taxlist();
        // pr($items);die;
        $this->view->partial('frontend/blocks/view_cart', array('cart' => $items, 'baseURL' => URL));
    }
    public function smallCartAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $this->view->disable();
        $this->view->cart = (new \RW\Cart\Cart)->get();
        $this->view->baseURL = URL;
        $this->view->partial('frontend/blocks/small_cart');
    }

    function findLocationAction(){
        $this->view->disable();
        if($this->request->isAjax()){
            $arr_return = array(
                    'status' => 'error',
                    'message' => ''
                );
            $check_find = false;
            $postal_code = $this->request->hasPost('postal_code')?$this->request->getPost('postal_code'):'';
            $address = $this->request->hasPost('address')?$this->request->getPost('address'):'';

            $link_get_postal_code = 'http://geocoder.ca/?postal='.$postal_code.'&json=1&exact=1&showaddrs=1&topmatches=1';
            $link_get_address = 'http://geocoder.ca/?locate='.$address.'&json=1&exact=1&showaddrs=1&topmatches=1';

            if($postal_code != ''){
                $result_get_postal_code = json_decode(file_get_contents($link_get_postal_code));
                if(isset($result_get_postal_code->longt) && $result_get_postal_code->latt){
                    $longt = deg2rad($result_get_postal_code->longt);
                    $latt = deg2rad($result_get_postal_code->latt);
                    $check_find = true;
                }else{
                    $arr_return['message'] = 'We can not find your postal code';
                }
            }else{
                if($address!= ''){
                    $result_get_address = json_decode(file_get_contents($link_get_address));
                    if(isset($result_get_address->longt) && $result_get_address->latt){
                        $longt = deg2rad($result_get_address->longt);
                        $latt = deg2rad($result_get_address->latt);
                        $check_find = true;
                    }else{
                        $arr_return['message'] = 'We can not find your address';
                    }
                }else{
                    $arr_return['message'] = 'Please input postal code or address information';
                }
            }
            if($check_find){
                $company = JTCompany::findFirst(
                                            array(array(
                                                    'name' => 'Retail'
                                                )
                                            )
                                        )->toArray();
                $arr_compare = array();
                foreach ($company['addresses'] as $key => $address) {
                    $address_company = rawurlencode($address['address_1'].','.$address['town_city'].','.$address['province_state']);
                    $link_get_address_company = 'http://geocoder.ca/?locate='.$address_company.'&json=1&exact=1&showaddrs=1&topmatches=1';
                    $result_get_address_company = json_decode(file_get_contents($link_get_address_company));
                    if(!isset($result_get_address_company->error)){
                        $longt_company = deg2rad($result_get_address_company->longt);
                        $latt_company = deg2rad($result_get_address_company->latt);
                        $R = 6371000;
                        $x = ($longt_company - $longt)*cos(($latt_company + $latt) / 2);
                        $y = ($latt_company - $latt);
                        $d = sqrt($x*$x + $y*$y)*$R;
                        if($d<25000){
                            $arr_compare[$key] = $d;
                        }
                    }
                }
                $min = min($arr_compare);
                foreach ($arr_compare as $key => $value) {
                    if($min==$value){
                        $arr_return['address'] = $company['addresses'][$key];
                        $arr_return['status'] = 'success';
                    }
                }
                if($arr_return['status']=='success'){
                    $this->session->set('location',$arr_return['address']);
                }
            }
            echo json_encode($arr_return);
        }else{
            return $this->response->redirect('/');
        }
    }

    public function getOptionItemAction(){
        $this->view->disable();
        $productController = new \RW\Controllers\ProductsController();
        $cart = $this->cart->get($this->request->getPost('cartKey'));
        $arr_option =  $productController->optionAction((string)$cart['_id'],1);
        
        $this->view->arr_group = $arr_option['arr_group'];
        $this->view->option = $arr_option['option'];
        $this->view->option_price_total = $arr_option['option_price_total'];
        $this->view->baseURL = URL;
        $this->view->cart_id = $arr_option['cart_id'];
        $this->view->finish_option = $arr_option['finish_option'];
        $this->view->partial('frontend/Categories/option_product');
    }


    public function changeTaxAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $taxper = $this->request->getPost('taxper');
            $this->cart->updateTax($taxper);
            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                    'error' => 0,
                    'cart' => [
                        'quantity' => $this->cart->getQuantity(),
                        'total' => number_format($total['total'],2),
                        'taxper' => number_format($total['taxper'],2),
                        'tax' => number_format($total['tax'],2),
                        'main_total' => number_format($total['main_total'],2)
                    ]
                ];
        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }

    public function printAction($order_id=''){
        if($order_id!=''){
            $orderList = JTOrder::findFirst(array(
                'conditions'=>array('_id'=>new \MongoId($order_id)),
                'sort'   => ['_id' => -1] 
            ))->toArray();
        }
        $user = $this->session->has('user') ? $this->session->get('user') : false;
        $arr_pro =array();
        if(isset($orderList) && $order_id!='')
            $items = $orderList['cart'];
        else
            $items = (new \RW\Cart\Cart)->get();

        $this->view->payment_method = '';
        foreach ($items['payment_method'] as $key => $value) {
            $this->view->payment_method .= $key.", ";
        }
        $this->view->amount_tendered = $items['amount_tendered'];
        $this->view->main_total = $items['main_total'];
        $this->view->change_due = $items['amount_tendered'] - $items['main_total'];
        $this->view->tax_no = $items['tax_no'];
        $order_number = isset($orderList['code'])?$orderList['code']:$items['last_your_order_code'];
        $this->view->order_id = substr($order_number, -2);
        $this->view->discount_total = isset($items['discount_total'])?$items['discount_total']:0;
        foreach ($items['items'] as $key => $value) {
            if(isset($value['options']))
                foreach ($value['options'] as $kk => $op) {
                    if($op['option_type']!='')
                    $arr_pro[$op['_id']] = $op['option_type'];
                }
        }
        $this->view->disable();
        $pos_num = 0;
        if(file_exists("cookies_id.json")){
            $arr_cookies_id = json_decode(file_get_contents("cookies_id.json"),true);
            $cookies_order_pos = trim($this->cookies->get('cookies_order_pos')->getValue());
            if(count($arr_cookies_id)>0)
            foreach ($arr_cookies_id as $key => $value) {
                if(trim((string)$value)==$cookies_order_pos){
                    $pos_num = $key;
                    break;
                }
            }
        }
        $this->view->session_order = $pos_num;
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->taxlist = $this->taxlist();            
        $this->view->cart = $items;

        if($order_id!=''){
            $user_group_list = $user_data_list= array();
            if(isset($items['group_order']['list'])){
                $user_data_list = $items['group_order']['list'];
                foreach ($items['group_order']['list'] as $key => $value) {
                    $user_group_list[$value['user_id']] = $value['user_name'];
                }
            }
            $this->view->user_list = $user_group_list;
            $this->view->user_data = $user_data_list;
            if(isset($items['combo_list']))
                $this->view->combo_list = $items['combo_list'];
        }else{
            $this->view->user_list = $this->cart->user_group_list();
            $this->view->user_data = $this->cart->user_data_list();
            $this->view->combo_list = (new \RW\Cart\Cart)->combo_list(); 
        }
        
        $this->view->baseURL = URL;
        $this->view->user = $user['full_name'];
        $this->view->time = $this->getDatetimeFormated();

        if(isset($items['payment_method']['On Account']) && (float)$items['payment_method']['On Account']>0){
            if(isset($orderList) && $order_id!='')
                $this->view->orderList =  $orderList;  
            $this->view->partial('frontend/blocks/print_on_account');
        }
        else
            $this->view->partial('frontend/blocks/print');
    }

    public function paymentCalculationAction(){

        $credit =array();
        $items = $this->cart->get();
        foreach ($items['payment_method'] as $key => $value) {
            if($key=='Visa Cart'){
                $credit['type'] = 'Visa Cart';
                $credit['value'] = $value;
            }
            if($key=='Master Card'){
                $credit['type'] = 'Master Card';
                $credit['value'] = $value;
            }
        }
        $this->view->disable();
        $this->view->taxlist = $this->taxlist();
        $this->view->cart = $items;
        // pr($items);die;
        $this->view->credit = $credit;
        $this->view->baseURL = URL;
        $this->view->time = date('D M d, Y g:i A');
        $this->view->datetimepicker = date("m/d/Y h:i:s A",strtotime($this->getDatetimeFormated())+15*60);
        $this->view->partial('frontend/blocks/payment_calculation');
    }    

    public function clearCartAction()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $save_order =  $this->request->getPost('save_order');
        if($save_order == 'yes'){
            $ordersController = new \RW\Controllers\OrdersController();
            $ordersController->is_process='call_from_edit';
            $ordersController->createOrderAction();
            $ordersController->is_process='';
        }
        $this->cart->destroy();
        return $this->response(['error' => 0]);
    }
    public function idledisplayAction(){
        $arr_return = ['error'=>1,'image'=>''];
        if ($this->request->isAjax()) {
            $run = $this->request->hasPost('data_total')?$this->request->getPost('data_total'):0;
            settype($run,'int');            
            if($run<=0){
                $image = '';
                $arr_extra = (new \RW\Models\Banners)->getListBannersByType(4) ;
                $items = (new \RW\Cart\Cart)->get();
                if($items['total']==0){
                   if(!empty($arr_extra)){
                        $arr_return = ['error'=>0,'image'=>URL.'/'.rand_key ($arr_extra)];
                    }   
                }else{
                    $arr_return = ['error'=>1];
                }
                 
            }
        }        
        return $this->response($arr_return);
    }
    public function customerDisplayAction(){
        $arr_pro =array();
        $cycle = 4;
        $items = (new \RW\Cart\Cart)->get();
        // pr($items);die;
        foreach ($items['items'] as $key => $value) {
            if(isset($value['options']))
                foreach ($value['options'] as $kk => $op) {
                    if($op['option_type']!='')
                    $arr_pro[$op['_id']] = $op['option_type'];
                }
        }
        $count = $this->request->hasPost('count')?(int)$this->request->getPost('count'):0;
        $this->view->disable();
        if(isset($items['finalize']) && $items['finalize']=='done'){
            echo 'finalize_'.$items['last_your_order_code'];die();
        }
        if($items['total']==0 && $count>=$cycle){
            echo 'change_banner';die();
        }else{
            $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
            $this->view->taxlist = $this->taxlist();
            $this->view->cart = $items;
            $this->view->combo_list = (new \RW\Cart\Cart)->combo_list();
            $this->view->baseURL = URL;
            $this->view->partial('frontend/blocks/customer_display');
        }
        
    }
    public function customerDisplayViewAction(){
        $arr_pro =array();
        $cycle = 4;
        $order_cookies = $this->request->hasPost('id')?$this->request->getPost('id'):0;
        $arr_cookies= json_decode(file_get_contents("cookies_id.json"),true);
        $arr_cart= json_decode(file_get_contents("datacart.json"),true);
        //khi order completed and deploy cart, data finalize will save in to the finalize.json file
        $arr_finalize= json_decode(file_get_contents("finalize.json"),true);
        $items = ['items'=>[]];
        if($order_cookies){
            $cookies_id = isset($arr_cookies[$order_cookies])?$arr_cookies[$order_cookies]:1;
            $items = isset($arr_cart[$cookies_id])?$arr_cart[$cookies_id]:array();
            $finalize = isset($arr_finalize[$cookies_id])?$arr_finalize[$cookies_id]:array();
        }
        if(!isset($items['items']))
            $items['items'] =array();

        foreach ($items['items'] as $key => $value) {
            if(isset($value['options']))
                foreach ($value['options'] as $kk => $op) {
                    if($op['option_type']!='')
                    $arr_pro[$op['_id']] = $op['option_type'];
                }
        }
        $count = $this->request->hasPost('count')?(int)$this->request->getPost('count'):0;
        $this->view->disable();
        if(!isset($items['last_your_order_code']))
            $items['last_your_order_code'] = '';
        if(isset($finalize['finalize']) && $finalize['finalize']=='done'){
            echo 'finalize_'.$items['last_your_order_code'];
            die();
        }
        if(!isset($items['total']))
                $items['total'] = 0;
        if($items['total']==0 && $count>=$cycle){
            echo 'change_banner';die();
        }else{
            $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
            $this->view->taxlist = $this->taxlist();
            $this->view->cart = $items;
        
            $this->view->combo_list = isset($items['combo_list'])?$items['combo_list']:array();
            $this->view->baseURL = URL;
            $this->view->partial('frontend/blocks/customer_display');
        }
    }
    public function beginComboAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $product_id = $this->request->getPost('product_id');
        $combo_sales = $this->request->getPost('combo_sales');
        $items = (new \RW\Cart\Cart)->setcombo(1,$product_id,$combo_sales);
        $this->view->disable();
        die;
    }
    public function stopComboAction(){
        $items = (new \RW\Cart\Cart)->setcombo(0);
        $this->view->disable();
        die;
    }
    public function cancelComboAction(){
        $items = (new \RW\Cart\Cart)->cancelcombo();
        $this->view->disable();
        die;
    }
    public function removeComboAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $this->cart->cancelcombo($this->request->getPost('combo_id'));
            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                            'error' => 0,
                            'cart' => [
                                'quantity' => $this->cart->getQuantity(),
                                'total' => number_format($total['total'],2),
                                'taxper' => number_format($total['taxper'],2),
                                'tax' => number_format($total['tax'],2),
                                'main_total' => number_format($total['main_total'],2)
                            ],
                        ];
           
        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }
    public function changeComboAction(){
        if (!$this->request->isAjax()){
            return $this->error404();
        }
         $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $cartKey = $this->request->getPost('cartKey');
            $this->cart->returnstep($cartKey);
            $arrReturn = ['error' => 0];
        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }
    public function addqtyComboAction(){
        if (!$this->request->isAjax()){
            return $this->error404();
        }
         $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $cartKey = $this->request->getPost('cartKey');
            $comboqty = $this->request->getPost('comboqty');
            $this->cart->changeqtyCombo($cartKey,$comboqty);
            $arrReturn = ['error' => 0];
        } catch (\RW\Cart\Exception $e){
        }
        return $this->response($arrReturn);
    }
    public function beginGroupAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $product_id = $this->request->getPost('product_id');
        $username = $this->request->getPost('username');
        $items = $this->cart->setUseGroup(1,$product_id,$username);
        $this->view->disable();
        $arrReturn = array('next_uid'=>$this->cart->next_uid());
        return $this->response($arrReturn);
        die;
    }
    public function nextGroupAction(){
        $items = $this->cart->setUseGroup(0);
        $this->view->disable();
        $arrReturn = array('next_uid'=>$this->cart->next_uid());
        return $this->response($arrReturn);
        die;
    }
    public function endGroupAction(){
        $items = $this->cart->setUseGroup(0);
        $this->view->disable();
        die;
    }

    public function removeUserGroupAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $this->cart->cancelUserGroup($this->request->getPost('user_id'));

            $total = $this->cart->getDetailTotal();
            $arrReturn = [
                            'error' => 0,
                            'cart' => [
                                'quantity' => $this->cart->getQuantity(),
                                'total' => number_format($total['total'],2),
                                'taxper' => number_format($total['taxper'],2),
                                'tax' => number_format($total['tax'],2),
                                'main_total' => number_format($total['main_total'],2)
                            ],
                        ];
           
        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }

    public function changeUserGroupAction(){
        if (!$this->request->isAjax()){
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Please refresh and try again.'];
        try {
            $cartKey = $this->request->getPost('cartKey');
            $user_id = $this->request->getPost('user_id');
            
            $data = $this->cart->next_uid($user_id);

            $this->cart->returnUserGroup($cartKey, $data);
            $this->view->disable();
            
            if(is_array($data))
                $arrReturn = array('error' => 0, 'next_uid'=>$data['next_uid'], 'user_name'=>$data['user_name']);

        } catch (\RW\Cart\Exception $e) {
        }
        return $this->response($arrReturn);
    }    

    public function updateSepayAction(){
        $payment_method = $this->request->getPost('Paidby');
        $amount_tendered = $this->request->getPost('total');
        $items = $this->cart->update_payment_mt($payment_method,$amount_tendered);

        $arrReturn = array('status'=>'good');
        $this->view->disable();
        return $this->response($arrReturn);
        die;
    }
    public function offFinalizeAction(){
        $order_cookies = $this->request->hasPost('id')?$this->request->getPost('id'):0;
        if($order_cookies){ 
            $arr_cookies= json_decode(file_get_contents("cookies_id.json"),true);
            $cookies_id = isset($arr_cookies[$order_cookies])?$arr_cookies[$order_cookies]:1;
            $finalize= json_decode(file_get_contents("finalize.json"),true);
            $new_finalize['finalize'] = '';
            $new_finalize['last_your_order_code'] = '';
            $finalize[$cookies_id] = $new_finalize;
            file_put_contents("finalize.json", json_encode($finalize));
            $arrReturn = array('order_code'=>$new_finalize['last_your_order_code']);
        }else{ //cung 1 session
            $this->cart->update_last_code('off_finalize');
            $arrReturn = array('order_code'=>$this->cart->getDataField('last_your_order_code'));
        }
        $this->view->disable();
        return $this->response($arrReturn);
        die;
    }

    public function applyCouponAction(){
        $this->view->disable();
        $message = ''; $voucher_value = 0;
        $coupon = $this->request->hasPost('coupon')?$this->request->getPost('coupon'):'';
        $arr_return = array('error'=>1,'message'=>'');
        if($coupon == ''){
            $arr_return['message'] = 'Coupon is empty';
        }else{
        	  // $coupon = strtoupper($coupon);
        	  $voucher = Vouchers::findFirst(['conditions' =>["name"=>trim($coupon)]]);
        	  if(!$voucher){
        	  	$arr_return['message'] = 'Not found voucher for this coupon';
        	  }else{
        	  	$voucher = returnArray($voucher);//$voucher->toArray();
                if($voucher['limited']==2){
                    $heck2 = JTOrder::findFirst(['conditions' =>["voucher"=>trim($coupon)]]);
                    if($heck2){
                        $arr_return = array('error'=>1,'message'=>'Voucher expired');
                        return $this->response($arr_return);
                    }
                }

                //voucher ap dung cho 1 category
                if(isset($voucher['category']) && $voucher['category']!=""){
                    $items = (new \RW\Cart\Cart)->get();
                    // pr($items);exit;
                    foreach ($items['items'] as $product) {
                        if($product['category'] != $voucher['category']){
                            $arr_return['message'] = 'This code is only used for category '.$voucher['category'].', Thank you.';
                            return $this->response($arr_return);
                        }
                    }
                }
                

                if($voucher['name']=='bms10' && $this->cart->getTotal() < 50){
                    $arr_return['message'] = 'bms10 code is only for any minimum order of $50cad. Please shop more and get the discount. Thank you!';
                    return $this->response($arr_return);
                }


                $arr_voucher = $this->cart->update_vouchers($voucher);
                $message = 'Discount ('.$voucher['value'].$voucher['type'].')';
                $arr_return = array('error'=>0,'message'=>$message,'voucher_value'=>number_format((double)$arr_voucher['value'],2),'new_main_total'=>$arr_voucher['voucher_main_total']);
        	  }
        }
        return $this->response($arr_return);
    }
    public function removeCouponAction(){
        $arr_return = array('error'=>1,'message'=>'');
        $data = $this->cart->remove_vouchers();
        $arr_return['new_main_total'] = $data['new_main_total'];
        $this->view->disable();
        return $this->response($arr_return);
    }
    public function applyPromoAction(){
        $this->view->disable();
        $message = ''; $promo_value = 0;
        $promo = $this->request->hasPost('promo')?$this->request->getPost('promo'):'';
        $arr_return = array('error'=>1,'message'=>'');
        if($promo == ''){
            $arr_return['message'] = 'Promo code is empty';
        }else{
              // $coupon = strtoupper($coupon);
              $promo_voucher = Vouchers::findFirst(['conditions' =>["name"=>trim($promo),"product_type"=>"promo"]]);
              if(!$promo_voucher){
                $arr_return['message'] = 'Not found voucher for this coupon';
              }else{
                $promo_voucher = returnArray($promo_voucher);//$promo_voucher->toArray();
                // if($promo_voucher['limited']==2){
                //     $heck2 = JTOrder::findFirst(['conditions' =>["voucher"=>trim($promo)]]);
                //     if($heck2){
                //         $arr_return = array('error'=>1,'message'=>'Voucher expired');
                //         return $this->response($arr_return);
                //     }
                // }
                $arr_promo = $this->cart->update_promo($promo_voucher);
                $message = 'Total promo ';
                $arr_return = array('error'=>0,'message'=>$message,'promo_value'=>number_format((double)$arr_promo['value'],2),'new_main_total'=>$arr_promo['promo_main_total']);
              }
        }
        return $this->response($arr_return);
    }
    public function removePromoAction(){
        $arr_return = array('error'=>1,'message'=>'');
        $data = $this->cart->remove_promo();
        $arr_return['new_main_total'] = $data['new_main_total'];
        $this->view->disable();
        return $this->response($arr_return);
    }
    public function getCurrentTimeAction(){
        $this->view->disable();
        $arr_return = array('status'=>'ok','datetime'=>$this->getDatetimeFormated());
        return $this->response($arr_return);
        die;
    }

    public function addFreeItemsAction(){
        $arr_return = array('error'=>1,'message'=>'');
        $free_item_list = $this->request->hasPost('free_item_list')?$this->request->getPost('free_item_list'):array();
        
        $free_item_list = returnArray($free_item_list);
        $promo_code = $this->request->hasPost('promo_code')?$this->request->getPost('promo_code'):'';
        $promo_voucher = Vouchers::findFirst(['conditions' =>["name"=>trim($promo_code),"product_type"=>"promo"]]);
        $promo_voucher = returnArray($promo_voucher);
        $arr = $this->cart->add_free_item($free_item_list,$promo_voucher);
        $arr_return['arr'] = $arr;
        return $this->response($arr_return);
    }
}
