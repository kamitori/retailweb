<?php
namespace RW\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use RW\Models\JTProvince;
use RW\Models\Categories;
use RW\Models\Configs;
use RW\Models\JTProduct;
use RW\Models\JTDocuse;
use RW\Models\JTDoc;
use RW\Models\JTSettings;

class ControllerBase extends Controller
{
    protected $response;

    public function initialize(){
        $this->response = new \Phalcon\Http\Response;
        // echo $this->security->hash('123456');die;
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher){
        $controllerName = $dispatcher->getControllerName();
        $actionName = $dispatcher->getActionName();        
        if (!$this->session->has('user')
            && $controllerName != 'Users'
            && !in_array($actionName, ['loginpos', 'logout','cart','customerDisplay'])){
            $dispatcher->forward(array(
                'namespace'     => 'RW\Controllers',
                'controller'    => 'Users',
                'action'        => 'loginpos'
            ));
            return false;
        }

        $this->view->baseURL = URL;
        $this->view->theme = THEME;
        $this->view->JT_URL = JT_URL;
    }

    public function afterExecuteRoute(Dispatcher $dispatcher){
        if (!$this->request->isAjax()) {
            /*$this->assets
                    ->collection('css')
                    ->addCss('/bower_components/bootstrap/dist/css/bootstrap.min.css')
                    ->addCss('/bower_components/font-awesome/css/font-awesome.css')
                    ->addCss('/'.THEME.'css/font.css')
                    ->addCss('/'.THEME.'css/main.css')
                    ->addCss('/'.THEME.'css/custom.css')
                    ->addCss('/'.THEME.'css/datetimepicker.css')
                    ->setSourcePath(PUBLIC_PATH)
                    ->setTargetPath(PUBLIC_PATH.DS.THEME.'/css/app.min.css')
                    ->addCss('/'.THEMEPOS.'/js/jquery/lib/alert/css/jAlert-v3.css')
                    ->addCss('/'.THEMEPOSCASH.'css/currentTableCss.css')

                    ->setTargetUri('/'.THEME.'css/app.min.css')
                    ->join(true)
                    ->addFilter(new \Phalcon\Assets\Filters\Cssmin());*/

             // $this->assets
             //        ->collection('js')
             //        ->addJs('/bower_components/jquery/dist/jquery.min.js')
             //        ->addJs('/bower_components/jquery-ui/jquery-ui.min.js')
             //        ->addJs('/bower_components/bootstrap/dist/js/bootstrap.min.js')
             //        ->addJs('/bower_components/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js')
             //        ->addJs('/bower_components/iscroll/build/iscroll.js')
             //        ->addJs('/bower_components/moment/min/moment.min.js')
             //        ->addJs('/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')
             //        ->addJs('/js/idle-timer.min.js')
             //        ->addJs('/'.THEMEPOS.'/js/jquery/lib/alert/js/jAlert-v3.js')
             //        ->addJs('/'.THEMEPOS.'/js/jquery/lib/alert/js/jAlert-functions.js')
             //        ->addJs('/'.THEME.'js/load.js')
             //        ->addJs('/'.THEME.'js/function.js')
             //        ->addJs('/'.THEME.'js/pricing.js')
             //        ->addJs('/'.THEME.'js/common.js')
             //        // ->addJs('/'.THEME.'js/main.js')
             //        ->setSourcePath(PUBLIC_PATH)
             //        ->setTargetPath(PUBLIC_PATH.DS.THEME.'/js/app.min.js')
             //        ->setTargetUri('/'.THEME.'js/app.min.js')
             //        ->join(true)
             //        ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
            
        }

        // $this->view->baseURL = URL;
        // $this->view->theme = THEME;
        // $this->view->JT_URL = JT_URL;

        $session_user = $this->session->has('user')?$this->session->get('user'):false;
        $session_user['session_order'] = 0;
        if(file_exists("cookies_id.json")){
            $arr_cookies_id = json_decode(file_get_contents("cookies_id.json"),true);
            $cookies_order_pos = trim($this->cookies->get('cookies_order_pos')->getValue());
            if(count($arr_cookies_id)>0)
            foreach ($arr_cookies_id as $key => $value) {
                if(trim((string)$value)==$cookies_order_pos){
                    $session_user['session_order'] = $key;
                    break;
                }
            }
        }
        $this->view->session_user = $session_user;
        $this->view->session_location = $this->session->has('location')?$this->session->get('location'):false;

        $this->view->provinces = JTProvince::find(array(
                                                array("country_id" => "CA"),
                                                "sort" => array("name" => 1)
                                            ));
        $this->view->setViewsDir($this->view->getViewsDir() . '/frontend/');
        $this->view->cart = (new \RW\Cart\Cart)->get();
        $this->view->use_combo = (new \RW\Cart\Cart)->checkcombo();
        $this->view->combo_step = (new \RW\Cart\Cart)->combostep();
        $this->view->next_uid = (new \RW\Cart\Cart)->next_uid();
        $this->view->step_description = array('0'=>'','1'=>'pick sub', '2'=>'pick appetizer','3'=>'pick drink');
        $this->view->use_group = (new \RW\Cart\Cart)->checkgroup();
        $this->view->user_name = (new \RW\Cart\Cart)->user_group_now();

        $this->view->mainmenu = $this->SettingMenu();
        $this->view->link = $this->router->getRewriteUri();
        $shortname = $this->dispatcher->getParam('categoryName');
        $arr_cate= $this->JTCategorySimple();
        $this->view->off_main_menu = 0;
        $this->view->autoload = 0;
        if($this->view->link=="/bms-station"){
            $this->view->title = 'Banh mi SUB Station';
            $this->view->off_main_menu = 1;
            $this->view->autoload = 1;
        }
        else if($this->view->link=="/drink-station"){
            $this->view->title = 'Drinks Station';
            $this->view->off_main_menu = 1;
            $this->view->autoload = 1;
        }
        else if($this->view->link=="/kitchen-station" || $this->view->link=="/kitchen"){
            $this->view->title = 'Kitchen';
            $this->view->off_main_menu = 1;
            $this->view->autoload = 1;
        }
        else if($this->view->link=="/manager"){
            $this->view->title = 'Manager';
            $this->view->off_main_menu = 1;
            $this->view->autoload = 1;
        }
        else if($this->view->link=="/online-station"){
            $this->view->title = 'Online Orders';
            $this->view->off_main_menu = 1;
            $this->view->autoload = 1;
        }
        else if($this->view->link=="/orders/account_order")
            $this->view->title = 'Payment On Account Order';
        else if($this->view->link=="/orders/adjustment_order")
            $this->view->title = 'Adjustment of Orders';
        else if($this->view->link=="/orders/daily_history"){
            $this->view->title = 'Daily Order History';
            $codeOrder = $this->request->hasPost("codeOrder")?$this->request->getPost("codeOrder"):'';
            $this->view->codeOrder = $codeOrder;
        }
        else if(isset($arr_cate[$shortname]))
            $this->view->title = $arr_cate[$shortname];
        else
            $this->view->title = 'Main menu';
        $this->view->taxlist = $this->taxlist();
        $this->view->arr_task = (new \RW\Models\JTTask)->pos_task();
        
    }

