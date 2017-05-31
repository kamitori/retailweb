<?php

namespace RW\Controllers\Pos;
use RW\Models\Products;
use RW\Models\Orders;
use RW\Models\Ordersitems;
use RW\Models\Users;
class OrdersController extends ControllerBase
{
	protected $notFoundMessage = 'This order did not exist.';
    protected $model;
    public function indexAction()
    {

    }
    public function SaleOrdersAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $arr_order = Orders::findFirst($filter->sanitize($data['txt_name'], 'int'));
        $arr_order->status = 2;
        if($arr_order->save()!==true){
            echo $arr_order->getMessage();
        }
    }
    public function loadOrderAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $id = (float)$data['txt_name'];
        $arr_order = Orders::findFirst(
            $filter->sanitize($id, 'int')
        );
        $arr_items = $arr_order->getOrderItems(['conditions' => "deleted = 0"]);
        $arr_items = $arr_items->toArray();
        $v_return = '';
        $v_total_tax = 0;
        $v_sub_tag = 0;
        for($i=0;$i<count($arr_items);$i++){
            $v_return .= $this->drawOneOrderItem($arr_items[$i]);
            $v_total_tax += (float)$arr_items[$i]['tax'];
            $v_sub_tag += (float)$arr_items[$i]['price'];
        }
        $v_total_price = $v_sub_tag + $v_total_tax;
        $arr_return = [
            'datas'=>$v_return
            ,'error'=>0
            ,'sub_price'=>display_format_currency($v_sub_tag)
            ,'to_pay'=>display_format_currency($v_total_price)
            ,'sub_tax'=>display_format_currency($v_total_tax)
            ,'total'=>$v_sub_tag
            ,'description'=>$arr_order->description
            ,'_id'=>$arr_order->id
        ];
        $this->_isJsonResponse = true;
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'UTF-8');
        return $response->setContent(json_encode($arr_return)); 
    }
    function drawOneOrderItem($item){
        return '
                <div class="row">
                    <div class="quantity col-md-1" data-quantity="'.$item['quantity'].'" data-price="'.$item['price'].'">
                        <span class="badge">
                            '.$item['quantity'].'
                        </span>
                    </div>
                    <div class="info col-md-5">
                        <div class="name">
                            '.$item['productName'].'
                        </div>
                    </div>
                    <div class="list col-md-1">
                        <i class="glyphicon glyphicon-th-list"></i>
                    </div>
                    <div class="col-md-2 price">
                        <span class="badge">
                           '.$item['unitprice'].'
                        </span>
                    </div>
                    <div class="col-md-2 amount">
                        <span class="badge">
                            '.$item['totalprice'].'
                        </span>
                    </div>
                    <div class="col-md-1 delete" data-id="'.$item['id'].'" onclick="deleteItem(this)">
                        <i class="glyphicon glyphicon-remove-sign"></i>
                    </div>
                </div>
        ';
    }
    public function RetrieveSaleAction(){
        die;
    }
    public function createDiscountAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $_price = (float)$data['txt_name'];     
        $arr_order  = Orders::findFirst(
            $filter->sanitize($this->session->get("current-order"), 'int')
        );
        $ordersitems = new Ordersitems;
        $ordersitems->productId = 0;
        $ordersitems->productName = 'Discount';
        $ordersitems->unitprice = $filter->sanitize(display_format_currency((float)$_price), 'string');
        $ordersitems->quantity = -1;
        $ordersitems->orderId = $filter->sanitize($arr_order->id, 'int');
        $ordersitems->deleted = 0;
        $ordersitems->userId = 0; // chua lam dang nhap
        $ordersitems->categoryId = 0;
        $ordersitems->categoryName = 'Discount';
        $ordersitems->price = $_price;
        $ordersitems->totalprice = $filter->sanitize(display_format_currency((float)$_price), 'string');
        $ordersitems->tax = (float) ($_price*-1)/10.0;        
        if($ordersitems->save()!==true){
            echo $ordersitems->getMessage();
        }
        $arr_order->totalPrice = (float) $arr_order->totalPrice + (float) $_price*-1;
        $arr_order->totalTax = (float) $arr_order->totalTax + (float) $ordersitems->tax;
        $arr_order->SubTotal = $arr_order->totalTax + $arr_order->totalPrice;
        if($arr_order->totalPrice <=0) $arr_order->totalPrice = 0;
        if($arr_order->SubTotal <=0) $arr_order->SubTotal = 0;
        if($arr_order->save()!==true){
            echo $arr_order->getMessage();
        }
    }
    public function parkOrderAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $arr_post = $this->getPost();
        $order_id = $this->session->get("current-order");
        if(isset($arr_post['txt_p'])) $order_id = $arr_post['txt_p'];
        $deleted = (int)$arr_post['txt_name'];
        $arr_order = Orders::findFirst($filter->sanitize($order_id, 'int'));        
        if($arr_order){
            $arr_order->deleted = $deleted;
            if($arr_order->save()!==true){
                echo $arr_order->getMessage();
            }
        }
        if($deleted){
            $arr_new_oder = $this->createPosOrder();
            $this->session->set("current-order", $arr_new_oder['id']);
            $response = new \Phalcon\Http\Response();
            $response->setContentType('application/json', 'UTF-8');
            $arr_return = ['order'=>$arr_new_oder['id']];
            return $response->setContent(json_encode($arr_return));
        }        
    }
    public function saveNotesAction(){
        $this->view->disable();
        $data = $this->getPost();
        $order_id = $this->session->get("current-order");
        if(isset($data['txt_p'])) $order_id = $data['txt_p'];
        $filter = new \Phalcon\Filter;
        $arr_order = Orders::findFirst($filter->sanitize($order_id, 'int'));
        if($arr_order){
            $arr_order->description = $filter->sanitize($data['txt_name'], 'string');
            if($arr_order->save()!==true){
                echo $arr_order->getMessage();
            }
        }
    }
    public function initialize(){        
    }
    public function deleteOrderAction(){
        $this->view->disable();
        $data = $this->getPost();
        $filter = new \Phalcon\Filter;
        $arr_order_items = Ordersitems::findFirst($filter->sanitize($data['txt_name'], 'int'));
        $arr_order_items->deleted = 1;
        if($arr_order_items->save()!==true){
            echo $arr_order_items->getMessage();
        }
        $order_id = $this->session->get("current-order");
        if(isset($data['txt_p'])) $order_id = $data['txt_p'];
        $arr_order = Orders::findFirst($filter->sanitize($order_id, 'int'));
        $arr_order->totalPrice = (float)$arr_order->totalPrice - (float)$arr_order_items->price;
        if($arr_order->totalPrice<=0) $arr_order->totalPrice = 0;
        $arr_order->totalTax = (float)$arr_order->totalTax - (float)$arr_order_items->tax;
        if($arr_order->totalTax<=0) $arr_order->totalTax = 0;

        $arr_order->SubTotal = $arr_order->totalPrice + $arr_order->totalTax ;
        if($arr_order->totalPrice <=0) $arr_order->totalPrice = 0;
        if($arr_order->SubTotal <=0) $arr_order->SubTotal = 0;

        if($arr_order->save()!==true){
            echo $arr_order->getMessage();
        }
    }
    public function CloseRegisterAction(){
        $this->view->disable();
        $arr_post = $this->getPost();
        if(isset($arr_post['txt_name'])){
            $_ordersId = $this->session->get("current-close-register");
            if(Orders::_updatePart($_ordersId['_listOrdersId'])){
                $this->session->remove("current-close-register");
            }            
        }
    }
    public function removeCustomerAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $_order_id = $data['txt_name'];
        $_check = Orders::findFirst($filter->sanitize($_order_id, 'int'));
        if($_check){
            $_check->customerId = 0;
            $_check->customerName = '';
            $_check->save(); // nen co them 1 phan log o day nhung do logic project ko can nen ko lam - [hung-do-order.3.1.5.(*)]
        }
    }
    public function updateCustomerAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $_email_ = $filter->sanitize($data['txt_name'], 'string');
        $_email_ = trim($_email_);
        $_order_id = $data['txt_p'];
        $_user = Users::findFirst(
            [
                'conditions' => "email = '". $_email_ ."'"
            ]
        );
        if($_user){            
            $_order_ = Orders::findFirst($filter->sanitize($_order_id, 'int'));
            if($_order_){
                $_order_->customerId = $_user->id;
                $_order_->customerName = $_user->first_name.' '.$_user->last_name;
                if($_order_->save()!== true){
                    echo $_order_->getMessages();
                }
            }
        }else{
            // echo $_user->getMessages();
        }
    }
    public function AddCustomerAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $_arr_ = $data['txt_name'];
        $_order_id_ = $data['txt_p'];
        $_arr_data = [];
        $_check_email = true;
        $_message_ = '';
        $_m_ = '';
        $_y_ = '';
        $_d_ = '';
        $_full_name = '';
        for($i =0 ;$i<count($_arr_);$i++){
            if(!isset($_arr_[$i]['_id'])){
                continue;
            }            
            if($_arr_[$i]['_id']=='dob_month'){
                $_m_ = $_arr_[$i]['_val'];
            }
            else if($_arr_[$i]['_id']=='dob_year'){
                $_y_ = $_arr_[$i]['_val'];
            }
            else  if($_arr_[$i]['_id']=='dob_day'){
                $_d_ = $_arr_[$i]['_val'];
            }else{
                $_arr_data [$_arr_[$i]['_id']] = $_arr_[$i]['_val'];
            }
            if($_arr_[$i]['_id']=='email'){
                if (!filter_var($_arr_[$i]['_val'], FILTER_VALIDATE_EMAIL)){
                    $_message_ = $_arr_[$i]['_val'] . ' is not a valid email, plese try again';
                    $_check_email = false;
                    break;
                }else{
                    $_on_user = Users::findFirst([
                        'conditions' => "email = '". $_arr_[$i]['_val']."'"
                    ]);
                    if($_on_user){
                        $_message_ = $_arr_[$i]['_val'] . ' already existed, please try a different email';
                        $_check_email = false;
                        break;
                    }
                }                
            }
        }
        $_full_name = $_arr_data['first_name'] . ' '.$_arr_data['last_name'];
        $_arr_data ['birthday'] = $_y_.'/'.$_m_.'/'.$_d_;
        $_arr_data ['password'] = $this->security->hash('123456');
        $_arr_data ['fullname'] = $_full_name;
        $_data_return = [];
        if($_check_email){
            $_user = new Users;
            foreach ($_arr_data as $key => $value) {
                $_user->$key = $value;
            }
            if($_user->save()!==true){
                $_data_return = ['error_check_email'=>1,'message'=>$_user->getMessages()];    
            }else{
                $_data_return = ['error_check_email'=>0,'message'=>'Ok','_order_'=>$_user->getId(),'full_name'=>$_full_name];
                $orders = Orders::findFirst((int)$_order_id_);
                if($orders){
                    $orders->customerId = (int) $_user->getId();
                    $orders->customerName = $_full_name;
                    $orders->save();
                }
            }
        }else{
            $_data_return = ['error_check_email'=>1,'message'=>$_message_];
        }
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'UTF-8');
        return $response->setContent(json_encode($_data_return));
    }
    public function createOrderAction(){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $_quantity = 0;
        $order_id = $this->session->get("current-order");
        if(isset($data['txt_p'])) $order_id = $data['txt_p'];
        if(isset($data['txt_v'])) $_quantity = $data['txt_v'];
        $arr_products = Products::findFirst([
            'conditions' => "name = '". $data['txt_name']."'"
        ]);
        $arr_category = $arr_products->categories->toArray();
        $arr_products = $arr_products->toArray();
        
        $_check_product_existed = Ordersitems::findFirst([
            'conditions' => "orderId = '". $order_id."' and deleted = 0 and productId = '".$arr_products['id']."'"
        ]);
        if($_check_product_existed){
            if($_quantity) $_check_product_existed->quantity = (int) $_quantity;
            else $_check_product_existed->quantity = (int)$_check_product_existed->quantity + 1;
            $_check_product_existed->unitprice = display_format_currency($arr_products['price']);
            $_check_product_existed->price = $arr_products['price'];
            $_check_product_existed->totalprice = display_format_currency($_check_product_existed->price);
            $_check_product_existed->tax = $_check_product_existed->price/10.0;
            if($_check_product_existed->save()!==true){
                echo $ordersitems->getMessage();
            }
            $response = new \Phalcon\Http\Response();
            $response->setContentType('application/json', 'UTF-8');
            $arr_return = $this->reSumOrderTotalPrice($order_id);
            return $response->setContent(json_encode($arr_return));
        }else{
            $arr_order  = Orders::findFirst(
                $filter->sanitize($order_id, 'int')
            );            
            $ordersitems = new Ordersitems;
            $ordersitems->productId = $filter->sanitize($arr_products['id'], 'int');
            $ordersitems->productName = $filter->sanitize($arr_products['name'], 'string');
            $ordersitems->unitprice = $filter->sanitize(display_format_currency((float)$arr_products['price']), 'string');
            $ordersitems->quantity = 1;
            $ordersitems->orderId = $filter->sanitize($arr_order->id, 'int');
            $ordersitems->deleted = 0;
            $ordersitems->userId = 0; // chua lam dang nhap
            $ordersitems->categoryId = $filter->sanitize($arr_category['id'], 'int');
            $ordersitems->categoryName = $filter->sanitize($arr_category['name'], 'string');
            $ordersitems->price = $filter->sanitize($arr_products['price'], 'float');
            $ordersitems->totalprice = $filter->sanitize(display_format_currency((float)$arr_products['price']), 'string');
            $ordersitems->tax = (float) $arr_products['price']/10.0;
            if($ordersitems->save()!==true){
                echo $ordersitems->getMessage();
            }
            $arr_order->totalPrice = (float) $arr_order->totalPrice + (float) $arr_products['price'];
            $arr_order->totalTax = (float) $arr_order->totalTax + (float) $ordersitems->tax;
            $arr_order->SubTotal = $arr_order->totalPrice + $arr_order->totalTax ;
            if($arr_order->totalPrice <=0) $arr_order->totalPrice = 0;
            if($arr_order->SubTotal <=0) $arr_order->SubTotal = 0;
            if($arr_order->save()!==true){
                echo $arr_order->getMessage();
            }
        }        
    }    
    public function reSumOrderTotalPrice($_ordersId){
        $this->view->disable();
        $filter = new \Phalcon\Filter;
        $_arr_orders_list = Ordersitems::find([
            'conditions' => "orderId = '". $_ordersId."' and deleted = 0"
        ]);
        $_total_price = 0;            
        foreach($_arr_orders_list as $_item){
            $_total_price += (float) $_item->price * (int)$_item->quantity;            
        }
        $_total_tax = $_total_price / 10.0;
        $_subTotal = $_total_price + $_total_tax;
        $arr_order  = Orders::findFirst(
            $filter->sanitize($_ordersId, 'int')
        );
        $arr_order->totalPrice = (float) $_total_price;
        $arr_order->totalTax = (float) $_total_tax;
        $arr_order->SubTotal = $_subTotal;
        if($arr_order->totalPrice <=0) $arr_order->totalPrice = 0;
        if($arr_order->SubTotal <=0) $arr_order->SubTotal = 0;
        if($arr_order->save()!==true){
            echo $arr_order->getMessage();
        }
        return [
            '_totalPrice'=> display_format_currency($arr_order->totalPrice),
            '_hiddenPrice'=> $arr_order->totalPrice,
            '_totalTax'=> display_format_currency($arr_order->totalTax) ,
            '_topay'=> display_format_currency($arr_order->totalPrice + $arr_order->totalTax),
            '_type'=>'custom-price'
        ];
    }
    public static function createPosOrder(){        
        $orders = new Orders;
    	$orders->code = 'oPos-'.($orders->countTotalOrder()+1);        
    	if ($orders->save() === true) {
            return $orders->toArray();
        } else {
            echo $orders->getMessage();
            return false;
        }        
    }

}

