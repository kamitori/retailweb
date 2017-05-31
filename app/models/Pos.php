<?php
namespace RW\Models;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Pos extends ModelBase {

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
    public $short_name;

    /**
     *
     * @var string
     */
    public $desciption;

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
        $this->hasMany('id', 'RW\Models\Pos', 'category_id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
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

    public function getOptions()
    {
        $arrReturn = [];
        $categories = $this->find([
                'columns'   => 'id, name',
                'order'     => 'name ASC'
            ]);
        if ($categories) {
            foreach ($categories as $category) {
                $arrReturn[] = ['text' => $category->name, 'value' => $category->id];
            }
        }

        return $arrReturn;
    }
}