    public function getDatetimeFormated()
    {
        $arr_option = (new \RW\Models\JTSettings)->type('time_zone')->get();
        // pr($arr_option);exit;
        $default_timezone = 'Canada/Mountain';
        $different_minutes = 0;
        if(is_array($arr_option))
        {
            foreach ($arr_option as $key => $value) {
                if ($value['deleted'])
                    continue;
                if($value['name'] == 'default_timezone')
                {
                    $default_timezone = $value['value'];
                }
                elseif($value['name'] == 'different_minutes')
                {
                    $different_minutes = $value['value'];
                }
            }                
        }    
        date_default_timezone_set($default_timezone);
        $exact_time = time() + intval($different_minutes)*60;
        $date_format = date('Y-m-d H:i:s', $exact_time);
        // echo $date_format;
        return $date_format;
    }

    public function taxlist(){
        return array(0=>'0%',5=>'5%',12=>'12%',13=>'13%',14=>'14%',15=>'15%');
    }
    public function SettingMenu(){
        $menu = array();
        $menu[0]['up'] = 'Order';
        $menu[0]['down'] = 'Now';
        $menu[0]['link'] = '/';

        $menu[1]['up'] = 'Select';
        $menu[1]['down'] = 'Location';
        $menu[1]['link'] = '#location';

        $menu[2]['up'] = 'Repeat';
        $menu[2]['down'] = 'Last Order';
        $menu[2]['link'] = '/last-order';

        $menu[3]['up'] = 'Saved';
        $menu[3]['down'] = 'Favourites';
        $menu[3]['link'] = '/favourites';

        $menu[4]['up'] = 'Sign';
        $menu[4]['down'] = 'in';
        $menu[4]['link'] = '#signin';

        $menu[5]['up'] = 'Create';
        $menu[5]['down'] = 'Account';
        $menu[5]['link'] = '#create-account';
        return $menu;
    }

