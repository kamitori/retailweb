<?php
namespace RW\Controllers;

use RW\Models\Products;
use RW\Models\JTStuffs;

class ServiceController extends ControllerBase
{

    public function indexAction()
    {
    	
    }

    function syncDataToServerAction(){

        define('MONGODB_SERVER', 'jt');

        if(URL != "http://retailweb.com"){
            echo 'Please sync from local.';
            die;
        }

        $path = PUBLIC_PATH.DS.'mongodump';

        if( !file_exists($path) ){
            mkdir($path, 0777, true);
        }

        clean_directory($path);

        $path .= DS.'mongodump-'.(date('y.m.d'));

        if( !file_exists($path) ) {
            mkdir($path, 0777, true);
        }

        $where = array();
        $where['conditions']['name'] = 'Last_sync_date';
        $where['conditions']['deleted'] =false;
        // $where['sort'] = array('_id'=>1);
        $where['field'] = array('_id','sync_time', 'name', 'date_modified');
        $lastSyncs = JTStuffs::findFirst($where);

        $lastSync = array();
        if( !$lastSyncs ) {
            $lastSync = [
                    'name' => 'Last_sync_date',
                    'sync_time' => 0,
                    'date_modified' => new \MongoDate()
            ];
        } else {
            $lastSync = $lastSyncs->toArray();
        }

        $lastSync['sync_time'] = isset($lastSync['sync_time']) ? $lastSync['sync_time'] : 0;
        $lastSync['date_modified'] = isset($lastSync['date_modified']) ? $lastSync['date_modified'] : new \MongoDate();

        $arr_collections = array('\RW\Models\JTCompany' => 'tb_company',
                                    '\RW\Models\JTContact' => 'tb_contact',
                                    '\RW\Models\JTOrder' => 'tb_salesorder',
                                    '\RW\Models\JTProduct' => 'tb_product',
                                    '\RW\Models\JTQuotation' => 'tb_quotation',
                                    '\RW\Models\JTSalesaccount' => 'tb_salesaccount',
                                    '\RW\Models\JTSettings' => 'tb_settings',
                                    '\RW\Models\JTTask' => 'tb_task',
                                    '\RW\Models\JTTax' => 'tb_tax',
                                    '\RW\Models\JTUser' => 'tb_user',
                                    '\RW\Models\JTJob' => 'tb_job',
                                    '\RW\Models\JTProvince' => 'tb_province',
                                    '\RW\Models\JTStuffs' => 'tb_stuffs'
            );
        foreach($arr_collections as $key => $collection) {
            
            $firstNewRecord = $key::findFirst(array('field' => array('_id'),
                                                'conditions' => array('date_modified' => [ '$gt' => $lastSync['date_modified'] ]),
                                                'sort'=>array('_id'=>1)
                                    ));
            
            // pr($firstNewRecord);exit;
            if($firstNewRecord){
                $firstNewRecord = $firstNewRecord->toArray();
                $query = '{ \'_id\' : { \'$gte\' : ObjectId(\''.$firstNewRecord['_id'].'\') } }';
                if( DS == '/' ) {
                    $query = '{ \'_id\' : { \$gte : ObjectId(\''.$firstNewRecord['_id'].'\') } }';
                }
                //dump db in current machine
                $command = 'mongodump -u sysadmin -p serCurity!2017 --port 27017 -d '.JT_DB.' -q "'.$query.'" -c '.$collection.' -o '.$path;
                exec($command);
                // echo 'command:'.$command.'<br/>';
                if( is_file($path.DS.JT_DB.DS.$collection.'.bson') ){
                    //restore into db in current machine
                    $command = "mongorestore -h 167.114.209.179 -u sysadmin -p serCurity!2017 -d ".MONGODB_SERVER." -c $collection {$path}".DS.JT_DB.DS."$collection.bson";
                    exec($command);
                    // echo 'command:'.$command.'<br/>';
                }
            }

        }

        $lastSync['sync_time']++;
        $lastSync['date_modified'] = new \MongoDate();

        if(!$lastSyncs){
            $lastSyncs = new JTStuffs();
        }
        $lastSyncs->sync_time = $lastSync['sync_time'];
        $lastSyncs->name = $lastSync['name'];
        $lastSyncs->date_modified = $lastSync['date_modified'];
        $lastSyncs->save();
        
        $arrData = [
                    'event'     => 'DBSync',
                    'message'   => 'Database has been synchronized to the server.',
                    'status'    => 'success',
                    'updated_at'    => date('d M, y H:i', $lastSync['date_modified']->sec),
                    'updated_time'  => number_format($lastSync['sync_time'])
                ];
        // pr($arrData);
        // die;
        $this->view->disable();
        //Create a response instance
        $response = new \Phalcon\Http\Response();
        //$response->setContentType('application/json', 'UTF-8');
        //Set the content of the response
        $response->setContent(json_encode($arrData));
        $response->send();
    }

