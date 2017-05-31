<?php
namespace RW\Controllers\Pos;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use RW\Models\Categories;
use RW\Models\Orders;
use RW\Models\OrdersItems;
use RW\Models\Products;
use RW\Models\Users;
class ControllerBase extends Controller
{
    protected $response;
    protected $orders;
    protected $model;
    protected $ordersItems;
    public function indexAction(){        
    }
    public function initialize()
    {        
        $this->response = new \Phalcon\Http\Response;

        //Auto load Model class    
        $modelClass = 'RW\\Models\\Products';
        
        $this->model = new $modelClass;
        // default menu
        $this->view->menu = Categories::find([
            'columns'   => 'id, name, short_name',
            'order'     => 'order_no ASC',
            'conditions' => "position = 1 "
        ]);
        $arr_order_list = Orders::find([
            'columns'   => 'id, created_at,userName,customerName,code,totalPrice,totalTax, description',
            'order'     => 'id desc',
            'conditions' => "deleted = 0 and type = 1 and status = 1 "
        ]);
        $this->session->set("current-order-list", $arr_order_list->toArray()); 
        $this->view->baseURL = URL;

        $this->orders = new OrdersController;
        $this->orderItemss = new OrdersItemsController;
        $arr_order = Orders::getTotalOrder();        

        if(!$arr_order){
            $arr_order = $this->orders->createPosOrder();            
            $arr_order['items'] = [];
            if(!$arr_order){
                echo 'Error';
                die;
            }
        }
        $this->session->set("current-order", $arr_order['id']);
        $this->session->set("current-close-register",$this->_closeRegister());
        $this->view->currentOrder = $arr_order;
        $this->getPOSdata();        
    }
    public function _closeRegister(){
        return Orders::_SumOrdersRegister();
    }
    public function redrawProductListAction(){
        echo $this->drawOneProduct();
        die;
    }
    function getPOSdata(){
        $this->view->currentCategory = 'All';        
        $arr_list_items = $this->view->currentOrder['items'];
        $this->view->arrListItem = $arr_list_items;
        $total_price = 0;
        if(!empty($arr_list_items)){            
            for($i=0;$i<count($arr_list_items);$i++){
                if((int)$arr_list_items[$i]['deleted'] == 1) continue;
                $total_price += (float)$arr_list_items[$i]['price'] * (int)$arr_list_items[$i]['quantity'];
            }
        }
        $tax = $total_price/10.0;
        $this->view->subtotal = display_format_currency($total_price);
        $this->view->hiddensubtotal = $total_price;
        $this->view->tax = display_format_currency($tax);
        $this->view->total = display_format_currency($total_price+$tax);
        $this->view->topay = display_format_currency($total_price+$tax);
        $this->view->listItems = json_encode($this->getListItem(['name','price']));
        $this->view->listUsers = json_encode(Users::getListUserActive());
    }
    public function getData(){
        return $this->listRecords(false,['id', 'name', 'image','price'],null,true);
    }
    public function drawOneProduct($_category = ''){
        $this->view->orderList = $this->session->get("current-order-list");
        $this->view->_closeRegister = $this->session->get("current-close-register");
        $arr_products =  $this->getData($_category);        
        $arr_product = $arr_products['data'];
        $this->view->TotalPage = $arr_products['totalPage'];        
        $v_return = '';
        for($i=0;$i<count($arr_product);$i++){
            $v_return .='<div class="block" onclick="addPosProduct(this)" data-price="'.$arr_product[$i]['price'].'">';
                $v_return .='<span>';
                    $v_return .= $arr_product[$i]['name'];
                $v_return .='</span>';
            $v_return .='</div>';
        }
        return $v_return;
    }
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
    }
    public function response($responseData = [], $responseCode = 200, $responseMessage = '', $responseHeader= [])
    {
        $this->response->setStatusCode($responseCode, $responseMessage)
                            ->setJsonContent($responseData);
        if (!empty($responseHeader)) {
            foreach($responseHeader as $headerName => $headerValue) {
                $this->response->setHeader($headerName, $headerValue);
            }
        }
        return $this->response;
    }
    public function getListItem($columns = [],$tostring = false){
        $categoryName = str_replace('/', '', $this->dispatcher->getParam('paramsList'));
        if($categoryName){
            $category = Categories::findFirstByShortName($categoryName);
            if($category && $category->products) $data =  $category->products;
        }else{
            $conditions = [];
            $data = $this->model->find([
                'conditions' => $conditions,           
                'columns'    => $columns
            ]);
        }        
        $return = [];
        if (isset($data)) {
            $data = $data->toArray();
            if($tostring){
                foreach($data as $key => $value) {
                    $return [$key] = $value; 
                }
            }else{
                foreach($data as $key => $value) {
                    $string = '';
                    for($i=0;$i<count($columns);$i++){
                        $temp = $value[$columns[$i]];
                        if($columns[$i] =='price'){
                            $temp = display_format_currency($value[$columns[$i]]);
                        }
                        $string .=$temp.($i!=count($columns)-1 ? ' - ':'');
                    }
                    $return [] = $string;
                }
            }
        } else {
            $return = [];
        }
        return $return;
    }
    public function listRecords($ajax=true,$columns = [], $arrayHandle = null,$limit = true)
    {
        $filter = new \Phalcon\Filter;
        $pageSize = _POS_PAGE_SIZE_;
        $arr_post = $this->getPost();    
        $data = array_merge([
                'search'     => [],
                'pagination' => [
                    'pageNumber' => 1,
                    'pageSize'   => $pageSize,
                    'sort'       => 'asc',
                    'sortName'   => 'id'
                ]
            ], $arr_post);
        if(isset($arr_post['pageNumber'])) {
            $pageNumber = (int)$arr_post['pageNumber'];
            $data['pagination']['pageNumber'] = $pageNumber;
        }
        if(!$limit) unset($data['pagination']);
        $conditions = [];
        $bind = [];
        foreach($data['search'] as $fieldName => $value) {
            if (is_numeric($value)) {
                if (is_int($value)) {
                    $value = $filter->sanitize($value, 'int');
                } else if (is_float($value)) {
                    $value = $filter->sanitize($value, 'float');
                }
                $conditions[] = "{$fieldName}= :{$fieldName}:";
                $bind[$fieldName] = $value;
            } else if (is_string($value)) {
                $value = $filter->sanitize($value, 'string');
                $conditions[] = "{$fieldName} LIKE :{$fieldName}:";
                $bind[$fieldName] = '%'.$value.'%';
            }
        }
        $conditions = implode(' AND ', $conditions);
        if ($limit && is_string($data['pagination']['sortName'])) {
            $order = $data['pagination']['sortName'] .' '. $data['pagination']['sort'];
        } else {
            $order = 'id desc';
        }
        $offset = 0;
        $pageNumber = 1;
        if($limit){            
            $limit = is_numeric($data['pagination']['pageSize']) ? $data['pagination']['pageSize'] : 100;
            $pageNumber = isset($data['pagination']['pageNumber']) ? (int)$data['pagination']['pageNumber'] : 1;            
            $offset = ceil( ($pageNumber-1) * $limit);
        }
        $total = $this->model->count([
                                        'conditions' => $conditions,
                                        'bind'       => $bind,
                                    ]);
        $data = $this->model->find([
                'conditions' => $conditions,
                'bind'       => $bind,
                'order'      => $order,
                'columns'    => $columns,
                'limit'      => $limit,
                'offset'     => $offset
            ]);

        if ($data) {
            $data = $data->toArray();
            $arrayHandleCallable = is_callable($arrayHandle);
            foreach($data as $key => $value) {
                if ($arrayHandleCallable) {
                    $value = $arrayHandle($value);
                }
                $data[$key] = $value;
            }
        } else {
            $data = [];
        }
        if($ajax) return $this->response(['error' => 0, 'data' => $data, 'total' => $total]);
        else return [
                'data'=>$data
                ,'totalPage'=>$this->drawPagination($pageNumber,$total/$pageSize)
            ];
    }
    public function drawPagination($page,$totalPage,$categoryName='All'){
        $_return = '';
        if($totalPage>1){
            $_return .= '<ul class="pagination">';
                $_return .= '<li class="prev" id="">';
                    $_return .= '<a href="javascript:void(0);" onclick=paginate("'.$categoryName.'","p") aria-label="Previous">';
                        $_return .= '<span aria-hidden="true"><i class="glyphicon glyphicon-triangle-left"></i></span>';
                        $_return .= '<input type="hidden" id="currentPage" value="'.$page.'" />';
                        $_return .= '<input type="hidden" id="maxPage" value="'.$totalPage.'" />';
                    $_return .= '</a>';
                $_return .= '</li>';
                for($i=1;$i<=$totalPage;$i++){
                    $_return .= '<li id="p'.($i+1).'" class="'.($i==$page ? "active liCurrentActive":"").'"><a href="javascript:void(0);" onclick=paginate("'.$categoryName.'","'.$i.'")>'.$i.'</a></li>';
                }                
                $_return .= '<li class="next">';
                  $_return .= '<a href="javascript:void(0);" onclick=paginate("'.$categoryName.'","n") aria-label="Next">';
                    $_return .= '<span aria-hidden="true"><i class="glyphicon glyphicon-triangle-right"></i></span>';
                  $_return .= '</a>';
                $_return .= '</li>';
            $_return .='</ul>';
        }
        return $_return;
    }
    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
        if (!$this->request->isAjax()) {
            $this->assets
                    ->collection('css')                    
                    ->addCss('/'.THEMEPOS.'/css/bootstrap.css')
                    ->addCss('/'.THEMEPOS.'/css/style.css')
                    ->addCss('/'.THEMEPOS.'/js/jquery/lib/angular/css/autocomplete.css')
                    ->addCss('/'.THEMEPOS.'/js/jquery/lib/alert/css/jAlert-v3.css')                    
                    ->setSourcePath(PUBLIC_PATH)
                    ->setTargetPath(PUBLIC_PATH . DS .THEMEPOS.'/css/pos.min.css')
                    ->setTargetUri(THEMEPOS.'/css/pos.min.css')
                    ->join(true)
                    ->addFilter(new \Phalcon\Assets\Filters\Cssmin());
             $this->assets
                    ->collection('js')
                    ->addJs('/'.THEMEPOS.'/js/jquery/core/jquery.min.js')
                    ->addJs('/'.THEMEPOS.'/js/core/bootstrap.min.js')
                    ->addJs('/'.THEMEPOS.'/js/action/main.js')
                    ->addJs('/'.THEMEPOS.'/js/jquery/lib/angular/js/angular.min.js')                    
                    // ->addJs('/'.THEMEPOS.'/js/jquery/lib/angular/js/autocomplete.js')
                    // ->addJs('/'.THEMEPOS.'/js/jquery/lib/angular/js/app.js')
                    ->addJs('/'.THEMEPOS.'/js/jquery/lib/alert/js/jAlert-v3.js')
                    ->addJs('/'.THEMEPOS.'/js/jquery/lib/alert/js/jAlert-functions.js')
                    ->setSourcePath(PUBLIC_PATH)
                    ->setTargetPath(PUBLIC_PATH. DS .THEMEPOS.'/js/pos.min.js')
                    ->setTargetUri(THEMEPOS.'/js/pos.min.js')
                    ->join(true)
                    ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
            $this->view->baseURL = URL;
            $this->view->themeURL = THEMEPOS;
        }
        $this->view->setViewsDir($this->view->getViewsDir() . '/pos/');
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
                    $message = 'This page this not exists. Please go back to homepage.';
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
}
