<?php
namespace RW\Controllers;

use RW\Models\JTOrder;
use RW\Models\JTTask;
use RW\Models\JTProduct;
use Phalcon\Mvc\View;

class StationsController extends ControllerBase
{
    public function indexAction(){
    }
    public function drinkAction(){
        $this->listAction("drink");
    }
    public function bmsAction(){
        $this->listAction("bms");
    }
    public function kitchenAction(){
        $this->listAction("kitchen");
    }
    public function managerAction(){
        $this->listAction("manager");
    }
    public function cartAction(){
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        $this->view->arrbanner_small = (new \RW\Models\Banners)->getListBannersByType(5);
        $this->view->arrbanner_full = (new \RW\Models\Banners)->getListBannersByType(4);
    }
    public function cartviewAction($id){
        $this->view->idss = $this->dispatcher->getParam("id");
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
        $this->view->arrbanner_small = (new \RW\Models\Banners)->getListBannersByType(5);
        $this->view->arrbanner_full = (new \RW\Models\Banners)->getListBannersByType(4);
    }
    public function listAction($type=''){
        $prolist = JTProduct::findByAssetType($type);
        $arr_bms = $prolist['arr_bms'];
        $arr_bms_pro = $prolist['arr_bms_pro'];
        $arr_station_asset_id = array(
                'kitchen' => '52b738cbe6f2b23a680943c3',
                'drink' => '5279a88767b96daa4b000029',
                'bms' => '51ef8224222aad6011000092',
                'manager' => '52b73fa3e6f2b24e690943b2'
            );
        $date_like = date("y-m");

        $order_conditions = array( 
                                    // '$or'=>array(
                                    //         array("status"=>"In production",'heading'=>array('$ne'=>'Online')),
                                    //         array(
                                    //                 "status"=>"In progress",
                                    //                 'heading'=>'Online'
                                    //             )
                                    // ),
                                    'status'=>"In production",
                                    'completed'=>array('$ne'=>1),
                                    '$or'=>array(
                                            array('code'=>array('$regex'=>$date_like)),
                                            array('code'=>array('$regex'=>'16-08'))
                                        ),
                                    //'code'=>array('$regex'=>$date_like), // 16-08
                                    // 'products'=>array('$elemMatch'=>array(
                                    //     "products_id"=>array('$in'=>$arr_bms)
                                    // )),
                                 'deleted'=>false
                                );
        $codeOrder = '';
         //search code
        if($this->request->hasPost("codeOrder")){
            $codeOrder = $this->request->getPost("codeOrder");
            $order_conditions['code'] = array('$regex' => $codeOrder);
        }
        if($type=='manager'){
            unset($order_conditions['status']);
            unset($order_conditions['completed']);
            $order_conditions['code']= array('$regex'=>$date_like);
            unset($order_conditions['$or']);
            $order_conditions['status'] = array('$in'=>array('In production','Completed'));
            // $order_conditions['manager_completed'] = 0;
            //neu paid_by khong phai la loai on account thi xet completed # 1
            //nguoc lai neu la loai on account thi khong xet completed
            $order_conditions['$or'] = array(
                                                array('create_from'=>'Create from POS', 'manager_completed'=>0),
                                                array('create_from'=>array('$ne'=>'Create from POS'),'manager_completed'=>array('$ne'=>1))
                                            );

            // $order_conditions['paid_by'] = array('$ne'=>'Had paid'); 
            // $order_conditions['had_paid'] = array('$ne'=>1);            
        }else{
            $now = $this->getDatetimeFormated();
            $time_ahour = strtotime($now)+3600;
            $order_conditions['datetime_pickup'] = array(
                    '$lte'  => new \MongoDate($time_ahour)
                );
        }     
        $list_order = JTOrder::find(array(
                        'conditions'    => $order_conditions,
                        'sort'          => array('datetime_pickup' => 1)
                      ));
        $arr_item = $arr_order = $arr_pro = array();
        $type_id = $arr_station_asset_id[$type];

        //collapse or expand
        $arr_collapse = $this->session->has('arr_collapse') ? $this->session->get('arr_collapse') : array();
        $arr_expand = $this->session->has('arr_expand') ? $this->session->get('arr_expand') : array();

        $arr_order_late = array();

        if($list_order){
            foreach ($list_order as $key => $value){
                $order_item = $value->toArray();
                if(!isset($order_item['paid_by']))
                    $order_item['paid_by'] = '';
                // echo $order_item['code'].'<br>';
                if($type=='manager' && $order_item['paid_by'] == 'Multipay' && !isset($order_item['cart']['payment_method']['On Account']))
                    continue;
                
                $check_has = false;
                $this_item = array();
                $color_bar = 'green';
                

                if(!isset($order_item['heading']))
                    $color_bar = 'green';
                //neu la order online
                else if($order_item['heading']=='Online' || $order_item['heading']=='Create from Group order')
                    $color_bar = 'red';
                //neu la POS1
                else if($order_item['heading']=='Create from bms-order-station')
                    $color_bar = 'yellow';
                //neu la Payment on account
                else if(isset($order_item['paid_by']) && ($order_item['paid_by']=='On Account' || ($order_item['paid_by']=='Multipay' && isset($order_item['payment_method']['On Account']) ) ))
                    $color_bar = 'blue';
                if($type=='manager' && !isset($arr_expand[(string)$order_item['_id']]) ){
                    $arr_collapse[(string)$order_item['_id']] = (string)$order_item['_id'];
                }

                //neu la task

                if(isset($order_item['cart']['items'])&& !empty($order_item['cart']['items'])){
                    //collapse or expand
                    $collapse_item = array();
                    if(isset($arr_collapse[(string)$order_item['_id']])){
                        $collapse_item['order_id'] = (string)$order_item['_id'];
                        $collapse_item['order_code'] = (string)$order_item['code'];
                        $collapse_item['cart_id'] = '';
                        $collapse_item['products_id'] = '';
                        $collapse_item['products_name'] = 'Order #'.substr($collapse_item['order_code'], -2);
                        $collapse_item['contact_name'] = $order_item['contact_name'];
                        $collapse_item['phone'] = $order_item['phone'];
                        $collapse_item['email'] = $order_item['email'];
                        $collapse_item['time'] = intval(substr((string)$order_item['_id'], 0, 8), 16);
                        $collapse_item['collapse'] = 1;
                        $collapse_item['arr_image'] = array();
                        $collapse_item['line'] = array();
                        $collapse_item['quantity'] = 0;
                        $collapse_item['color_bar'] = $color_bar;
                        $collapse_item['pos_no'] = isset($order_item['cart']['pos_id'])?$order_item['cart']['pos_id']:'';
                        $collapse_item['had_paid'] = isset($order_item['had_paid'])?$order_item['had_paid']:0;
                        $collapse_item['had_paid_amount'] = isset($order_item['had_paid_amount'])?$order_item['had_paid_amount']:0;

                        if($type=='manager') {
                            $time_delivery_10m = $order_item['time_delivery']->sec+600;
                            if(strtotime($now) > $time_delivery_10m) {
                                $collapse_item['late'] = 1;
                                $arr_order_late[] = ['code'=>(string)$order_item['code'],
                                                        'no'=>'Order #'.substr($collapse_item['order_code'], -2)
                                                    ];
                            }
                        }

                    }

                    foreach ($order_item['cart']['items'] as $cartkey => $cartval) {
                        if($type!='manager' && (isset($cartval['completed']) || !in_array($cartval['_id'],$arr_bms))) {
                            continue;
                        }

                        if($type!='manager' && isset($cartval['multi_compl']) && isset($cartval['multi_compl'][$type_id]) && $cartval['multi_compl'][$type_id]=='completed'){
                            continue;
                        }

                        $cartval['order_id'] = (string)$order_item['_id'];
                        $cartval['order_code'] = (string)$order_item['code'];
                        $cartval['cart_id'] = $cartkey;
                        $cartval['products_id'] = $cartval['_id'];
                        $cartval['products_name'] = $cartval['name'];
                        $cartval['contact_name'] = $order_item['contact_name'];
                        $cartval['phone'] = $order_item['phone'];
                        $cartval['email'] = $order_item['email'];
                        $cartval['time'] = intval(substr((string)$order_item['_id'], 0, 8), 16);
                        $cartval['collapse'] = 0;
                        $cartval['color_bar'] = $color_bar;
                        $cartval['pos_no'] = isset($order_item['cart']['pos_id'])?$order_item['cart']['pos_id']:'';
                        $cartval['had_paid'] = isset($order_item['had_paid'])?$order_item['had_paid']:0;
                        $cartval['had_paid_amount'] = isset($order_item['had_paid_amount'])?$order_item['had_paid_amount']:0;

                        //collapse or expand
                        if(isset($arr_collapse[$cartval['order_id']])){
                            $collapse_item['arr_image'][] = $cartval['image'];
                            $collapse_item['line'][] = $cartval;
                            $collapse_item['quantity']+=$cartval['quantity'];
                            
                        }else
                            $arr_item[] = $cartval;

                        if(isset($cartval['options']))
                        foreach ($cartval['options'] as $kk => $op) {
                            if($op['option_type']!='')
                            $arr_pro[$op['_id']] = $op['option_type'];
                        }
                    }
                    //collapse or expand
                    if(isset($arr_collapse[(string)$order_item['_id']])){
                        $arr_item[] = $collapse_item;
                    }
                    $check_has = true;
                }

                
                //check cart
                if($check_has){
                    $arr_order[(string)$order_item['_id']] = $order_item;
                }
            }
        }
        


        //find in asset resource
        $task_lists = (new \RW\Models\JTResource)->task_list_by_user($type);

        $task_conditions['is_kitchen'] = 1;
        $task_conditions['$or'][] = array('our_rep_id'=>new \MongoId($arr_station_asset_id[$type]));
        $task_conditions['$or'][] = array('_id'=>array('$in'=>$task_lists));
        // $task_conditions['our_rep_id'] = new \MongoId($arr_station_asset_id[$type]);
        $task_conditions['deleted'] = false;
        $task_conditions['status'] = array('$nin'=>array("Cancelled","DONE"));
        $query_task = JTTask::find(array(
                            'conditions'=> $task_conditions,
                            'sort'       => array("date_modified" => 1)
                        ));
        foreach($query_task as $key => $value){
            $data_tmp = $value->toArray();
            $task_item = array();
            $task_item['products_name'] = $data_tmp['name'];
            $task_item['note'] = '';
            $task_item['quantity'] = 1;
            $task_item['order_code'] = $data_tmp['no'];
            $task_item['order_id'] = (string)$data_tmp['_id'];
            $task_item['products_id'] = (string)$data_tmp['_id'];
            $task_item['contact_name'] = 'Kitchen';
            $task_item['phone'] = '';
            $task_item['email'] = '';
            $task_item['time'] = intval(substr((string)$data_tmp['_id'], 0, 8), 16);
            $task_item['image'] = URL.'/themes/banhmisub/images/BmiSUB_logo.png';
            $task_item['options'] = array();
            $task_item['type'] = 'task';
            $task_item['color_bar'] = 'pos_tax';
            $arr_item[]= $task_item;
        }
       
       
        if($this->request->isAjax())
            $this->view->disable();

        $this->view->codeOrder = $codeOrder;

        //sort lai theo thoi gian
        $arr_item = aasort($arr_item,'time',1);
        $this->view->arr_item = $arr_item;

        //check _timelog for ring ring
        $newu = count($arr_item)-1;
        $timelog = isset($arr_item[$newu]['time'])?$arr_item[$newu]['time']:0;
        if(!$this->session->has($type.'_timelog'))
            $this->session->set($type.'_timelog',$timelog);
        if(intval($timelog) > intval($this->session->get($type.'_timelog')))
            $this->view->ring = 1;
        else 
            $this->view->ring = 0;

        $this->session->set($type.'_timelog',$timelog);
        $this->view->timelog = $timelog;

        $this->view->arr_order = $arr_order;
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->type = $type;
        $this->view->timelog = $timelog;

        $this->view->arr_order_late = $arr_order_late;

        $this->view->baseURL = URL;
        $this->view->theme = 'themes/frontend/';
        $this->view->JT_URL = JT_URL;
        $view = 'frontend/Stations/loadmore';
        if($type=='manager')
            $view = 'frontend/Stations/loadmore_manager';
        if($this->request->isAjax()){
            $this->view->partial($view);
        }
    }
    public function viewcartsAction(){
        $this->view->disable();
        $my_config = "viewcarts.json";
        $configs = file_get_contents($my_config);
        $configs = json_decode($configs, true);
        if(!$configs || empty($configs)){
            $configs = array("1"=>["screen_id"=>"1", "url"=>"/cartview/1"],
                            "2"=>["screen_id"=>"2", "url"=>"/cartview/2"],
                            "3"=>["screen_id"=>"3", "url"=>"/cartview/3"],
                            "4"=>["screen_id"=>"4", "url"=>"/cartview/4"],
                            "5"=>["screen_id"=>"5", "url"=>"/cartview/5"],
                            "6"=>["screen_id"=>"6", "url"=>"/cartview/6"],
                );
            $fh = fopen($my_config, 'w') or die("can't open file");
            fwrite($fh, json_encode($configs));
            fclose($fh);    
        }
        $this->view->screenview = $configs;
        $this->view->partial('frontend/Stations/viewcarts');
    }
    public function updateUrlScreenAction(){
        $my_config = "viewcarts.json";
        $this->view->disable();
        if($this->request->isPost()){
            $configs = file_get_contents($my_config);
            $configs = json_decode($configs, true);
            if(!$configs) $configs = array();
            $screen_id = isset($_POST['screen_id']) ? $_POST['screen_id'] : 1;
            $url = isset($_POST['url']) ? $_POST['url'] : '/cartview/1';
            $configs[$screen_id] = ['screen_id'=>$screen_id, 'url'=>trim($url)];
            $fh = fopen($my_config, 'w') or die("can't open file");
            fwrite($fh, json_encode($configs));
            fclose($fh);    
            if($this->request->isAjax()){
                echo json_encode(['status'=>'ok']);
            }
        }
        die;        
    }
    public function completeAction(){
        $this->view->disable();
        $order_id = $this->request->getPost('order_id');
        $product_id = $this->request->getPost('product_id');
        $cart_id = $this->request->getPost('cart_id');
        $station_type = $this->request->getPost('bms_type');
        $arr_station_asset_id = array(
                'kitchen' => '52b738cbe6f2b23a680943c3',
                'drink' => '5279a88767b96daa4b000029',
                'bms' => '51ef8224222aad6011000092',
                'manager' => '52b73fa3e6f2b24e690943b2'
        );

        $thistag = $arr_station_asset_id[$station_type];
        $product = JTProduct::findFirst(array(array('_id'=>new \MongoId($product_id))))->toArray();
        $arr_sync = array(
                        '51ef8224222aad6011000092'=>'bms',
                        '5279a88767b96daa4b000029'=>'drink',
                        '52b738cbe6f2b23a680943c3'=>'kitchen',
                        '52b73fa3e6f2b24e690943b2' => 'manager'
                    );
        $arr_tag_key = array();
        if(isset($product['production_step'])){
            foreach ($product['production_step'] as $key => $value){
                if($value['deleted']==true || (string)$value['tag_key']=='52b73fa3e6f2b24e690943b2')
                    continue;
                if(isset($arr_sync[(string)$value['tag_key']])){
                    $arr_tag_key[(string)$value['tag_key']]='production';
                }
            }
        }
        $order = JTOrder::findFirst(
                    array(
                        array('_id'=> new \MongoId($order_id))
                    )
                );
        $check_complete_order = true;
        if(isset($order->cart) && isset($order->cart['items'])){
            $arr_item = array();
            $thiskey = '';

            foreach ($order->cart['items'] as $key => $value){
                if($key==$cart_id){ //tach item dang xet
                    $arr_item = $order->cart['items'][$key];
                    $thiskey = $key;
                }else if(!isset($order->cart['items'][$key]['completed'])){
                    $check_complete_order = false;
                }
            }

            //xu ly item dang xet
            if(!isset($arr_item['multi_compl']) || count($arr_item['multi_compl'])!=count($arr_tag_key)){
                $arr_item['multi_compl'] = $arr_tag_key;
            }
                        
            $arr_item['multi_compl'][$thistag] = 'completed';
            $JTTask = new \RW\Models\JTTask;
            $JTTask->completed_task_by_order($order_id,$thistag,$order->code);
            $check_complete_item = true;
            foreach ($arr_item['multi_compl'] as $key => $value) {
                if($value!='completed'){
                    $check_complete_item = false;
                }
            }
            if($check_complete_item){
                $arr_item['completed'] = 1;
            }else{
                $check_complete_order = false;
            }
            // pr($arr_tag_key);
            // pr($arr_item);die;
            //gan item lai vao order
            $order->cart['items'][$thiskey] = $arr_item;
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
    public function completeAllAction(){
        $this->view->disable();
        $order_id = $this->request->getPost('order_id');
        $is_manager = $this->request->getPost('is_manager');
        $order = JTOrder::findFirst(
                    array(
                        array('_id'=> new \MongoId($order_id))
                    )
                );

        if(isset($order->cart)&&isset($order->cart['items'])){
            foreach ($order->cart['items'] as $key => $value){
                $order->cart['items'][$key]['completed'] = 1;
            }
        }
        $order->completed = 1;
        $order->status = 'Completed';
        $order->status_id = 'Completed';
        $order->asset_status = 'Completed';
        
        //complete tasks
        $JTTask = new \RW\Models\JTTask;
        $arr_station_asset_id = array(
                'kitchen' => '52b738cbe6f2b23a680943c3',
                'drink' => '5279a88767b96daa4b000029',
                'bms' => '51ef8224222aad6011000092',
                'manager' => '52b73fa3e6f2b24e690943b2'
        );
        foreach ($arr_station_asset_id as $key => $value) {
            $JTTask->completed_task_by_order($order_id, $value, $order->code);            
        }
        

        $arr_on_account = array('On Account','Multipay');
        if(isset($is_manager) && $is_manager=='1'){
            // $order->paid_by = "Had paid";
            $order->manager_completed = 1;
        }

        if($order->save()){
            echo 1;
        }else{
            echo 0;
        }

    }
    public function completeTaskAction(){
        $task_id = $this->request->getPost('task_id');
        (new \RW\Models\JTTask)->completed_kichen_task($task_id);
        $this->view->disable(); echo 1;
    }
    public function changeStatusAction(){
        $this->view->disable();
        $order_id = $this->request->getPost('order_id');
        $product_id = $this->request->getPost('product_id');
        $status = $this->request->getPost('status');
        $order = JTOrder::findFirst(array(array('_id'=>new \MongoId($order_id))));
        if($order){
            if($status == 'Cancelled'){
                $order->delete();                
            }else{
                $order->status = $status;
                $order->asset_status = $status;
                $order->save();
            }
        }
    }
    public function doubleTaskAction(){
        if($this->request->isAjax() && $this->request->hasPost("task_id")){
            $task_id = $this->request->getPost('task_id');
            (new \RW\Models\JTTask)->create_task_kitchen($task_id);
            $this->view->disable();
        }
    }
    public function collapseExpandAction(){
        if($this->request->isAjax() && $this->request->hasPost("order_id")){
            $order_id = $this->request->getPost('order_id');
            $arr_collapse = $this->session->has('arr_collapse') ? $this->session->get('arr_collapse') : array();
            $arr_expand = $this->session->has('arr_expand') ? $this->session->get('arr_expand') : array();
            $type = $this->request->hasPost("type")?trim($this->request->getPost("type")):'Collapse';
            if($type=='Collapse'){
                $arr_collapse[$order_id] = $order_id;
                if(isset($arr_expand[$order_id]))
                    unset($arr_expand[$order_id]);
            }else{
                $arr_expand[$order_id] = $order_id;
                if(isset($arr_collapse[$order_id]))
                    unset($arr_collapse[$order_id]);
            }
            $this->session->set('arr_collapse', $arr_collapse);
            $this->session->set('arr_expand', $arr_expand);
            $this->view->disable();
        }
    }    

}