    function syncDataFromServerAction(){

        define('MONGODB_SERVER', 'jt');

        if(URL != "http://retailweb.com"){
            echo 'Please sync from local.';
            die;
        }

        $path = PUBLIC_PATH.DS.'mongodump';

        if( !file_exists($path) ){
            mkdir($path, 0777, true);
        }

        clean_directory($path);

        $path .= DS.'mongodump-'.(date('y.m.d'));

        if( !file_exists($path) ) {
            mkdir($path, 0777, true);
        }

        $where = array();
        $where['conditions']['name'] = 'Last_sync_date';
        $where['conditions']['deleted'] =false;
        // $where['sort'] = array('_id'=>1);
        $where['field'] = array('_id','sync_time', 'name', 'date_modified');
        $lastSyncs = JTStuffs::findFirst($where);

        $lastSync = array();
        if( !$lastSyncs ) {
            $lastSync = [
                    'name' => 'Last_sync_date',
                    'sync_time' => 0,
                    'date_modified' => new \MongoDate()
            ];
        } else {
            $lastSync = $lastSyncs->toArray();
        }

        $lastSync['sync_time'] = isset($lastSync['sync_time']) ? $lastSync['sync_time'] : 0;
        $lastSync['date_modified'] = isset($lastSync['date_modified']) ? $lastSync['date_modified'] : new \MongoDate();
        
        $arr_collections = array('\RW\Models\JTCompany' => 'tb_company',
                                    '\RW\Models\JTContact' => 'tb_contact',
                                    '\RW\Models\JTOrder' => 'tb_salesorder',
                                    '\RW\Models\JTProduct' => 'tb_product',
                                    '\RW\Models\JTQuotation' => 'tb_quotation',
                                    '\RW\Models\JTSalesaccount' => 'tb_salesaccount',
                                    '\RW\Models\JTSettings' => 'tb_settings',
                                    '\RW\Models\JTTask' => 'tb_task',
                                    '\RW\Models\JTTax' => 'tb_tax',
                                    '\RW\Models\JTUser' => 'tb_user',
                                    '\RW\Models\JTJob' => 'tb_job',
                                    '\RW\Models\JTProvince' => 'tb_province',
                                    '\RW\Models\JTStuffs' => 'tb_stuffs'
            );
        foreach($arr_collections as $key => $collection) {
            
            $lastRecord = $key::findFirst(array('field' => array('_id'),
                                                            'sort'=>array('_id'=>-1)
                                    ))->toArray();
            
            // pr($lastRecord);exit;
            $query = '{ \'_id\' : { \'$gt\' : ObjectId(\''.$lastRecord['_id'].'\') } }';
            if( DS == '/' ) {
                $query = '{ \'_id\' : { \$gt : ObjectId(\''.$lastRecord['_id'].'\') } }';
            }
            //dump jobtraq db on the SERVER
            $command = 'mongodump -h 167.114.209.179 -u sysadmin -p serCurity!2017 --port 27017 -d '.MONGODB_SERVER.' -q "'.$query.'" -c '.$collection.' -o '.$path;
            exec($command);
            // echo 'command:'.$command.'<br/>';
            if( is_file($path.DS.MONGODB_SERVER.DS.$collection.'.bson') ){
                //restore into db in current machine
                exec("mongorestore -u sysadmin -p serCurity!2017 -d ".JT_DB." -c $collection {$path}".DS.MONGODB_SERVER.DS."$collection.bson");
            }

        }

        $lastSync['sync_time']++;
        $lastSync['date_modified'] = new \MongoDate();

        /*if(!$lastSyncs){
            $lastSyncs = new JTStuffs();
        }        
        $lastSyncs->sync_time = $lastSync['sync_time'];
        $lastSyncs->name = $lastSync['name'];
        $lastSyncs->date_modified = $lastSync['date_modified'];
        $lastSyncs->save();*/
        
        $arrData = [
                    'event'     => 'DBSync',
                    'message'   => 'Database has been synchronized to the current machine.',
                    'status'    => 'success',
                    'updated_at'    => date('d M, y H:i', $lastSync['date_modified']->sec),
                    'updated_time'  => number_format($lastSync['sync_time'])
                ];
        // pr($arrData);
        // die;

        $this->view->disable();
        //Create a response instance
        $response = new \Phalcon\Http\Response();
        //$response->setContentType('application/json', 'UTF-8');
        //Set the content of the response
        $response->setContent(json_encode($arrData));
        $response->send();
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

