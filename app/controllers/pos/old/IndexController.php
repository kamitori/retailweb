<?php
namespace RW\Controllers\Pos;
class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->ListProducts = $this->drawOneProduct(); // list product chua limit, chua phan trang        
    }
}
