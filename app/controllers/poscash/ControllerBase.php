<?php
namespace RW\Controllers\Poscash;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use RW\Models\Categories;

class ControllerBase extends Controller
{
    protected $response;

    public function initialize()
    {
        $this->response = new \Phalcon\Http\Response;
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {

        $controllerName = $dispatcher->getControllerName();

        $identity = $this->auth->getIdentity();

        // If there is no identity available the admin is redirected to index/index
        if (!is_array($identity) && !in_array(strtolower($controllerName), ['auth'])) {
            $dispatcher->forward(array(
                'namespace'     => 'RW\Controllers\Poscash',
                'controller'    => 'Auth',
                'action'        => 'index'
            ));
            $this->view->baseURL = URL;
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
                    ->addCss('/'.THEMEPOSCASH.'css/currentTableCss.css')   
                    ->addCss('/'.THEMEPOSCASH.'css/poscash.css')
                    ->setSourcePath(PUBLIC_PATH)
                    ->setTargetPath(PUBLIC_PATH.DS.THEMEPOSCASH.'/css/poscash.min.css')
                    ->setTargetUri('/'.THEMEPOSCASH.'css/poscash.min.css')
                    ->join(true)
                    ->addFilter(new \Phalcon\Assets\Filters\Cssmin());
             $this->assets
                    ->collection('js')
                    ->addJs('/bower_components/jquery/dist/jquery.min.js')
                    ->addJs('/bower_components/jquery-ui/jquery-ui.min.js')
                    ->addJs('/bower_components/bootstrap/dist/js/bootstrap.min.js')
                    ->addJs('/bower_components/iscroll/build/iscroll.js')
                    ->addJs('/bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js')
                    ->addJs('/'.THEME.'js/main.js')                    
                    ->addJs('/'.THEMEPOSCASH.'/js/poscash.js')
                    ->setSourcePath(PUBLIC_PATH)
                    ->setTargetPath(PUBLIC_PATH. DS .THEMEPOSCASH.'/js/poscash.min.js')
                    ->setTargetUri('/'.THEMEPOSCASH.'/js/poscash.min.js')
                    ->join(true)
                    ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
            $this->view->baseURL = URL;
            $this->view->theme = THEME;
            $this->view->themeURL = THEMEPOSCASH;
        }
        $this->view->session_user = $this->session->has('user')?$this->session->get('user'):false;
        $this->view->session_location = $this->session->has('location')?$this->session->get('location'):false;
        
        $this->view->setViewsDir($this->view->getViewsDir() . '/poscash/');

        $this->view->mainmenu = $this->SettingMenu();
        $cart_items = array(); $amount = $total_price = 0;
        if ($this->session->has("cart_items"))
            $cart_items = $this->session->get("cart_items");
        if($this->session->has("amount"))
            $amount = (string)$this->session->get("amount");
        if($this->session->has("total_price"))
            $total_price = (string)$this->session->get("total_price");
        $this->view->cart_items = $cart_items;
        $this->view->amount = $amount;
        $this->view->total_price = $total_price;
        $this->view->link = $this->router->getRewriteUri();
    }

    public function SettingMenu(){
        $menu = array();
        $menu[0]['up'] = 'Product';
        $menu[0]['down'] = 'List';
        $menu[0]['link'] = '/poscash/';

        $menu[1]['up'] = 'Add New';
        $menu[1]['down'] = 'Sale';
        $menu[1]['link'] = '/poscash/orders';

        $menu[2]['up'] = 'Current';
        $menu[2]['down'] = 'Sales';
        $menu[2]['link'] = '/poscash/orders/current';

        $menu[3]['up'] = 'Retrieve';
        $menu[3]['down'] = 'Sales';
        $menu[3]['link'] = '/poscash/orders/retrieve';

        $menu[4]['up'] = 'Saves';
        $menu[4]['down'] = 'Reports';
        $menu[4]['link'] = '/poscash/pos/reports';

        $menu[5]['up'] = 'Setting';
        $menu[5]['down'] = 'System';
        $menu[5]['link'] = '/poscash/pos/setting';

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
        if(!is_null($postJSON)) return array_merge($postJSON, $this->request->getPost());
        return $this->request->getPost();
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
        $where['sort'] = array('name'=>1);
        $where['limit'] = 100;
        $jtproduct = JTProduct::find($where);
        $products = $product_id = array();
        foreach ($jtproduct as $key => $value) {
            $products[$key]['name'] = $value->name;
            $products[$key]['description'] = isset($value->product_desciption)?$value->product_desciption:'';
            $products[$key]['id'] = (string)$value->_id;
            $products[$key]['price'] = round($value->sell_price,2);
            $products[$key]['image'] = '';
            $products[$key]['category_id'] = $value->category;
            $products[$key]['custom'] = 0;
            if((isset($value->pricebreaks) && count($value->pricebreaks)>0) || (isset($value->options) && count($value->options)>0)){
                $products[$key]['custom'] = 1;
            }
            if($value->products_upload && isset($value->products_upload[0]['path']))
                $products[$key]['image'] = JT_URL.$value->products_upload[0]['path'];
            else
                $products[$key]['image'] = '';
        }
        return $products;
    }
    public function JTCategorySimple(){
        $category = array();
        $query = Categories::findFirst(array(
            'conditions'=>array('setting_value'=>'product_category')
        ));
        if(isset($query->option) && !empty($query->option))
            foreach ($query->option as $key => $value) {
                if(isset($value['pubblic']) && $value['pubblic']==1)
                    $category[$value['value']] = $value['name'];
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

}