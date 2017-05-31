<?php
namespace RW\Controllers\Services;

use RW\Models\Products;
use RW\Models\JTProduct;

class IndexController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function listProductsAction()
    {
        $products = JTProduct::find(array(
                                    array("status" => 1, "deleted" => false)
                                ));

        $arr_result = [];
        $data = [];
        
        foreach ($products as $key => $value) 
        {                        
            $product = [];

            //Get image
            $products_upload = isset($value->products_upload) ? $value->products_upload : array();
            $path = '';
            foreach($products_upload as $image)
            {
                if(!$image['deleted'])
                {
                    $path = $image['path'];
                }
            }
            $product['image'] = JT_URL.$path;

            //Get price
/*            $sellprices = isset($value->sellprices) ? $value->sellprices : array();
            $price = 0.00;
            foreach($sellprices as $sp)
            {
                if(!$sp['deleted'] && $sp['sell_default'])
                {
                    $price = $sp['sell_unit_price'];
                }
            }
            $product['price'] = $price;*/

            $product['price'] = $value->sell_price;
            
            $product['id'] = (string)$value->_id;
            $product['name'] = $value->name;
            $product['description'] = $value->description;

            $product['category_name'] = $value->category;
            
/*            $product['category_id'] = $value->category_id;
            $product['category_name'] = '';
            $product['category_image'] = '';
            $product['category_description'] = '';
            $cat = $value->categories;
            if($cat)
            {
                $product['category_name'] = $cat->name; 
                $product['category_image'] = URL.'/'.$cat->image;  
                $product['category_description'] = $cat->description;
            }
*/            
            $data[(string)$value->_id] = $product;
            
        }
        $arr_result['datasupdate'] = $data;
        
        //Write to products.json file
        $myFile = "datas/products.json";
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, json_encode($arr_result));
        fclose($fh);

        $this->view->disable();

        $callback = $this->request->get('callback');
        if($callback != null)
        {
            header("Content-Type: application/json; charset=utf-8");
            echo $callback.'('.json_encode($arr_result).')';
            exit;
        }

        //Create a response instance
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'UTF-8');

        //Set the content of the response
        $response->setContent(json_encode($arr_result));

        //return $response;     
        $response->send();
    }

/*    public function listProductsAction()
    {
        $products = Products::find();

        $arr_result = [];
        $arr_result['last_update'] = '';
        $arr_result['mac_address'] = '';
        $arr_result['mode'] = '';
        $arr_result['type'] = '';
        $data = [];
        
        foreach ($products as $key => $value) 
        {
        	$product = [];
        	$product['short_name'] = $value->short_name;
        	$product['extension'] = substr($value->image, strrpos($value->image, '.', -1)+1);
        	$product['image'] = URL.'/'.$value->image;
        	$product['price'] = $value->price;
        	$product['id'] = $value->id;
        	$product['name'] = $value->name;
            $product['description'] = $value->description;
            $product['category_id'] = $value->category_id;
            $product['category_name'] = '';
            $product['category_image'] = '';
            $product['category_description'] = '';
            $cat = $value->categories;
            if($cat)
            {
                $product['category_name'] = $cat->name; 
                $product['category_image'] = URL.'/'.$cat->image;  
                $product['category_description'] = $cat->description;
            }
            $data[$value->id] = $product;
            
        }
        //pr($data);
        $arr_result['datasupdate'] = $data;
        
        //Write to products.json file
        $myFile = "datas/products.json";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, json_encode($arr_result));
		fclose($fh);

        $this->view->disable();

        $callback = $this->request->get('callback');
        if($callback != null)
        {
	        header("Content-Type: application/json; charset=utf-8");
	        echo $callback.'('.json_encode($arr_result).')';
	        exit;
        }

        //Create a response instance
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'UTF-8');

    	//Set the content of the response
        $response->setContent(json_encode($arr_result));

        //return $response;    	
        $response->send();
    }*/
}

