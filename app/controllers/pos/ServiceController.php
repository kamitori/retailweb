<?php
namespace RW\Controllers\Pos;

use RW\Models\Products;

class ServiceController extends ControllerBase
{

    public function indexAction()
    {
    	
    }

    public function listProductsAction()
    {
        $products = Products::find()->toArray();

        $arr_result = [];
        $arr_result['lastUpdate'] = '';
        $arr_result['mac_address'] = '';
        $arr_result['mode'] = '';
        $arr_result['type'] = '';
        $data = [];
        foreach ($products as $key => $value) 
        {
        	$product = [];
        	$product['short_name'] = $value['short_name'];
        	$product['extension'] = substr($value['image'], strrpos($value['image'], '.', -1)+1);
        	$product['image'] = URL.'/'.$value['image'];
        	$product['price'] = isset($value['price']) ? $value['price'] : '';
        	$product['id'] = $value['id'];
        	$product['name'] = $value['name'];
        	$data[$value['id']] = $product;
        }
        $arr_result['datasupdate'] = $data;
        
        //Write to products.json file
        $myFile = "datas/products.json";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, json_encode($arr_result));
		fclose($fh);

        $this->view->disable();

        //Create a response instance
        $response = new \Phalcon\Http\Response();
        //$response->setContentType('application/json', 'UTF-8');

    	//Set the content of the response
        $response->setContent('('.json_encode($arr_result).')');


        $callback = $this->request->get('callback');
        if($callback != null)
        {
	        header("Content-Type: application/json; charset=utf-8");
	        echo $callback.'('.json_encode($arr_result).')';
	        exit;
        }

        //return $response;    	
        $response->send();
    }

}

