<?php
namespace RW\Models;

class JTCategory extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_category';
    }
}
