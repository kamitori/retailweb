<?php
namespace RW\Controllers\Pos;

use RW\Models\Pages;


class PagesController extends ControllerBase
{
    public function indexAction()
    {
    	$pageName = $this->dispatcher->getParam('pageName');
    	$page = Pages::findFirstByShortName($pageName);
        if ($page) {
        	$this->view->content = $page->content;
        }
    }

}
