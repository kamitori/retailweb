<?php
namespace RW\Models;

class JTDoc extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_document';
    }
}