    final function getPost()
    {
        $postJSON = $this->request->getRawBody();
        if (!empty($postJSON)) {
            $postJSON = json_decode($postJSON, true);
        } else {
            $postJSON = [];
        }
        return array_merge($postJSON, $this->request->getPost());
    }

    final function abort($code = 404, $title = '', $message = '')
    {
        switch ($code) {
            case 404:
                if (empty($title)) {
                    $title = 'Page not found';
                    $message = 'This page did not exist. Please go back to homepage.';
                }
                $this->dispatcher->forward(array(
                    'controller'    => 'Errors',
                    'action'        => 'notFound',
                ));
                break;
        }
        $data = [
            'title'     => $title,
            'message'   => $message
        ];
        $this->dispatcher->setParams(['data' => $data]);
    }
    public function productByCategory($cate_name='', $arr_condition = array()){
        $where = array();

        if(isset($arr_condition['or']))
        {
            foreach($arr_condition as $key=>$value)
            {
                if($key == 'or') continue;
                $where['conditions']['$or'][] = array($key=>$value);
            }
        }
        else
        {
            foreach($arr_condition as $key=>$value)
            {
                $where['conditions'][$key] = $value;    
            }
        }
    
        if($cate_name!=''){
            $where['conditions']['$or'][] = array('category'=>$cate_name);
            $where['conditions']['$or'][] = array('cate_more'=>$cate_name);
        }
        $where['conditions']['assemply_item'] =1;
        $where['conditions']['deleted'] =false;
        $where['conditions']['status'] =1;
        $where['sort'] = array('name'=>1);
        $where['limit'] = 100;
        $jtproduct = JTProduct::find($where);
        $products = $product_id = $product_tag = $arr_have_price= $arr_opid = array();
        foreach ($jtproduct as $key => $value) {
            $products[$key]['name'] = $value->name;
            $products[$key]['description'] = isset($value->description)?$value->description:'';
            $products[$key]['product_desciption'] = isset($value->product_desciption)?$value->product_desciption:'';
            $products[$key]['id'] = (string)$value->_id;
            //find option defaut price
            // if(isset($value->options)){
            //     foreach ($value->options as $kk => $vv){
            //         if($vv['deleted']==true)
            //             continue;
            //         $adjustment_amount = isset($vv['adjustment'])?(float)$vv['adjustment']:0;
            //         $unit_price = isset($vv['unit_price'])?$vv['unit_price']:0;

            //         if($vv['require']==1 && isset($vv['product_id']) && isset($vv['quantity'])){
            //             $arr_have_price[$key][] = array('product_id'=>$vv['product_id'],'quantity'=>$vv['quantity'],'adjustment'=>$adjustment_amount,'unit_price'=>$unit_price);
            //             if(!in_array($vv['product_id'],$arr_opid))
            //                 $arr_opid[] = $vv['product_id'];
            //         }else if(isset($vv['default']) && $vv['default'] == 1 && isset($vv['finish']) && $vv['finish']==1){
            //             $arr_have_price[$key][] = array('product_id'=>$vv['product_id'],'quantity'=>$vv['quantity'],'adjustment'=>$adjustment_amount,'unit_price'=>$unit_price);
            //             if(!in_array($vv['product_id'],$arr_opid))
            //                 $arr_opid[] = $vv['product_id'];
            //         }
            //     }
            // }
            $products[$key]['price'] = round($value->sell_price,2);
            $products[$key]['image'] = '';
            $products[$key]['category_id'] = $value->category;
            
            // if($value->category=='sub_combo')
            if($value->category=='Sub Combo') //test
                $products[$key]['combo'] = 1;
            else
                $products[$key]['combo'] = 0;

            if(isset($value->use_group_order)) //test
                $products[$key]['use_group_order'] = $value->use_group_order;
            else
                $products[$key]['use_group_order'] = 0;
            
            $products[$key]['combo_sales'] = isset($value->combo_sales)?(float)$value->combo_sales:1;

            if(isset($value->product_base)&&!empty($value->product_base)){
                $products[$key]['tag'] = $value->product_base;
                $product_tag[$products[$key]['tag']] = $products[$key]['tag'];
            }else
                $products[$key]['tag'] = 'Other';

            $products[$key]['custom'] = 0;
            if((isset($value->pricebreaks) && count($value->pricebreaks)>0) || (isset($value->options) && count($value->options)>0)){
                $products[$key]['custom'] = 1;
            }

            $products[$key]['image'] = '';
            if(isset($value->products_upload)){
                $products[$key]['image'] = $value->products_upload;
                foreach ($value->products_upload as $kk => $vv) {
                   if($vv['deleted']==false && $vv['path']!='' ){
                        $products[$key]['image'] = JT_URL.$vv['path'];
                        // break;
                   }
                }
            }
        }
        $where = array();
        $where['conditions']['_id'] = array('$in'=>$arr_opid);
        $where['conditions']['deleted'] =false;
        $where['sort'] = array('_id'=>1);
        $where['field'] = array('_id','sell_price');
        $where['limit'] = 200;
        $opproduct = JTProduct::find($where);
        
        foreach ($opproduct as $key => $value) {
            $sell_price[(string)$value->_id] = (float)$value->sell_price;
        }
        foreach ($arr_have_price as $key => $oplist) {
            foreach ($oplist as $kk => $value) {
                if(isset($sell_price[(string)$value['product_id']])){
                    $op_price = ($value['unit_price']!=0)?$value['unit_price']:$sell_price[(string)$value['product_id']];
                    $products[$key]['price'] = round($products[$key]['price'] + $op_price * $value['quantity'] + $value['adjustment'],2);
                    // echo $products[$key]['price'].'<br>';
                }
            }
        }
        // die;
        // pr($arr_have_price);pr($sell_price);die;
        // pr($products);die;
        $product_tag['Other'] = 'Other';
        return array('product_list'=>$products,'tag_list'=>$product_tag);
    }
    public function JTCategorySimple(){
        $category = array();
        $query = Categories::findFirst(array(
            'conditions'=>array('setting_value'=>'product_category')
        ));
        if(isset($query->option) && !empty($query->option))
            foreach ($query->option as $key => $value) {
                if (isset($value['hidden_on_pos']) && $value['hidden_on_pos']=="1") {
                    continue;
                }                                
                if(isset($value['pubblic']) && $value['pubblic']==1)
                    $category[strtolower(str_replace(" ","_",$value['value']))] = $value['name'];
            }
       return $category;
    }
    public function JTCategory(){
        $category = array();
        $query = Categories::findFirst(array(
            'conditions'=>array('setting_value'=>'product_category')
        ));
        if(isset($query->option) && !empty($query->option)){
            foreach ($query->option as $key => $value) {
                if (isset($value['hidden_on_pos']) && $value['hidden_on_pos']=="1") {
                    continue;
                }                
                if(isset($value['pubblic']) && $value['pubblic']==1 && isset($value['deleted']) && $value['deleted']==false){
                    $short_name = strtolower(str_replace(" ","_",$value['value']));
                    $category[$short_name]['name'] = $value['name'];
                    $category[$short_name]['value'] = $value['value'];
                    $category[$short_name]['short_name'] = strtolower(str_replace(" ","_",$value['value']));
                    if(!isset($value['order_no']))
                        $category[$short_name]['order_no'] = 1;
                    else
                        $category[$short_name]['order_no'] = (int)$value['order_no'];

                    if(isset($value['image']) && $value['image']!='')
                        $category[$short_name]['image'] = $value['image'];
                    else
                        $category[$short_name]['image'] = 'http://pos.banhmisub.com/images/product-categories/l_518856232_banh-mi-xa-xiu.18-10-15.png';
                }
            }
            $category = aasort($category,'order_no',1);
            // pr($category);die;
        }
       return $category;
    }

    final function response($responseData = [], $responseCode = 200, $responseMessage = '', $responseHeader= [])
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8')
                            ->setStatusCode($responseCode, $responseMessage)
                            ->setJsonContent($responseData);
        if (!empty($responseHeader)) {
            foreach($responseHeader as $headerName => $headerValue) {
                $this->response->setHeader($headerName, $headerValue);
            }
        }
        return $this->response;
    }

    protected function error404($message = 'Page not found')
    {
        return $this->response(['error' => 1, 'message' => $message], 404, $message);
    }

}
