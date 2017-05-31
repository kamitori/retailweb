<?php
namespace RW\Controllers\Pos;
use RW\Models\Categories;
use RW\Models\Products;
class PosController extends ControllerBase
{
    public function indexAction()
    {       
    }
    public function menusAction(){        
        $this->view->ListProducts = $this->drawOneProduct();
    }
    public function redrawProductListAction(){
        $data = $this->getPost();     
        echo $this->drawOneProduct((isset($data['txt_name'])?$data['txt_name']:'All'));
        die;
    }
    public function getData($_category = ''){        
        // cho fix routter
        $pageNumber = 1;
        $pageSize = _POS_PAGE_SIZE_;
        $data = $this->getPost();
        if(isset($data['pageNumber'])) $pageNumber = $data['pageNumber'];
        $offset = $pageNumber * $pageSize;
        $total = 1;

        $categoryName = str_replace('/', '', $this->dispatcher->getParam('paramsList'));
        if($_category) $categoryName = $_category;
        $this->view->currentCategory = $categoryName;
        $category = Categories::findFirstByShortName($categoryName);               
        $products = [];        
        if ($category) {
            $products =  $category->products;            
            $category = $category->toArray();
            if ($category['image']) {
                $category['image'] = URL.'/'.$category['image'];
            } else {
                $category['image'] = '';
            }            
            if ($products) {                
                $productsCheck = $products->toArray();

                $total = count($products);                
                
                $run = 1;
                $products = [];
                foreach($productsCheck as $key => $product) {
                    
                    if(($run<$offset && $pageNumber>1) || $run > ($offset+$pageSize)) {
                        $run ++;
                        continue;
                    }
                    if(count($products) >= $pageSize) break;                    
                    if ($product['image']) {
                        $productsCheck[ $key ]['image'] = URL.'/'.$product['image'];
                    } else {
                        $productsCheck[ $key ]['image'] = '';
                    }
                    $products [] = $productsCheck[ $key ];
                    $run ++;
                }
            } else {
                $products = [];
            }
        }
        return [
                'data'=>$products
                ,'totalPage'=>$this->drawPagination($pageNumber,$total/$pageSize,$categoryName)
            ];
    }
}
