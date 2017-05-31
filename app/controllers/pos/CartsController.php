<?php
namespace RW\Controllers\Pos;

use RW\Models\Carts;
use RW\Models\JTProduct;
use RW\Models\JTCompany;
use RW\Models\JTProvince;
use RW\Cart\Cart;

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
    	$this->view->partial('pos/Carts/index');        
        $this->view->setViewsDir($this->view->getViewsDir() . '/pos/');
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
                                            'fields'    => ['name', 'description']
                                        ]);
            $cartKey = $this->cart->add([
                            '_id'       => new \MongoId($main['_id']),
                            'name'      => $data['name'],
                            'description'=> $data['description'],
                            'note'      => $main['note'],
                            'image'     => $main['image'],
                            'quantity'  => $main['quantity'],
                            'options'   => $options,
                            'sell_price' => $data['sell_price'],
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
                            ]
                        ];
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
        $this->view->disable();
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->taxlist = $this->taxlist();
        // pr($items);die;
        $this->view->partial('pos/blocks/view_cart', array('cart' => $items, 'baseURL' => URL));
    }
    public function smallCartAction(){
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $this->view->disable();
        $this->view->cart = (new \RW\Cart\Cart)->get();
        $this->view->baseURL = URL;
        $this->view->partial('pos/blocks/small_cart');
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
            return $this->response->redirect('/pos');
        }
    }

    public function getOptionItemAction(){
        $this->view->disable();
        $productController = new \RWControllersPos\ProductsController();
        $cart = $this->cart->get($this->request->getPost('cartKey'));
        $arr_option =  $productController->optionAction((string)$cart['_id'],1);
        
        $this->view->arr_group = $arr_option['arr_group'];
        $this->view->option = $arr_option['option'];
        $this->view->option_price_total = $arr_option['option_price_total'];
        $this->view->baseURL = URL;
        $this->view->cart_id = $arr_option['cart_id'];
        $this->view->finish_option = $arr_option['finish_option'];
        $this->view->partial('pos/Categories/option_product');
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

    public function printAction(){
        $arr_pro =array();
        $items = (new \RW\Cart\Cart)->get();
        foreach ($items['items'] as $key => $value) {
            if(isset($value['options']))
                foreach ($value['options'] as $kk => $op) {
                    if($op['option_type']!='')
                    $arr_pro[$op['_id']] = $op['option_type'];
                }
        }
        $this->view->disable();
        $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
        $this->view->taxlist = $this->taxlist();
        $this->view->cart = $items;
        $this->view->baseURL = URL;
        // pr($items);die;
        $this->view->partial('pos/blocks/print');
    }

    public function clearCartAction()
    {
        if (!$this->request->isAjax()) {
            return $this->error404();
        }
        $save_order =  $this->request->getPost('save_order');
        if($save_order == 'yes'){
            $ordersController = new \RWControllersPos\OrdersController();
            $ordersController->is_process=='call_from_edit';
            $ordersController->createOrderAction();
            $ordersController->is_process=='';
        }
        $this->cart->destroy();
        return $this->response(['error' => 0]);
    }

}
