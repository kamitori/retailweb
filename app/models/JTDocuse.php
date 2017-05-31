<?php
namespace RW\Models;

class JTDocuse extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_document_use';
    }
}
