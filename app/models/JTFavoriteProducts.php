<?php
namespace RW\Models;

class JTFavoriteProducts extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_favorite_products';
    }
    public static function findListById($user){    	
    	if(empty($user)){
    		$_ip = get_client_ip();
    		$_datas = JTFavoriteProducts::find(
                array(
                    array(
                        'ip'=>$_ip,
                        'deleted'=>false
                    )
                )
            );
    	}else{
    		$_datas = JTFavoriteProducts::find(
                array(
                    array(
                        'user_id'=>$user['user_id'],
                        'deleted'=>false
                    )
                )
            );
    	}
    	$_return = array();
    	foreach($_datas as $_key => $_val){
    		$_val = $_val->toArray();
    		$_return [$_key] = $_val;
    	}
    	return $_return;
    }
}
