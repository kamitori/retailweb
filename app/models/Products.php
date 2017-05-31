<?php
namespace RW\Models;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Products extends ModelBase {

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $desciption;

    /**
     *
     * @var integer
     */
    public $category_id;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var string
     */
    public $meta_title;

    /**
     *
     * @var string
     */
    public $meta_desciption;

    public function initialize()
    {
        $this->belongsTo('category_id', 'RW\Models\Categories', 'id', array(
                                                                                "alias" => "categories"
                                                                            ));
        $this->hasMany('id', 'RW\Models\ProductOption', 'product_id', array(
                                                                                "alias" => "productOption"
                                                                            ));
    }
    public function getSource()
    {
        return 'products';
    }

    public function beforeValidation()
    {
        $this->short_name = slug($this->name);
    }

    public function validation()
    {
        $this->validate(
            new PresenceOf(
                array(
                    'field'    => 'name',
                    'message'  => 'Name is required.'
                )
            )
        );

        $this->validate(
            new Uniqueness(
                array(
                    'field'    => 'name',
                    'message'  => 'Value of field "name" is already present in another record'
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }
}
