<?php
namespace RW\Models;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class ProductOption extends  ModelBase
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
    public $product_id;

    /**
     *
     * @var integer
     */
    public $option_id;

    /**
     *
     * @var double
     */
    public $weight;

    /**
     *
     * @var double
     */
    public $amount;

    /**
     *
     * @var double
     */
    public $custom_price;

    /**
     *
     * @var double
     */
    public $total;

    public function initialize()
    {
        $this->belongsTo('product_id', 'RW\Models\Products', 'id', array('alias' => 'Product'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'product_option';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProductOption[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProductOption
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
