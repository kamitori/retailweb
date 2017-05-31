<?php
namespace RW\Models;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Options extends  ModelBase
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
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $group;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var double
     */
    public $price;

    /**
     *
     * @var string
     */
    public $sold_by;

    /**
     *
     * @var string
     */
    public $oum;

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
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'options';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Options[]
     */
    public static function getOptions($parameters = null)
    {
        $options = parent::find($parameters);
        $arrReturn = array();
        if($options)
        {
            $options = $options->toArray();
            foreach ($options as $key=>$val) {
                $arrReturn[$key] = ['text' => $val['name'], 
                                    'value' => $val['id'], 
                                    'price' => $val['price'],
                                    'sold_by' => $val['sold_by'],
                                    'oum' => $val['oum']
                                    ];
            }            
        }

        return $arrReturn;            

    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Options
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
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
