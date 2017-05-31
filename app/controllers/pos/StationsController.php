<?php
namespace RW\Controllers\Pos;

use RW\Models\JTOrder;
use RW\Models\JTProduct;

class StationsController extends ControllerBase
{
    public function indexAction(){
    }
    public function drinkAction(){
        $this->listAction("Drinks");
    }
    public function bmsAction(){
        $this->listAction();
    }
    public function cartAction(){
        $this->listAction();
    }
    public function listAction($type=''){
        $arr_where = array();
        $arr_where['product_type'] = "Product";
        $arr_where['deleted'] = false;
        if($type=='Drinks')
            $arr_where['category'] = "Drinks";
        else
            $arr_where['category'] = array('$ne'=>"Drinks");

        $list_bms = JTProduct::find(array('conditions'=>$arr_where));
        $arr_bms = array();
        foreach ($list_bms as $key => $value) {
            $arr_bms[] = $value->_id;
            $arr_bms_pro[(string)$value->_id] = $value->toArray();
        }
        $list_order = JTOrder::find(array(
                                'conditions'=> array("status"=>"In production",
                                                      "completed"=>array('$ne'=>1),
                                                      "products"=>array('$elemMatch'=>array(
                                                            "products_id"=>array('$in'=>$arr_bms)
                                                        )),
                                                      'deleted'=>false
                                                ),
                                "sort"       => array("name" => 1)
                            ));
        $arr_item = $arr_order = $arr_pro = array();
        if($list_order){
            foreach ($list_order as $key => $value){
                $order_item = $value->toArray();
                $check_has = false;
                if(isset($order_item['cart']['items'])&& !empty($order_item['cart']['items'])){
                    foreach ($order_item['cart']['items'] as $cartkey => $cartval) {
                        if(isset($cartval['completed']) || !in_array($cartval['_id'],$arr_bms)) {
                            continue;
                        }
                        $cartval['order_id'] = (string)$value->_id;
                        $cartval['order_code'] = (string)$value->code;
                        $cartval['products_id'] = $cartval['_id'];
                        $cartval['products_name'] = $cartval['name'];
                        $arr_item[] = $cartval;
                        if(isset($cartval['options']))
                        foreach ($cartval['options'] as $kk => $op) {
                            if($op['option_type']!='')
                            $arr_pro[$op['_id']] = $op['option_type'];
                        }
                    }
                    $check_has = true;
                }else{
                    foreach ($value->products as $prokey => $proval) {
                        if ($proval['deleted']==true || (isset($proval['completed'])&&$proval['completed']==1)) {
                            continue;
                        }
                        if(isset($proval['products_id']) && in_array($proval['products_id'],$arr_bms)){
                            $idpro = (string)$proval['products_id'];
                            $proval['image'] = '';
                            if(isset($arr_bms_pro[$idpro]['products_upload']) && !empty($arr_bms_pro[$idpro]['products_upload'])){
                                foreach ($arr_bms_pro[$idpro]['products_upload'] as $k => $v) {
                                    if($v['deleted']==false){
                                        $proval['image'] = JT_URL.$v['path'];
                                    }
                                }
                            }
                            $proval['order_id'] = (string)$value->_id;
                            $proval['order_code'] = (string)$value->code;
                            $proval['note'] = '';
                            $proval['options'] = array();
                            $arr_item[] = $proval;
                            $check_has = true;
                        }
                    }
                }
                //check cart
                
                if($check_has){
                    $arr_order[(string)$order_item['_id']] = $order_item;
                }
            }
        }
        if($this->request->isAjax())
            $this->view->disable();
        $this->view->arr_item = $arr_item;
        $this->view->arr_order = $arr_order;
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);

        if($this->request->isAjax())
            $this->view->partial('pos/Stations/loadmore');
        // pr($arr_item);pr($arr_order);die;
    }

    public function completeAction()
    {
        $this->view->disable();
        $order_id = $this->request->getPost('order_id');
        $product_id = $this->request->getPost('product_id');
        $order = JTOrder::findFirst(array(array('_id'=>new \MongoId($order_id))));
        $check_complete_order = true;
        /*foreach ($order->products as $key => $value) {
            if((string)$value['products_id'] == $product_id){
                $order->products[$key]['completed']=1;
            }
            if(!isset( $order->products[$key]['completed'] ))
            $check_complete_order = false;
        }*/
        if(isset($order->cart)&&isset($order->cart['items'])){
            foreach ($order->cart['items'] as $key => $value) {
                if((string)$value['_id'] == $product_id){
                    $order->cart['items'][$key]['completed']=1;
                }
                if(!isset($order->cart['items'][$key]['completed']))
                    $check_complete_order = false;
            }
        }
        if($check_complete_order){
            $order->completed = 1;
            $order->status = 'Completed';
            $order->status_id = 'Completed';
            $order->asset_status = 'Completed';
        }
        if($order->save()){
            echo 1;
        }else{
            echo 0;
        }
    }

}
