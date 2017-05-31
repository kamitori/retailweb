<?php
namespace RW\Models;

class JTSalesaccount extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_salesaccount';
    }
}