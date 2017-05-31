<?php
namespace RW\Models;

use Phalcon\Mvc\Model\Validator\PresenceOf;

class Configs extends ModelBase {

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $cf_key;

    /**
     *
     * @var string
     */
    public $cf_value;

    public $status;
    

    public function getSource()
    {
        return 'configs';
    }

    public function validation()
    {
        $this->validate(
            new PresenceOf(
                array(
                    'field'    => 'cf_key',
                    'message'  => 'Key is required.'
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    public function getOptionGroup()
    {
        $group = $this->findFirst("cf_key = 'option_group'");
        $arrReturn = array();
        if($group)
        {
            $arr_group = json_decode($group->cf_value, true);
            foreach ($arr_group as $key=>$val) {
                $arrReturn[$key] = ['text' => $val, 'value' => $key];
            }            
        }

        return $arrReturn;            
    }

    public function getListUnit()
    {
        //[{"name":"Unit","data":["Piece","Loaf","Part"]},{"name":"Weight","data":["Kg","Grams","Grains","Pounds","Ounces"]},{"name":"Size","data":["Inches","Feet","Cm","Sq.in","Sq.ft","Sq.cm"]}]
        $list_unit = $this->findFirst("cf_key = 'list_unit'");
        $arr_unit = array();
        $data = array();
        if($list_unit)
        {
            $data = json_decode($list_unit->cf_value, true);
            foreach ($data as $key=>$val) {
                $arr_unit[$key] = ['value' => $val['name'], 'text' => $val['name']];
            }            
        }        
        return ['unit'=>$arr_unit, 'listUnit'=>$data];            
    }    
}
