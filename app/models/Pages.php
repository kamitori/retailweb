<?php
namespace RW\Models;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Pages extends ModelBase {

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
    public $content;

    /**
     *
     * @var integer
     */
    public $order_no;

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



    public function getSource()
    {
        return 'pages';
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

    public function beforeValidation()
    {
        $this->short_name = slug($this->name);
    }
}
