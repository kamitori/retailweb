<?php

namespace RW\Models;

class Orders extends ModelBase 
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $type;

    /**
     *
     * @var double
     */
    public $totalPrice;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var integer
     */
    public $customerId;

    /**
     *
     * @var string
     */
    public $userName;

    /**
     *
     * @var string
     */
    public $customerName;

     /**
     *
     * @var double
     */
    public $totalTax;

    public function initialize()
    {
        $this->hasMany('id', 'RW\Models\Ordersitems', 'orderId',array('alias'=>'Ordersitems'));
    }
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'order';
    }
    public function getOrderItems($parameters = null) {
        // cho phien ban phalcon moi
        return $this->getRelated('Ordersitems', $parameters);
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function countTotalOrder(){
        return Orders::count([
                                        'conditions' => [],
                                    ]);
    }
    public static function _SumOrdersRegister(){
        $filter = new \Phalcon\Filter;
        $_where = [
            'conditions' => 'type = 1 and deleted = 0 and status = 2'
            ,'bind' => []
        ];
        $_datas = Orders::find($_where);
        $_orders_sub_total = 0;
        $_orders_tax = 0;
        $_orders_discount = 0;
        $_orders_id = [];
        foreach($_datas as $_data){
            $_id = $filter->sanitize($_data->id, 'int');
            $_orders_id [] = $_id;
            $_orders_sub_total +=(float)$_data->totalPrice;
            $_orders_tax +=(float)$_data->totalTax;
            $_once_record = Orders::findFirst($_id);
            $_items = $_once_record->getOrderItems(['conditions' => "deleted = 0"]);
            foreach($_items as $_item){
                if((int)$_item->quantity<0){
                    $_orders_discount +=(float)$_item->price - (float)$_item->tax;
                }
            }
        }
        return [
            '_subTotal'=>display_format_currency($_orders_sub_total),
            '_subTax'=>display_format_currency($_orders_tax),
            '_subDiscount'=>display_format_currency($_orders_discount),
            '_listOrdersId'=>$_orders_id
        ];
    }
    public static function _updatePart($id = [],$_datas = []){
        if(empty($_datas)){
            $_datas = [
                'deleted' => 0
            ];
            //find()->update($_,function($check))
        }
        $_items = Orders::find('id in ('.implode($id,',').')');
        if($_items){
            foreach($_items as $_item){
                $_item->status = 3;
                if($_item->save()!== true){
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    public static function getTotalOrder(){
        $arr_data = Orders::find(
            [
                'conditions' => 'type = 1 and deleted = 0 and status = 1'
                ,'bind'       => [] 
                ,'order'      => 'id desc'
                ,'limit'      => 1
            ]
        );
        if($arr_data){
            $data = $arr_data->toArray();            
            if(empty($data)){
                return 0;
            }else{
                $filter = new \Phalcon\Filter;
                $arr_data = Orders::findFirst($filter->sanitize($data[0]['id'], 'int'));
                $arr_items = $arr_data->getOrderItems(['conditions' => "deleted = 0"]);
                $data = $arr_data->toArray();
                $data['items'] = $arr_items->toArray();
                return $data;
            }
        }
        return 0;
    }
}