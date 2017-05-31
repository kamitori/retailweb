<?php
namespace RW\Controllers\Pos;

use RW\Models\Categories;
use RW\Models\Banners;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        die;
    	$this->view->banners = Banners::find([
            'columns'   => 'id, image, link',
            'order'     => 'order_no ASC',
            'conditions' => "position = 1 "
        ]);
        $this->view->banners_right = Banners::find([
            'columns'   => 'id, image, link',
            'order'     => 'order_no ASC',
            'conditions' => "position = 2 "
        ]);

        $this->view->categories = $this->JTCategory();
    }

}
