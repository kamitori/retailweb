<?php
namespace RW\Controllers;

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
        return $this->response->redirect(URL.'/carts');
    }
    public function savedOrdersAction(){
        $order_list = array();
        $items = (new \RW\Models\JTQuotation)->getPosList();
        foreach ($items as $key => $value) {
            $value->order_id =  (string)$value->_id;
            $value->code =  substr($value->code, -2);
            $value->time = date("d-m-Y H:i:s",$value->date_modified->sec);
            $order_list[] = $value->toArray();
        }
        // pr($order_list);die;
        $this->view->disable();
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        // pr($items);die;
        $this->view->partial('frontend/blocks/saved_orders');
    }
    public function createOrderAction(){
        $arr_return = array('error'=>1,'message'=>'','order_id'=>'');
        
        //cash_tend
        $cash_tend = $this->request->hasPost("cash_tend")?$this->request->getPost("cash_tend"):0;
        $cash_tend = floatval($cash_tend);

        //Paidby
        $arr_paidby = $this->request->hasPost("Paidby")?$this->request->getPost("Paidby"):array();
        if(is_string($arr_paidby)){
            $arr_paidby = array($this->request->getPost("Paidby"));
        }
        $arr_paidby = array_unique($arr_paidby);
        if(count($arr_paidby)==1)
            $Paidby = $arr_paidby[0];
        else if(count($arr_paidby)>1)
            $Paidby = 'Multipay';
        else
            $Paidby = '';
        $had_paid = 0;
        $had_paid_amount = 0;
        $pay_account_order = array();
        if(!empty($arr_paidby) && !in_array('On Account',$arr_paidby))
        {
            $had_paid = 1;
            $had_paid_amount = $cash_tend;
            $pay_account_order = $arr_paidby;
        //truong hop nhieu method trong do co On Account
        }else if(!empty($arr_paidby) && in_array('On Account',$arr_paidby) && count($arr_paidby)>1){
            $paid_by_mt = (new \RW\Cart\Cart)->get_paid_by_value();
            $had_paid_tmp = 0;
            foreach ($paid_by_mt as $key => $value) {
                if($key!='On Account')
                    $had_paid_tmp += (float)$value;
            }
            $on_account = isset($paid_by_mt['On Account'])?(float)$paid_by_mt['On Account']:0;
            $had_paid_amount = $had_paid_tmp;   
        }
        
        //time_delivery
        if($this->request->hasPost("time_delivery") && in_array('On Account',$arr_paidby)){
            $time_delivery = $this->request->getPost("time_delivery");
            $time_format = substr($time_delivery, 0, strrpos($time_delivery, ' '));
            $tt = substr($time_delivery, strrpos($time_delivery, ' '));
            if(trim($tt) == 'PM')
                $time_delivery = date('Y-m-d H:i:s', strtotime($time_format)+12*60*60); //+ 12 hours if PM
            else
                $time_delivery = date('Y-m-d H:i:s', strtotime($time_format));  

        }else{
            // $time_delivery =date('m/d/Y H:i A',(int)time()+3600);
            $current_date_time = $this->getDatetimeFormated();
            $time_delivery =date('Y-m-d H:i:s',(int)strtotime($current_date_time)+3600);
        }
        $time_delivery = new \MongoDate(strtotime($time_delivery));

        //Status, type
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

        //Email,name
        $emailInfo = $this->request->hasPost("emailInfo")?$this->request->getPost("emailInfo"):'';
        $nameInfo = $this->request->hasPost("nameInfo")?$this->request->getPost("nameInfo"):'';
        $phone = $this->request->hasPost("phone")?$this->request->getPost("phone"):'';
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
         //Addresses
        $provinceId = '';
        if ($province = $this->request->getPost('province')) {
            list($provinceId, $province) = explode('-', $province);
        }

        $contact = JTContact::findFirst(array(array('email'=>$emailInfo)));
        if(!$contact){
            $contact = new JTContact();
            $contact->email = $emailInfo;
            $contact->fullname = $nameInfo;
            $contact->first_name = $first_name;
            $contact->last_name = $last_name;
            $contact->phone = $phone;
            try {
                $contact->save();
            } catch (\RW\Cart\Exception $e) {
            }
        } else {
            //already existed
            if($nameInfo != '') {
                $contact->fullname = $nameInfo;
                $contact->first_name = $first_name;
                $contact->last_name = $last_name;
            }
            if($phone != '') {
                $contact->phone = $phone;
            }
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
        if($orderType==2){
            $Paidby = (new \RW\Cart\Cart)->get_paid_by();
            if(!in_array("On Account", $Paidby)){
                $Paidby[] = "On Account";
            }
            $Paidby = array_unique($Paidby);
        }
        $completed = 0;
        if($orderType==2 && $soStatus=='Completed'){
            $completed = 1;
        }
        $voucher = (new \RW\Cart\Cart)->get_voucher();

        //ADD ORDER
        if($contact){
            $oid = $somongo->add($contact, [
                'delivery_method' => $deliveryMethod,
                'invoice_address'  => $invoiceAddress,
                'shipping_address'  => $shippingAddress,
                'cash_tend' => $cash_tend,
                'paid_by' => $Paidby,
                'pos_delay'=>$pos_delay,
                'time_delivery' => $time_delivery,
                'datetime_pickup' => $time_delivery,
                'order_type'=>$orderType,
                'had_paid' => $had_paid,
                'had_paid_amount' => $had_paid_amount,
                'pay_account_order' => $pay_account_order,
                'status' => $soStatus,
                'status_id'=> $soStatus,
                'asset_status'=> $soStatus,
                'completed'=>$completed,
                'voucher'=>$voucher
            ]);            
        }else{
            $user = array('_id'=>new \MongoId('564694b0124dca8603f4d46f'));
            $oid = $somongo->add($user, [
                'delivery_method' => $deliveryMethod,
                'invoice_address'  => $invoiceAddress,
                'shipping_address'  => $shippingAddress,
                'paid_by' => $Paidby,
                'cash_tend' => $cash_tend, //payment
                'pos_delay'=>$pos_delay,
                'time_delivery' => $time_delivery,
                'datetime_pickup' => $time_delivery,
                'order_type'=>$orderType,
                'had_paid' => $had_paid,
                'had_paid_amount' => $had_paid_amount,
                'pay_account_order' => $pay_account_order,
                'status' => $soStatus,
                'status_id'=> $soStatus,
                'asset_status'=> $soStatus,
                'completed'=>$completed,
                'voucher'=>$voucher
            ]);
        }
        //after add
        if(isset($oid) && $oid!=''){
            $last_code = substr($oid, -2);
            (new \RW\Cart\Cart)->update_last_code($last_code);
            (new \RW\Cart\Cart)->destroy();
            $order_id = explode("%", $oid);
            $arr_return['order_id'] = $order_id[0];
        }
        
        //return
        if($this->is_process=='call_from_edit'){
            return $arr_return;
        }else if ($this->request->isAjax()){
           $this->view->disable();
           return $this->response($arr_return);
        }else{
            return $this->response->redirect(URL.'/orders');
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
        $this->view->combo_list = (new \RW\Cart\Cart)->combo_list();
        $this->view->taxlist = $this->taxlist();
        $this->view->cart = $items;
        $this->view->baseURL =URL;
        $this->view->partial('frontend/blocks/view_cart');
        
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
            // $order->deleted = true;
            $order->status = 'Cancelled';
            $order->status_id = 'Cancelled';
            $order->asset_status = 'Cancelled';
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

        //fix the error Allowed memory size on linux server
        // ini_set('memory_limit', '-1');

        $orderList = JTOrder::find(array(
            'conditions'=>array(
                    // '$or'=>array(
                    //     array('paid_by'=>'On Account'),
                    //     array('paid_by'=>'Multipay'),
                    // ),
                    'paid_by'=>array(
                        '$in'=>array('On Account','Multipay','Had paid')
                    ),
                    'deleted'=>false,
                    // 'status_id'=>array('$nin'=>array('Cancelled', 'Completed')),
                    '$or'=>array(
                        array('status_id'=>'Completed', 'had_paid'=>0),
                        array('status_id'=>array('$nin'=>array('Cancelled', 'Completed')))
                    )
            ),
            'sort'=> array("_id" => -1)
        ));

        if($orderList){            
            foreach ($orderList as $key => $value) {
                $cart = $value->cart;
                // if(isset($cart['payment_method']['On Account'])){}
                $value->order_id =  (string)$value->_id;
                $value->code =  substr($value->code, -2);
                $value->time = date("d-m-Y H:i:s",(int)$value->date_modified->sec+12*60);
                if(isset($value->time_delivery) && is_object($value->time_delivery))
                    $value->pick_time = date("Y-d-m H:i:s",(int)$value->time_delivery->sec);
                else
                    $value->pick_time = date("d-m-Y H:i:s",strtotime($this->getDatetimeFormated()));
                if(!isset($value->had_paid))
                    $value->had_paid = 0;
                if(!isset($value->had_paid_amount))
                    $value->had_paid_amount = 0;
                else if(round($cart['main_total']-$value->had_paid_amount,2)<0.009)
                    $value->had_paid = 1;
                else
                    $value->had_paid_amount = round($value->had_paid_amount,2);
                $order_list[] = $value->toArray();
            }            
        }        
        if($this->request->isAjax()){
            $this->view->disable();
        }
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        // pr($items);die;
        $this->view->partial('frontend/blocks/account_order');  
    }
    public function onlineOrderAction(){
        $order_list = array();

        //fix the error Allowed memory size on linux server
        // ini_set('memory_limit', '-1');

        $orderList = JTOrder::find(array(
            'conditions'=>array(
                    'heading'=>array('$in'=>array("Online","Create from Group order")),
                    'deleted'=>false,
                    'status_id'=>array('$ne'=>'Cancelled')
            ),
            'sort'=> array("_id" => -1)
        ));

        if($orderList){            
            foreach ($orderList as $key => $value) {
                $cart = $value->cart;
                // if(isset($cart['payment_method']['On Account'])){}
                $value->order_id =  (string)$value->_id;
                $value->code =  substr($value->code, -2);
                $value->time = date("d-m-Y H:i:s",(int)$value->date_modified->sec+12*60);
                if(isset($value->time_delivery) && is_object($value->time_delivery))
                    $value->pick_time = date("Y-d-m H:i:s",(int)$value->time_delivery->sec);
                else
                    $value->pick_time = date("d-m-Y H:i:s",strtotime($this->getDatetimeFormated()));
                if(!isset($value->had_paid))
                    $value->had_paid = 0;
                if(!isset($value->had_paid_amount))
                    $value->had_paid_amount = 0;

                if(isset($value->delivery_method) && $value->delivery_method=='delivery'){
                    $shipping_address = $value->shipping_address;
                    if(isset($shipping_address[0])){
                        $shipping_address = $shipping_address[0];
                        $value->delivery_address = $shipping_address['shipping_address_1'].', '.$shipping_address['shipping_town_city'].', '.$shipping_address['shipping_province_state'].', '.$shipping_address['shipping_country_id'].', '.$shipping_address['shipping_zip_postcode'];
                        // $value->delivery_customer_info = $shipping_address['full_name'].' - '.$shipping_address['phones'];

                    }
                }

                $order_list[] = $value->toArray();
            }            
        }
        if($this->request->isAjax()){
            $this->view->disable();
        }
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        // pr($items);die;
        $this->view->partial('frontend/blocks/online_order');  
    }
    public function adjustmentOrderAction(){
        $order_list = array();
        $orderList = JTOrder::find(array(
            'conditions'=>array('paid_by'=>'On Account','deleted'=>false),
            'sort'=> array("code" => -1)
        ));
        foreach ($orderList as $key => $value) {
            $value->order_id =  (string)$value->_id;
            $value->code =  substr($value->code, -2);
            $value->time = date("d-m-Y H:i:s",$value->date_modified->sec);
            $order_list[] = $value->toArray();
        }
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        $this->view->partial('frontend/blocks/adjustment_order');  
    }
    public function dailyHistoryAction(){
        $order_list = array();
        $nowtime = new \MongoDate(strtotime(date('Y-m-d').' 00:00:00'));
        
        $conditions = array();
        if($this->request->hasPost("codeOrder")){
            $conditions['code'] = array('$regex' => $this->request->getPost("codeOrder"));
        }
        $conditions['status'] = "Completed";
        $conditions['date_modified'] = array('$gte'  => new \MongoDate(strtotime(date('Y-m-d').' 00:00:00')),'$lte'  => new \MongoDate(strtotime(date('Y-m-d').' 23:59:59')));
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
            $value->code =  substr($value->code, -2);
            $value->time = date("d-m-Y H:i:s",$value->date_modified->sec);
            $order_list[] = $value->toArray();
        }
        $this->view->orders = $order_list;
        $this->view->baseURL = URL;
        $this->view->partial('frontend/blocks/daily_history');  
    }
    public function sendproductAccountOrderAction(){
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $order_id = $this->request->getPost('order_id');
        $order = JTOrder::findFirst(array(
            'conditions'=>array('_id'=>new \MongoId($order_id))
        ));
        if(isset($order->cart)&&isset($order->cart['items'])){
            foreach ($order->cart['items'] as $key => $value){
                $order->cart['items'][$key]['completed'] = 1;
            }
        }
        $order->paid_by = "Had paid";
        $order->had_paid = 1;
        // $order->manager_completed = 1;
        $order->status = "Completed";
        $order->status_id = "Completed";
        $order->asset_status = "Completed";
        $order->save();
    }
    public function payAccountOrderAction(){
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $arr_return = array('error'=>1,'message'=>'');
        $order_id = $this->request->getPost('order_id');
        $cash_tend = $this->request->hasPost("cash_tend")?$this->request->getPost("cash_tend"):'';
        $change_due = $this->request->hasPost("change_due")?$this->request->getPost("change_due"):0;
        $arr_paidby = $this->request->hasPost("Paidby")?$this->request->getPost("Paidby"):array();
        $completed = $this->request->hasPost("completed")?$this->request->getPost("completed"):'';
        // $order = new JTOrder();
        $order = JTOrder::findFirst(array(
            'conditions'=>array('_id'=>new \MongoId($order_id))
        ));
        $order->cash_tend = $cash_tend;
        $order->change_due = $change_due;
        $order->pay_account_order = $arr_paidby;
        $order->had_paid = 1;
        $order->had_paid_amount = $order->sum_amount;
        // if($completed=='completed'){
        //     $order->paid_by = "Had paid";
        //     $order->status = "Completed";
        //     $order->status_id = "Completed";
        //     $order->asset_status = "Completed";
        //     $arr_return['message'] = 'completed';
        // }
        $order->save();
        return $this->response($arr_return);
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
        (new \RW\Cart\Cart)->setOrderType(2);

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
        $this->view->partial('frontend/blocks/view_cart');
    }
    public function reprocessOrderNowAction(){
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $arr_return = array('error'=>1,'message'=>'Can not update');
        $order_id = $this->request->hasPost("orderId")?$this->request->getPost("orderId"):'';
        $arr_save = array();

        $current_date_time = $this->getDatetimeFormated();
        // $time_delivery =date('Y-m-d H:i:s',(int)strtotime($current_date_time)-3600);
        // $time_delivery =date('Y-m-d H:i:s');
        $time_delivery =date('Y-m-d H:i:s',(int)time()-2*3600);
        $time_delivery = new \MongoDate(strtotime($time_delivery));
        $arr_save['time_delivery'] = $time_delivery;

        if( (new \RW\Models\JTOrder)->reprocessOrder($order_id,$arr_save) ){
            $arr_return = array('error'=>0,'message'=>'Done');
        }
        return $this->response($arr_return);
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
        $this->view->partial('frontend/blocks/order_detail');
    }

    public function popupListAction(){
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $arr_return = array('error'=>0,'message'=>'');
        $arr_list = (new \RW\Models\JTContact)->find(array(
            'conditions'  => array(
                'deleted'           => false,
                'is_customer'       => 1,
            ),
            'fields' => ['_id', 'first_name', 'last_name', 'code', 'email','direct_dial','addresses'],
            'sort'      => array('first_name' => 1),
            'limit'=> 500
        ));
        $table_html = '<table class="display table table-bordered table-hover table-striped"  id="table_contact" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fullname</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                            </tr>
                            <tr id="search_field">
                                <th>#</th>
                                <th><input type="text" placeholder="Fullname">Fullname</th>
                                <th><input type="text" placeholder="Email">Email</th>
                                <th><input type="text" placeholder="Phone">Phone</th>
                                <th><input type="text" placeholder="Address">Address</th>
                            </tr>                            
                        </thead>
                        <tbody>';

        foreach ($arr_list as $key => $value) {
            if(!($value->first_name =='' && $value->last_name=='')){
                $table_html .= '<tr class="contact_list_items">';
                $table_html .= '<td>&nbsp;'.$value->code.'</td>';
                $table_html .= '<td class="link_items" '."onclick=\"choice_contacts('".$value->_id."')\">&nbsp;".$value->first_name.' '.$value->last_name;
                $table_html .= '<input type="hidden" id="after_choose_contact_'.$value->_id.'" value="'.htmlentities(json_encode(get_object_vars($value))).'" /></td>';
                $table_html .= '<td>&nbsp;'.$value->email.'</td>';
                $table_html .= '<td>&nbsp;'.$value->direct_dial.'</td>';
                $table_html .= '<td>&nbsp;'.$value->code.'</td>';
                $table_html .= '</tr>';
            }
        }
        $table_html .= '</tbody>
                        </table>';

        $arr_return['table_html'] = $table_html;
        return $this->response($arr_return);
    }

    public function testnoAction(){
        $field = 'code';
        $lastOrder = JTOrder::findFirst([
            'conditions'=>array(
                'deleted'=> false
            ),
            'fields' => [$field],
            'sort'   => ['_id' => -1]            
        ]);
        $code = isset($lastOrder[$field]) ? $lastOrder[$field] : 0;
        echo $code;die;
        $y = date('y');
        $m = str_pad(date('m'), 2, '', STR_PAD_LEFT);
        $prefix = "$y-$m-";
        if( strpos($code, $prefix) !== false ){
            $code = (int)str_replace($prefix, '', $code);
        } else {
            $code = 0;
        }
        echo $prefix.str_pad(++$code, 3, 0, STR_PAD_LEFT);die;
    }
    
}
