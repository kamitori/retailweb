<?php
namespace RW\Controllers\Pos;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use RW\Models\JTProvince;
use RW\Models\Categories;
use RW\Models\Configs;
use RW\Models\JTProduct;
use RW\Models\JTDocuse;
use RW\Models\JTDoc;

class ControllerBase extends Controller
{
    protected $response;

    public function initialize()
    {
        $this->response = new \Phalcon\Http\Response;
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        
        $this->view->baseURLPos = URL.'/pos';        

        $controllerName = $dispatcher->getControllerName();
        $actionName = $dispatcher->getActionName();
        if (!$this->session->has('user')
            && $controllerName != 'Users'
            && !in_array($actionName, ['loginpos', 'logout','cart'])){
            $dispatcher->forward(array(
                'namespace'     => 'RW\Controllers\Pos',
                'controller'    => 'Users',
                'action'        => 'loginpos'
            ));
            return false;
        }
    }

    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
        if (!$this->request->isAjax()) {
            $this->assets
                    ->collection('css')
                    ->addCss('/bower_components/bootstrap/dist/css/bootstrap.min.css')
                    ->addCss('/bower_components/font-awesome/css/font-awesome.css')
                    ->addCss('/'.THEME.'css/font.css')
                    ->addCss('/'.THEME.'css/main.css')
                    ->addCss('/'.THEME.'css/custom.css')
                    ->setSourcePath(PUBLIC_PATH)
                    ->setTargetPath(PUBLIC_PATH.DS.THEME.'/css/app.min.css')
                    ->addCss('/'.THEMEPOS.'/js/jquery/lib/alert/css/jAlert-v3.css')
                    ->addCss('/'.THEMEPOSCASH.'css/currentTableCss.css')
                    // ->addCss('/'.THEMEPOSCASH.'css/poscash.css')
                    ->setTargetUri('/'.THEME.'css/app.min.css')
                    ->join(true)
                    ->addFilter(new \Phalcon\Assets\Filters\Cssmin());
             $this->assets
                    ->collection('js')
                    ->addJs('/bower_components/jquery/dist/jquery.min.js')
                    ->addJs('/bower_components/jquery-ui/jquery-ui.min.js')
                    ->addJs('/bower_components/bootstrap/dist/js/bootstrap.min.js')
                    ->addJs('/bower_components/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js')
                    ->addJs('/bower_components/iscroll/build/iscroll.js')
                    ->addJs('/js/idle-timer.min.js')
                    ->addJs('/'.THEMEPOS.'/js/jquery/lib/alert/js/jAlert-v3.js')
                    ->addJs('/'.THEMEPOS.'/js/jquery/lib/alert/js/jAlert-functions.js')
                    ->addJs('/'.THEME.'js/load.js')
                    ->addJs('/'.THEME.'js/function.js')
                    ->addJs('/'.THEME.'js/pricing.js')
                    ->addJs('/'.THEME.'js/common.js')
                    // ->addJs('/'.THEME.'js/main.js')
                    ->setSourcePath(PUBLIC_PATH)
                    ->setTargetPath(PUBLIC_PATH.DS.THEME.'/js/app.min.js')
                    ->setTargetUri('/'.THEME.'js/app.min.js')
                    ->join(true)
                    ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
            $this->assets->collection('pageJS')->addJs('/'.THEME.'js/facebook_login.js');
            $this->view->baseURL = URL;            
            $this->view->theme = THEME;
            $this->view->JT_URL = JT_URL;
        }

        $this->view->session_user = $this->session->has('user')?$this->session->get('user'):false;
        $this->view->session_location = $this->session->has('location')?$this->session->get('location'):false;
        $this->view->provinces = JTProvince::find(array(
                                                array("country_id" => "CA"),
                                                "sort" => array("name" => 1)
                                            ));
        $this->view->setViewsDir($this->view->getViewsDir() . '/pos/');
        $this->view->cart = (new \RW\Cart\Cart)->get();
        $this->view->mainmenu = $this->SettingMenu();
        $this->view->link = $this->router->getRewriteUri();
        $shortname = $this->dispatcher->getParam('categoryName');
        $arr_cate= $this->JTCategorySimple();
        if($this->view->link=="/pos/bms-station")
            $this->view->title = 'Banh mi SUB Station';
        else if($this->view->link=="/pos/drink-station")
            $this->view->title = 'Drinks Station';
        else if($this->view->link=="/pos/orders/account_order")
            $this->view->title = 'Payment On Account Order';
        else if($this->view->link=="/pos/orders/adjustment_order")
            $this->view->title = 'Adjustment of Orders';
        else if($this->view->link=="/pos/orders/daily_history"){
            $this->view->title = 'Daily Order History';
            $codeOrder = $this->request->hasPost("codeOrder")?$this->request->getPost("codeOrder"):'';
            $this->view->codeOrder = $codeOrder;
        }
        else if(isset($arr_cate[$shortname]))
            $this->view->title = $arr_cate[$shortname];
        else
            $this->view->title = 'Main menu';
        $this->view->taxlist = $this->taxlist();
    }

    public function taxlist(){
        return array(0=>'0%',5=>'5%',12=>'12%',13=>'13%',14=>'14%',15=>'15%');
    }
    public function SettingMenu(){
        $menu = array();
        $menu[0]['up'] = 'Order';
        $menu[0]['down'] = 'Now';
        $menu[0]['link'] = '/pos';

        $menu[1]['up'] = 'Select';
        $menu[1]['down'] = 'Location';
        $menu[1]['link'] = '#location';

        $menu[2]['up'] = 'Repeat';
        $menu[2]['down'] = 'Last Order';
        $menu[2]['link'] = '/pos/last-order';

        $menu[3]['up'] = 'Saved';
        $menu[3]['down'] = 'Favourites';
        $menu[3]['link'] = '/pos/favourites';

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
    public function productByCategory($cate_name=''){
        $where = array();
        if($cate_name!='')
            $where['conditions']['category'] =$cate_name;
        $where['conditions']['assemply_item'] =1;
        $where['conditions']['deleted'] =false;
        $where['sort'] = array('name'=>1);
        $where['limit'] = 100;
        $jtproduct = JTProduct::find($where);
        $products = $product_id = $product_tag = array();
        foreach ($jtproduct as $key => $value) {
            $products[$key]['name'] = $value->name;
            $products[$key]['description'] = isset($value->product_desciption)?$value->product_desciption:'';
            $products[$key]['id'] = (string)$value->_id;
            $products[$key]['price'] = round($value->sell_price,2);
            $products[$key]['image'] = '';
            $products[$key]['category_id'] = $value->category;
            if(isset($value->product_base)&&!empty($value->product_base)){
                $products[$key]['tag'] = $value->product_base;
                $product_tag[$products[$key]['tag']] = $products[$key]['tag'];
            }else
                $products[$key]['tag'] = 'Other';

            $products[$key]['custom'] = 0;
            if((isset($value->pricebreaks) && count($value->pricebreaks)>0) || (isset($value->options) && count($value->options)>0)){
                $products[$key]['custom'] = 1;
            }

            $products[$key]['image'] = $value->products_upload;
            if(isset($value->products_upload)){
               foreach ($value->products_upload as $kk => $vv) {
                   if($vv['deleted']==false && $vv['path']!='' ){
                        $products[$key]['image'] = JT_URL.$vv['path'];
                        // break;
                   }
               }
            }
        }
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
