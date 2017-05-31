<?php
namespace RW\Controllers\Pos;

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
            $this->view->partial('pos/Categories/product_left', array('products' => $data['product_list'], 'tag_list'=>$data['tag_list'], 'baseURL' => URL));
        }else{
            $categoryName = $this->dispatcher->getParam('categoryName');
            $categories = $this->JTCategory();
            if (isset($categories[$categoryName])) {
                $this->view->category = $categories;
                $data = $this->productByCategory($categories[$categoryName]['value']);
                $this->view->products = $data['product_list'];
                $this->view->tag_list = $data['tag_list'];
            } else {
                return $this->abort(404);
            }
        }
    }
    

}
