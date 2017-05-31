<?php
namespace RW\Models;

class JTProvince extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_province';
    }
}
