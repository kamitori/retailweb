<?php
namespace RW\Models;

use Phalcon\Mvc\Model\Validator\PresenceOf;

class Banners extends ModelBase {

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var string
     */
    public $link;
    public $position;
    public $order_no;
    

    public function getSource()
    {
        return 'banners';
    }
    public function getListBannersByType($type){
        $arr = $this->find('position = '.$type);
        $arr = $arr->toArray();
        $arr_return = [];
        foreach($arr as $item){
            $arr_return [] = $item['image'];
        }
        return $arr_return;
    }
    public function validation()
    {
        $this->validate(
            new PresenceOf(
                array(
                    'field'    => 'image',
                    'message'  => 'Image is required.'
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }
}
