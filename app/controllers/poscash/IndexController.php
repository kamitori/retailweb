<?php
namespace RW\Controllers\Poscash;

use RW\Models\Categories;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->categories = $this->JTCategory();
    }
    public function retrieveAction(){

    }
}
