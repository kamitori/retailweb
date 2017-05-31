<?php

namespace RW\Models;

class Ordersitems extends ModelBase 
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $productId;

    /**
     *
     * @var string
     */
    public $productName;

    /**
     *
     * @var double
     */
    public $unitprice;

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
    public $quantity;

    /**
     *
     * @var integer
     */
    public $orderId;

    /**
     *
     * @var integer
     */
    public $deleted;

    /**
     *
     * @var integer
     */
    public $userId;

    /**
     *
     * @var integer
     */
    public $categoryId;

    /**
     *
     * @var string
     */
    public $categoryName;

    /**
     *
     * @var double
     */
    public $price;

    /**
     *
     * @var string
     */
    public $totalprice;

      /**
     *
     * @var double
     */
    public $tax;

    public function initialize()
    {
        $this->belongsTo('orderId', 'RW\Models\Orders', 'id', array(
                                                    "alias" => "Orders"
                                                ));
    }
    
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'orderitems';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orderitems[]
     */
    // public static function find($parameters = null)
    // {
    //     return parent::find($parameters);
    // }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orderitems
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'productId' => 'productId',
            'productName' => 'productName',
            'unitprice' => 'unitprice',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'quantity' => 'quantity',
            'orderId' => 'orderId',
            'deleted' => 'deleted',
            'userId' => 'userId',
            'categoryId' => 'categoryId',
            'categoryName' => 'categoryName',
            'price' => 'price',
            'totalprice' => 'totalprice',
            'tax' => 'tax'
        );
    }

}
