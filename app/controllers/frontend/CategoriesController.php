<?php
namespace RW\Controllers;

use RW\Models\Categories;
use RW\Models\JTSettings;
// use RW\Models\Products;
// use RW\Models\JTProduct;
// use RW\Models\JTDocuse;
// use RW\Models\JTDoc ;

class CategoriesController extends ControllerBase {

    public function indexAction(){
        if($this->request->isAjax()){
            $cate_name = $this->request->getPost("cate_name");
            $this->view->disable();
            $data = $this->productByCategory($cate_name);
            $this->view->partial('frontend/Categories/product_left', array('products' => $data['product_list'], 'tag_list'=>$data['tag_list'], 'baseURL' => URL));
        }else{
            $categoryName = $this->dispatcher->getParam('categoryName');
            
            //
            $iscombo = (new \RW\Cart\Cart)->checkcombo();
            $combostep = (new \RW\Cart\Cart)->combostep();
            if($iscombo==1 && $combostep==1){
                $categoryName = 'banh_mi_subs';
            }
            if($iscombo==1 && $combostep==2){
                $categoryName = 'appetizers';
            }
            if($iscombo==1 && $combostep==3){
                $categoryName = 'drinks';
            }
            // echo $categoryName;die;
            $categories = $this->JTCategory();
            if (isset($categories[$categoryName])) {
                $this->view->category = $categories;
                
                $arr_condition = array();
                $key = $this->request->get('key');
                if($key != null && trim($key) != '')
                {                    
                    $arr_condition['or'] = true;
                    $arr_condition['name'] = array('$regex'=>$key, '$options'=>'-i');
                    $arr_condition['description'] = array('$regex'=>$key, '$options'=>'-i');                    
                }

                $data = $this->productByCategory($categories[$categoryName]['value'], $arr_condition);
                $this->view->products = $data['product_list'];
                $this->view->tag_list = $data['tag_list'];
            } else {
                return $this->abort(404);
            }
        }
    }
    

}
