<?php
namespace RW\Models;

class JTUser extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_user';
    }
}
