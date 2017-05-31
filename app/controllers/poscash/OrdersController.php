<?php
namespace RW\Controllers\Poscash;
use RW\Models\Products;
use RW\Models\Orders;
use RW\Models\Ordersitems;
use RW\Models\JTOrders;
use Phalcon\Paginator\Adapter\Model as _Paginator;
use Phalcon\Paginator\Adapter\NativeArray as _arrPaginator;

class OrdersController extends ControllerBase
{
	protected $notFoundMessage = 'This order did not exist.';
    protected $model;
    public function indexAction()
    {

    }
    public function listsAction(){

    }
    public function doRetrieveAction(){
        $datas = $this->getPost();
        $filter = new \Phalcon\Filter;
        $this->view->disable();
        $_return = array('error'=>1);
        if(!empty($datas)){
            $_id = $datas['_id'];
            $_one_record = JTOrders::findFirst(array('_id'=> new \MongoId($filter->sanitize($_id, 'string')) ) );
            if($_one_record){
                $_new_id = new \MongoId();
                $_one_record->_id = $_new_id;
                $_one_record->status = 'New';
                $_one_record->asset_status = 'New';
                $_one_record->status_id = 'New';
                if($_one_record->save()!==true){
                    echo $_one_record->getMessages();
                }else{
                    $_return = array('error'=>0,'_tokenKey'=>(string)$_new_id);
                }
            }
        }
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'UTF-8');
        return $response->setContent(json_encode($_return)); 
    }
    public function retrieveAction(){
        $_request = new \Phalcon\Http\Request();
        $_page_number = (int)$_request->get('pageNumber');
        $_customer_name = $_request->get('_customer_name');
        $_from = $_request->get('_from');
        $_to = $_request->get('_to');     
        $_sort_by = $_request->get('_sort_by');
        $_sort_by = $_sort_by!=''?explode(',',$_sort_by):array();     
        $_sort_value = $_request->get('_sort_value');
        $_sort_value = $_sort_value!=''?explode(',',$_sort_value):array();    
        if(!$_page_number) $_page_number = 1;        
        $_where = array('status'=>'Complete');
        if($_from!='' && $_to!=''){
            $_tos = new \MongoDate(strtotime($_to));
            $_froms = new \MongoDate(strtotime($_from));
            $_where ['salesorder_date'] = array('$gte'=>$_froms, '$lte'=>$_tos);
        }
        else if($_from!=''){            
            $_froms = new \MongoDate(strtotime($_from));
            $_where ['salesorder_date'] = array('$gte'=>$_froms);
        }
        else if($_to!=''){         
            $_tos = new \MongoDate(strtotime($_to));
            $_where ['salesorder_date'] = array('$lte'=>$_tos);
        }
        if($_customer_name!=''){
            $_where ['$or'] = array(
                array('contact_name'=>new \MongoRegex('/'.$_customer_name.'/i')),
                array('name'=>new \MongoRegex('/'.$_customer_name.'/i'))
            );
        }
        if(count($_sort_by)){
            foreach ($_sort_by as $key => $value) {
                if(isset($_sort_value[$key]) & $_sort_value[$key]!=''){
                    $_sort[$value] = $_sort_value[$key]=='DESC'?-1:1;
                } 
            }
        }else{
            $_sort = array('salesorder_date'=>-1);
        }
        $_datas = JTOrders::_ListSaleOrders($_where);
        $_list_orders = array();
        $datas = $this->getPost();
        $_run = 1;
        if($_page_number>1) $_run = _POS_PAGE_SIZE_ * $_page_number;        
        $_paginator   = new _arrPaginator(
            array(
                "data"  => $_datas,
                "limit" => _POS_PAGE_SIZE_,
                "page"  => $_page_number
            )
        );
        $_paginator_datas = $_paginator->getPaginate();                
        foreach($_paginator_datas->items as $key => $val){
            $_sub_total = 0;
            $_total_quantity = 0;
            $_products = _checkIsset($val['products'],array());
            if(!empty($_products)){
                for($i=0;$i<count($_products);$i++){
                    $_sub_total += (float) _checkIsset($_products[$i]['amount'],0);
                    $_total_quantity += (int) _checkIsset($_products[$i]['quantity'],0);
                }
            }
            $_list_orders [] = array(
                'order'=>$_run++,
                '_id'=>(string)$val['_id'],
                'no'=>_checkIsset($val['customer_po_no']),
                'dateCreated' => date('d-m-Y h:m:s',$val['salesorder_date']->sec),
                'name' =>  _checkIsset($val['name']),
                'contact_name' =>  _checkIsset($val['contact_name']),
                'quantity' =>  $_total_quantity,
                'price' =>  display_format_currency($_sub_total),
            );
        }
        $this->view->_list_orders = $_list_orders;        
        if($_paginator_datas->total_pages>1 && $_paginator_datas->total_pages >= $_page_number){
            if($_paginator_datas->before==$_page_number){
                $this->view->_before = '<span><i class="glyphicon glyphicon-triangle-left"></i></span>';
            }else{
                $this->view->_before = '<a href="/poscash/orders/current?pageNumber='.$_paginator_datas->before.'&_customer_name='.$_customer_name.'&_to='.$_to.'&_from='.$_from.'"><i class="glyphicon glyphicon-triangle-left"></i></a>';
            }
            if($_page_number==$_paginator_datas->total_pages){
                $this->view->_next = '<span><i class="glyphicon glyphicon-triangle-right"></i></span>';
            }else{
                $this->view->_next = '<a href="/poscash/orders/current?pageNumber='.$_paginator_datas->next.'&_customer_name='.$_customer_name.'&_to='.$_to.'&_from='.$_from.'"><i class="glyphicon glyphicon-triangle-right"></i></a>';
            }
            $this->view->_current = '<input onkeypress="return _keypress(event);" name="pageNumber" class="form-control" style="margin: auto;width:50px;text-align:center" value="'.$_page_number.'"/>';
        }else{
            $this->view->_before = '';
            $this->view->_current = '';
            $this->view->_next = '';
        }
        $this->view->_customer_name = $_customer_name;
        $this->view->_from = $_from;
        $this->view->_to = $_to;
        $this->view->baseURL = URL;
        $this->view->sort = $_sort;
    }
    public function currentAction(){
        $_request = new \Phalcon\Http\Request();
        $_page_number = (int)$_request->get('pageNumber');
        $_customer_name = $_request->get('_customer_name');
        $_from = $_request->get('_from');
        $_to = $_request->get('_to');        
        $_sort_by = $_request->get('_sort_by');
        $_sort_by = $_sort_by!=''?explode(',',$_sort_by):array();     
        $_sort_value = $_request->get('_sort_value');
        $_sort_value = $_sort_value!=''?explode(',',$_sort_value):array();       
        if(!$_page_number) $_page_number = 1;        
        $_where = array('status'=>'New');
        if($_from!='' && $_to!=''){
            $_tos = new \MongoDate(strtotime($_to));
            $_froms = new \MongoDate(strtotime($_from));
            $_where ['salesorder_date'] = array('$gte'=>$_froms, '$lte'=>$_tos);
        }
        else if($_from!=''){            
            $_froms = new \MongoDate(strtotime($_from));
            $_where ['salesorder_date'] = array('$gte'=>$_froms);
        }
        else if($_to!=''){         
            $_tos = new \MongoDate(strtotime($_to));
            $_where ['salesorder_date'] = array('$lte'=>$_tos);
        }
        if($_customer_name!=''){
            $_where ['$or'] = array(
                array('contact_name'=>new \MongoRegex('/'.$_customer_name.'/i')),
                array('name'=>new \MongoRegex('/'.$_customer_name.'/i'))
            );
        }
        
        if(count($_sort_by)){
            foreach ($_sort_by as $key => $value) {
                if(isset($_sort_value[$key]) & $_sort_value[$key]!=''){
                    $_sort[$value] = $_sort_value[$key]=='DESC'?-1:1;
                } 
            }
        }else{
            $_sort = array('salesorder_date'=>-1);
        }

        $_datas = JTOrders::_ListSaleOrders($_where,$_sort);
        // $_datas = json_decode(json_encode($_datas), FALSE);
        $_list_orders = array();
        $datas = $this->getPost();
        $_run = 1;
        if($_page_number>1) $_run = _POS_PAGE_SIZE_ * $_page_number;        
        $_paginator   = new _arrPaginator(
            array(
                "data"  => $_datas,
                "limit" => _POS_PAGE_SIZE_,
                "page"  => $_page_number
            )
        );
        $_paginator_datas = $_paginator->getPaginate();                
        foreach($_paginator_datas->items as $key => $val){
            $_sub_total = 0;
            $_total_quantity = 0;
            $_products = _checkIsset($val['products'],array());
            if(!empty($_products)){
                for($i=0;$i<count($_products);$i++){
                    $_sub_total += (float) _checkIsset($_products[$i]['amount'],0);
                    $_total_quantity += (int) _checkIsset($_products[$i]['quantity'],0);
                }
            }
            $_list_orders [] = array(
                'order'=>$_run++,
                'no'=>_checkIsset($val['customer_po_no']),
                'dateCreated' => date('d-m-Y h:m:s',$val['salesorder_date']->sec),
                'name' =>  _checkIsset($val['name']),
                'contact_name' =>  _checkIsset($val['contact_name']),
                'quantity' =>  $_total_quantity,
                'price' =>  display_format_currency($_sub_total),
            );
        }
        $this->view->_list_orders = $_list_orders;        
        if($_paginator_datas->total_pages>1 && $_paginator_datas->total_pages >= $_page_number){
            if($_paginator_datas->before==$_page_number){
                $this->view->_before = '<span><i class="glyphicon glyphicon-triangle-left"></i></span>';
            }else{
                $this->view->_before = '<a href="/poscash/orders/current?pageNumber='.$_paginator_datas->before.'&_customer_name='.$_customer_name.'&_to='.$_to.'&_from='.$_from.'"><i class="glyphicon glyphicon-triangle-left"></i></a>';
            }
            if($_page_number==$_paginator_datas->total_pages){
                $this->view->_next = '<span><i class="glyphicon glyphicon-triangle-right"></i></span>';
            }else{
                $this->view->_next = '<a href="/poscash/orders/current?pageNumber='.$_paginator_datas->next.'&_customer_name='.$_customer_name.'&_to='.$_to.'&_from='.$_from.'"><i class="glyphicon glyphicon-triangle-right"></i></a>';
            }
            $this->view->_current = '<input onkeypress="return _keypress(event);" name="pageNumber" class="form-control" style="margin: auto;width:50px;text-align:center" value="'.$_page_number.'"/>';
        }else{
            $this->view->_before = '';
            $this->view->_current = '';
            $this->view->_next = '';
        }
        $this->view->_customer_name = $_customer_name;
        $this->view->_from = $_from;
        $this->view->_to = $_to;
        $this->view->baseURL = URL;
        $this->view->sort = $_sort;
    }
    public function initialize(){        
    }
}

