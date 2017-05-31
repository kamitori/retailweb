<?php
namespace RW\Models;

class Vouchers extends MongoBase {

    public $id;
    public $name;
    public $value;
    public $expries;
    public $active;
    public $type;
    public $product_type;
    public $order_no;
    

    public function getSource()
    {
        return 'jt_voucher';
    }    
}
