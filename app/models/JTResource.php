<?php
namespace RW\Models;

class JTResource extends MongoBase {

    public function getSource(){
        return 'tb_resource';
    }
    public function  task_list_by_user($asset_type){
    	$arr_sync = array(
                        'bms'=>'51ef8224222aad6011000092',
                        'drink'=>'5279a88767b96daa4b000029',
                        'kitchen'=>'52b738cbe6f2b23a680943c3',
                        'online'=>'52b738cbe6f2b23a680943c3',
                        'manager' => '52b73fa3e6f2b24e690943b2'
                    );
    	$query = self::find([
            'conditions'  => [
                'deleted'      	=> false,
                'module'		=> "Task",
                'item_id'      	=> new \MongoId($arr_sync[$asset_type])
            ],
            'fields' => ['_id', 'module_id', 'status_id'],
            'sort'      => ['code' => -1]
        ]);
        $arr_return = array();
        foreach ($query as $key => $value){
            if(is_object($value)){
                $value = $value->toArray();
            }
        	$task_id = (string)$value['module_id'];
        	if(!in_array($task_id, $arr_return))
        		$arr_return[] = $task_id;
        }
        return $arr_return;
    }
    
}

