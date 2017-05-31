<?php
namespace RW\Models;

class JTSetting extends MongoBase {
    
    public $code = '';
    public $name = '';

    public function getSource(){
        return 'tb_settings';
    }
}
