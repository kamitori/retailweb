<?php
namespace RW\Models;

class JTContact extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_contact';
    }
}
