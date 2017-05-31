<?php
namespace RW\Controllers;

use RW\Models\Products;
use RW\Models\JTProduct;

class ProductsController extends ControllerBase
{
    public function indexAction(){
    	echo 'erere';die;
    }
    public function calpriceAction(){
    	$arr_result = $arr_data = array();

    	$arr_result = JTProduct::calPrice($arr_data);

    	//return json ajax
    	$this->view->disable();
    	$response = new \Phalcon\Http\Response();
		$response->setStatusCode(200, 'OK');
		$response->setContentType('application/json', 'UTF-8');
		$response->setContent(json_encode($arr_result));
		return $response;
    }
    public function optionAction($id,$return=0){
    	if($id!=''){
	    	$where = $option= $arr_proid = $arr_group = $arr_pro = $is_default = array();
	        $where['conditions']['_id'] = new \MongoId($id);
	        $where['conditions']['assemply_item'] =1;
	        $jtproduct = JTProduct::findFirst($where);
	        $option_price_total = $max_choice = 0;
            $description = '';
            if(isset($jtproduct->maximum_option)){
                $max_choice = $jtproduct->maximum_option;
            }
            
	        if(isset($jtproduct->options)){
	        	foreach ($jtproduct->options as $key => $value){
                    if(!isset($value['hidden']))
                        $value['hidden'] = 0;
                    if(!isset($value['default']))
                        $value['default'] = 0;
	        		if($value['deleted']==false && $value['hidden']!=1){
	        			$value['key'] = $key;
	        			$group = (isset($value['option_group']) && $value['option_group']!='')?$value['option_group']:'all';
	        			if(!isset($value['require']) || $value['require']==0)
	        				$value['quantity'] = 0;
                        if(isset($value['finish'])){
                            $value['quantity'] = (int)$value['finish'];
                        }
                        $value['default_qty'] = $value['quantity'];
                        if(!isset($value['level']) || isset($value['level'])=='')
                            $value['level']='Level 01';
                        if(!isset($value['group_order']) || isset($value['group_order'])=='')
                            $value['group_order']=1;
                        
                        $arr_group[$value['level']][GroupToKey($group)]['label'] = GroupToName($group);
                        if(isset($arr_group[$value['level']][GroupToKey($group)]['order']) && (int)$value['group_order']<$arr_group[$value['level']][GroupToKey($group)]['order'])
                            $arr_group[$value['level']][GroupToKey($group)]['order'] = (int)$value['group_order'];
                        else
                            $arr_group[$value['level']][GroupToKey($group)]['order'] = (int)$value['group_order'];

                        $group = GroupToKey($group);
                        $option[$value['level']][$group][(string)$value['product_id']] = $value;
                        $option[$value['level']][$group][(string)$value['product_id']]['product_id'] = (string)$value['product_id'];
                        
	        			$arr_proid[] = $value['product_id'];
	        			$arr_group_product[(string)$value['product_id']] = $group;
                        if($value['default']==1 && isset($value['finish']) && $value['finish']==1)
                            $is_default[(string)$value['product_id']] = 1;
	        		}
	        	}
	        	$query = JTProduct::find(array(
		            'conditions'=>array('_id'=>array('$in'=>$arr_proid))
		        ));
		        foreach ($query as $key => $value){
		        	$value->image = '';
		        	if(isset($value->products_upload) && !empty($value->products_upload)){
		        		foreach ($value->products_upload as $k => $v) {
		        			if($v['deleted']==false){
		        				$value->image = JT_URL.$v['path'];
		        			}
		        		}
		        	}
		        	$id = (string)$value->_id;
                    if(isset($value->option_type))
                        $arr_pro[$id] = $value->option_type;
                    else
                        $value->option_type = '';
                    if(isset($is_default[$id])){
                        $description .='<p id="od_'.$id.'">'.$value->name.' (<b>'.'Yes'.'</b>) </p>';
                    }
                    foreach ($option as $level => $arr_value) {
                        if(isset($option[$level][$arr_group_product[$id]][$id])){
                            $option[$level][$arr_group_product[$id]][$id] = $item = array_merge($value->toArray(),$option[$level][$arr_group_product[$id]][$id]);
                            $adjustment = isset($item['adjustment'])?$item['adjustment']:0;
                            $option_price_total += $item['quantity']*($item['sell_price']+$adjustment);
                        }
                    }
		        }
	        }
            //sort
            ksort($option);
            ksort($arr_group);
            $temp = $tmpgroup = array();
            // pr($option);die;
            foreach ($arr_group as $key => $value) {
                aasort($value,'order');
                $arr_group[$key] = $value;
                foreach ($value as $kk => $vv) {
                     // aasort($option[$key][$kk],'group_order');
                     uasort($option[$key][$kk], function ($a, $b) {
                        return $b['name'] <= $a['name'];
                        // return $b['group_order'] <= $a['group_order'];
                     });
                     $temp[$key][$kk] = $option[$key][$kk];
                     $tmpgroup[$key][$kk] = $vv['label'];
                }
            }
            $option = $temp;
            $arr_group = $tmpgroup;
            // pr($arr_group);
            // pr($option);die;
	        if($this->request->isAjax()&&$return==0){
	        	$this->view->disable();
	        	$this->view->arr_group = $arr_group;
                $this->view->option = $option;
                $this->view->option_price_total = $option_price_total;
                $this->view->baseURL = URL;
	        	$this->view->cart_id = '';
                $this->view->description = $description;
                $this->view->choice_default_amout = count($is_default);
                $this->view->finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
                $this->view->max_choice = $max_choice;
            	$this->view->partial('frontend/Categories/option_product');
            }else{
                // pr(array(
                //     'arr_group' => $arr_group,
                //     'option' => $option,
                //     'option_price_total'=>$option_price_total
                // ));die;
             	return array(
                    'arr_group' => $arr_group,
                    'option' => $option,
                    'option_price_total'=>$option_price_total,
                    'cart_id' => '',
                    'finish_option' => (new \RW\Models\JTSettings)->getFinishOption($arr_pro),
                    'max_choice' => $max_choice,
                    'description'=> $description,
                    'choice_default_amout'=> count($is_default)
                );
            }
    	}
    }

