<?php
namespace RW\Models;

class JTStuffs extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_stuffs';
    }
}
