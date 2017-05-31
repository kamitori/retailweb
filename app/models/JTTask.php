<?php
namespace RW\Models;

class JTTask extends MongoBase {

    public function getSource(){
        return 'tb_task';
    }
    protected static function getCode($field = 'code'){
	    $lastTask = self::findFirst([
	        'sort'   => [$field => -1],
	        'fields' => [$field]
	    ]);
	    $code = isset($lastTask[$field]) ? $lastTask[$field] : 0;
	    $code++;
	    return $code;
	}
	public function create_task_kitchen($orid=''){
	    $ortask = self::findFirst([
	                        'conditions'  => [
	                            'deleted'       => false,
	                            '_id'           => new \MongoId($orid),
	                        ],
	                        'sort'   => ['_id' => -1]
	                    ])->toArray();

	    if(!empty($ortask)){
	        unset($ortask['_id']);

	        if(isset($ortask['pos_task'])){
	            $ortask['pos_task'] = 0;
	        }
	        $ortask['no'] = self::getCode('no');
	        $ortask['is_kitchen'] = 1;
	        $ortask['our_rep_type'] = 'assets';
	        // $ortask['our_rep_id'] = new \MongoId('52b738cbe6f2b23a680943c3');
	        // $ortask['our_rep'] = 'Kitchen';
	        $ortask['status'] = 'New';
	        $ortask['status_id'] = 'New';

	        $this->__default($ortask);
	        $this->getConnection()->{$this->getSource()}->insert($ortask);
	    }
	}
	public static function pos_task(){
		$arrTask =  self::find([
            'conditions' => [
                'deleted' => false,
                'pos_task'=> 1
            ],
            'fields' => ['name', 'no', 'type', 'status'],
            'sort'   => ['_id' => -1]
        ]);
        $return = array();
        if($arrTask){
        	foreach ($arrTask as $key => $value) {
        		if($value){
        			// $value = $value->toArray();
        			$value = returnArray($value) ;
        			$value['_id'] = (string)$value['_id'];
	        		$return[] = $value;
        		}
	        	
	        }
        }
        return $return;
	}

	public function completed_kichen_task($task_id=''){
		if($task_id!=''){
			$arrSave = array();
			$arrSave = self::findFirst([
	                        'conditions'  => [
	                            'deleted'       => false,
	                            '_id'           => new \MongoId($task_id),
	                        ],
	                        'sort'   => ['_id' => -1]
	                    ])->toArray();
			$arrSave['status'] = 'DONE';
			$arrSave['status_id'] = 'DONE';
			$this->getConnection()->{$this->getSource()}->update(array('_id'=>new \MongoId($task_id)), $arrSave,array('upsert' => true));
		}
	}

	public function completed_task_by_order($order_id='',$asset_id='',$order_no=''){
		if($order_id!=''){
			$arrTask = array();
			$conditions = array(
								'deleted'=> false,
	                            'salesorder_id' => new \MongoId($order_id)
	                           );
			if($asset_id!=''){
				$conditions['our_rep_id'] =  new \MongoId($asset_id);
			}
			$arrTask = self::find([
	                        'conditions'  => $conditions,
	                        'sort'   => ['_id' => -1]
	                    ]);
			$arrSave = array();
			if(empty($arrTask)){
				$arr_sync = array(
                        '51ef8224222aad6011000092'=>'Prep Station',
                        '5279a88767b96daa4b000029'=>'Drinks station',
                        '52b738cbe6f2b23a680943c3'=>'Kitchen',
                        '52b73fa3e6f2b24e690943b2' => '01. Manager'
                    );
				$ortask['no'] = self::getCode('no');
		        $ortask['is_kitchen'] = 0;
		        $ortask['type'] = 'Production';
		        $ortask['type_id'] = 'Production';
		        $ortask['our_rep_type'] = 'assets';
		        $ortask['our_rep_id'] = new \MongoId($asset_id);
		        $ortask['our_rep'] = $arr_sync[$asset_id];
		        $ortask['name'] = $order_no.' - Retail Customer - '.$ortask['our_rep'];
		        $ortask['status'] = 'DONE';
		        $ortask['status_id'] = 'DONE';
		        $ortask['salesorder_id'] = new \MongoId($order_id);
		        $this->__default($ortask);
		        $this->getConnection()->{$this->getSource()}->insert($ortask);
			}else{
				foreach ($arrTask as $key => $value){
					$arrSave = $value->toArray();
					$arrSave['status'] = 'DONE';
					$arrSave['status_id'] = 'DONE';
					$this->getConnection()->{$this->getSource()}->update(array('_id'=>$arrSave['_id']), $arrSave,array('upsert' => true));
				}
			}
			
		}
	}

	protected function getDefault(){
        return [
                'deleted'           => 'bool',
                'pos_task'          => ['default' => 0],
                'is_kitchen'     	=> ['default' => 0],
                'no'            => 'string',
        ];
    }
}