    public function opcartAction($cart_id){
    	$items = (new \RW\Cart\Cart)->getItems();
        $option =  $arr_group = $arr_pro =  $where = array();
        $option_price_total = $max_choice = 0;
        $description = '';    	
    	if(isset($items[$cart_id])){
            if(isset($items[$cart_id]['_id'])){
                $where['conditions']['_id'] = $items[$cart_id]['_id'];
                $where['conditions']['assemply_item'] = 1;
                $jtproduct = JTProduct::findFirst($where);
                if(isset($jtproduct->maximum_option)){
                    $max_choice = $jtproduct->maximum_option;
                }
            }
    		if(isset($items[$cart_id]['options'])){
    			foreach ($items[$cart_id]['options'] as $key => $value) {
                    $value['product_id'] = $value['_id'];
                    $value['sell_price'] = 0;
                    if(!isset($value['level']) || isset($value['level'])=='')
                        $value['level']='Level 01';
    				$arr_group[$value['level']][$value['group_id']] = $value['group_name'];
                    $option[$value['level']][$value['group_id']][(string)$value['_id']] = $value;
                    $arr_pro[$value['_id']] = $value['option_type'];
    			}
    		}
    	}
        ksort($option);
        ksort($arr_group);
        // pr($arr_group);
        // pr($option);die;
        $finish_option = (new \RW\Models\JTSettings)->getFinishOption($arr_pro);
    	$this->view->disable();
        $this->view->arr_group = $arr_group;
        $this->view->option = $option;
        $this->view->baseURL = URL;
	    $this->view->cart_id = $cart_id;
        // $this->view->description = $description;
        $this->view->option_price_total = isset($items[$cart_id]['total'])?$items[$cart_id]['total']:0;
        $this->view->finish_option = $finish_option;
        // $this->view->choice_default_amout = count($is_default);
        $this->view->max_choice = $max_choice;
        $this->view->partial('frontend/Categories/option_product');
    }

    public function calculateAction()
    {
        if( !$this->request->isAjax() ) {
            return $this->error404();
        }
        $arrReturn = ['error' => 1, 'message' => 'Not enough infomation to process the calculation.'];
        $main = $this->request->getPost('main');
        if( $main ){
            $price = JTProduct::getPrice([
                                            '_id'   => $main['_id'],
                                            'sizew' => 0,
                                            'sizeh' => 0,
                                            'quantity'  => $main['quantity'],
                                            'companyId' => '',
                                            'options'   => $this->request->getPost('options')
                                        ]);
            $arrReturn = ['error' => 0, 'total' => number_format($price['sub_total'], 2)];
        }
        return $this->response($arrReturn);
    }

}
