<?php
namespace RW\Controllers\Admin;

class VouchersController extends ControllerBase {

    protected $notFoundMessage = 'This Voucher did not exist.';

    public function listAction()
    {
        $filter = new \Phalcon\Filter;
        $data = array_merge([
                'search'     => [],
                'pagination' => [
                    'pageNumber' => 1,
                    'pageSize'   => 100,
                    'sort'       => 'desc',
                    'sortName'   => 'id'
                ]
            ], $this->getPost());
        $conditions = [];
        $bind = [];
        foreach($data['search'] as $fieldName => $value) {
            if (is_numeric($value)) {
                if (is_int($value)) {
                    $value = $filter->sanitize($value, 'int');
                } else if (is_float($value)) {
                    $value = $filter->sanitize($value, 'float');
                }
                $conditions[$fieldName] = $value;
            } else if (is_string($value)) {
                $value = $filter->sanitize($value, 'string');
                $conditions[$fieldName] = "/".$value."/";
            }
        }
        $arrConditions = ['deleted'=>false];
        if(count($conditions)>1){
            foreach ($conditions as $key => $value) {
                    $arrConditions['$and'][$key] = $value;
            }
        }else{
            foreach ($conditions as $key => $value) {
                $arrConditions[$key] = $value;
            }
        }
        $sort = [];
        if (is_string($data['pagination']['sortName'])) {
            $sort[$data['pagination']['sortName']] = $data['pagination']['sort']=="asc"||$data['pagination']['sort']=="ASC"?1:-1;
        } else {
            $sort['name'] = 1;
        }
       
        $limit = is_numeric($data['pagination']['pageSize']) ? $data['pagination']['pageSize'] : 100;
        $limit = $limit==25 ? 100 : $limit;
        $pageNumber = is_numeric($data['pagination']['pageNumber']) ? $data['pagination']['pageNumber'] : 1;
        $offset = ceil( ($pageNumber-1) * $limit);        
        
        $total = $this->model->count([
                                        "conditions"=>$arrConditions
                                    ]);
        $data = $this->model->find([
                "conditions"=>$arrConditions,
                'sort'      => $sort,
                'limit'      => $limit,
                'skip'     => $offset
            ]);
        if($data) {
            foreach($data as $key => $value) {
                $value->id = (string)$value->_id;
                $value->limited = $value->limited==2 ? "One time used" : 'Unlimited';
                $value->active = $value->active ? "Active" : 'InActive';
                $value->value = (int)$value->value;
                $value->product_type = $value->product_type=='promo' ? 'Promo code':'Voucher code';
                $data[$key] = $value->toArray();
            }
        } else {
            $data = [];
        }
        return $this->response(['error' => 0, 'data' => $data, 'total' => $total]);
    }
    public function getTenCodeAction($str){
        $filter = new \Phalcon\Filter;
        $arr_explorer = explode(':', $str);
        $value = isset($arr_explorer[0]) ? $arr_explorer[0] : 10;
        $value = str_replace('/', '', $value);
        $type = isset($arr_explorer[1]) ? $arr_explorer[1] : '';
        $type = $type == 'phan-tram' ? "%" : '$';
        $date = isset($arr_explorer[2]) && $arr_explorer[2]!="" ? $arr_explorer[2] : date('Y-m-d', ( strtotime(date('Y-m-d')) + 86400*7 )  );
        $date = date("Y-m-d", strtotime($filter->sanitize($date, 'string')) );
        $arrReturn = ['error'=>1,'message'=>'','dates'=>''];
        $arr_data = array();
        for($i=0;$i<10;$i++){
            $str = '';
            $start = 0;

            while(true){
                $start++;
                $str = 'FV'.(string)sprintf("%04d", $start);
                $test = $this->model->findFirst(['conditions' => ['name'=>$str]]);
                if(is_null($test) || !$test){
                    break;
                }
            };

            $Voucher = new $this->model;
            $Voucher->order_no = 1;
            $Voucher->name = $str;
            $Voucher->value = (float) $value;
            $Voucher->limited = 2;
            $Voucher->active = 1;
            $Voucher->product_type = 'all';
            $Voucher->type = $filter->sanitize($type, 'string');
            $Voucher->expries = date("Y-m-d H:i:s", strtotime($filter->sanitize($date, 'string')) );

            if($Voucher->save()) $arr_data[] = $str;
        }
        if(!empty($arr_data)) $arrReturn = ['error'=>0,'message'=>$arr_data,'dates'=>$date];

        return $this->response($arrReturn);
    }
    public function editAction($id = 0){
        $id = str_replace("/","",$id);
        $data = $this->model->findFirst(
                                        array(
                                            'conditions'=> array("_id"=> new \MongoId($id)),
                                            'sort'      => array("_id"=>-1)
                                        ));
        if(isset($data->_id))
            $data->id = (string)$data->_id;

        $data->categoryOptions = (new \RW\Models\JTSettings)->type('product_category')->getSelect();

        $arrReturn = ['error'=>0,'message'=>"",'data'=>$data];
        return $this->response($arrReturn);
    }
    public function generatorAction(){
        $arrReturn = ['error'=>1,'message'=>''];
        $str = '';
        $start = 0;

        while(true){
            $start++;
            $str = 'FV'.(string)sprintf("%04d", $start);
            $test = $this->model->findFirst(['conditions' => ['name'=>$str]]);
            if(is_null($test) || !$test){
                break;
            }
        };
        
        if($str !='') $arrReturn = ['error'=>1,'message'=>$str];

        return $this->response($arrReturn);
    }
    public function updateAction()
    {
        $filter = new \Phalcon\Filter;
        $data = $this->getPost();
        $data = array_merge(['link' => ''], $data);
        $id = 0;
        if (isset($data['id']) && $data['id']!='') {
            $id = (string)$data['id'];
            $Voucher = $this->model->findFirst(
                array(
                    'conditions'=>array(
                        '_id'=>new \MongoId($id)
                    )
                )
            );
            if ($Voucher) {
                $message = 'has been updated';
            } else {
                return $this->error404($this->notFoundMessage);
            }
            $test = $this->model->findFirst(
                array(
                    'conditions'=>array(
                        'name'=>$data['name'],
                        '_id'=>array('$ne'=>new \MongoId($id))
                    )
                )
            );
        } else {
            $Voucher = new $this->model;
            $message = 'has been created';
            $test = $this->model->findFirst(
            array(
                'conditions'=>array(
                            'name'=>$data['name']
                        )
                )
            );
        }
        $Voucher->name = $filter->sanitize($data['name'], 'string');
        if(is_null($test) || !$test){
            $Voucher->order_no = $filter->sanitize($data['order_no'], 'string');
            $Voucher->value = $filter->sanitize($data['value'], 'float');
            $Voucher->limited = $filter->sanitize($data['limited'], 'int');
            $Voucher->active = $filter->sanitize($data['active'], 'int');
            $Voucher->product_type = $filter->sanitize($data['product_type'], 'string');
            $Voucher->type = $filter->sanitize($data['type'], 'string');
            $Voucher->expries = date("Y-m-d H:i:s", strtotime($filter->sanitize($data['expries'], 'string')) );
            $Voucher->category = $filter->sanitize($data['category'], 'string');            
            if ($Voucher->save() === true) {
                $arrReturn = ['error' => 0, 'message' => 'Voucher <b>'.$Voucher->name.'</b> '.$message.' successful.', 'data' => ['id' => (string)$Voucher->_id]];
            } else {
                $arrReturn = ['error' => 1, 'message' => $Voucher->getmessage()];
            }
        }else{
            $arrReturn = ['error' => 1, 'message' => 'Voucher existed. Please try a different key'];
        }
        return $this->response($arrReturn);
    }
    public function deleteVoucherAction($id = 0){
        $arr_id = explode('/', $id);
        $id = end($arr_id);
        //http://pos.banhmisub.com/admin/Vouchers/delete/57bd2bd130575ac53a3c9875
        $id = str_replace("/","",$id);
        $arrReturn = ['error' => 1, 'message' => 'Cannot delete'];
        $item = $this->model->findFirst(
             array(
                    'conditions'=>array(
                        '_id'=>new \MongoId($id)
                    )
                )
        );
        if ($item != false) {
            if ($item->delete() == false) {
                $arrReturn['message'] = "Sorry, we can't delete the this item right now: \n";
                foreach ($item->getmessage() as $message) {
                    $arrReturn['message'] .= $message."\n";
                }
            } else {
                $arrReturn['error'] = 0;
                $arrReturn['message'] =  "The item was deleted successfully !";
            }
        }
        return $this->response($arrReturn);
    }
}
