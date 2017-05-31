<?php
namespace RW\Controllers\Kiosk;

class IndexController extends ControllerBase
{
    public function indexAction(){
        $this->view->categories = $this->JTCategory();
    }
}
