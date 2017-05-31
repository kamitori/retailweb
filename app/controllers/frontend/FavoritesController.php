<?php
namespace RW\Controllers;

use RW\Models\JTFavoriteProducts;
use RW\Models\JTCategory;


class FavoritesController extends ControllerBase
{
    public function indexAction()
    {
    	$this->assets
                ->collection('css')
                ->addCss('/bower_components/bootstrap/dist/css/bootstrap.min.css')
                ->addCss('/bower_components/font-awesome/css/font-awesome.css')
                ->addCss('/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css')
                ->addCss('/'.THEME.'css/font.css')
                ->addCss('/'.THEME.'css/main.css')
                ->addCss('/'.THEME.'css/custom.css')
                ->setSourcePath(PUBLIC_PATH)
                ->setTargetPath(PUBLIC_PATH.DS.THEME.'/css/app.min.css')
                ->setTargetUri('/'.THEME.'css/app.min.css')
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Cssmin());
         $this->assets
                ->collection('js')
                ->addJs('/bower_components/jquery/dist/jquery.min.js')
                ->addJs('/bower_components/bootstrap/dist/js/bootstrap.min.js')
                ->addJs('/bower_components/iscroll/build/iscroll.js')
                ->addJs('/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')
                ->addJs('/'.THEME.'js/main.js')
                ->addJs('/'.THEME.'js/facebook-sdk.js')
                ->addJs('/'.THEME.'js/facebook_login.js')
                ->setSourcePath(PUBLIC_PATH)
                ->setTargetPath(PUBLIC_PATH.DS.THEME.'/js/app.min.js')
                ->setTargetUri('/'.THEME.'js/app.min.js')
                ->join(true)
                ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
        $this->assets->collection('pageJS');
        $this->view->baseURL = URL;

        $categories = $this->JTCategory();

        $this->view->category = $categories;
        $user = $this->session->has('user')?$this->session->get('user'):array();
        $this->view->products = JTFavoriteProducts::findListById($user);
    }    
    function removeAction(){
        $this->view->disable();
        $_return = array('error'=>1);
        if($this->request->isAjax() && $this->request->isPost() ){            
            $_id = $this->request->getPost("id");
            $_userid = $this->request->getPost("ip");
            if($_userid!=''){
                $_one = JTFavoriteProducts::findFirst(
                    array(
                        array(
                            'user_id'=>$_userid
                            ,'product_id'=> $_id
                        )
                    )
                );
            }else{
                $_ip = get_client_ip();
                $_one = JTFavoriteProducts::findFirst(
                    array(
                        array(
                            'ip'=>$_ip
                            ,'product_id'=> $_id
                        )
                    )
                );
            }
            $_one->deleted = true;
            $_one->save();
            $_return = array('error'=>0);
        }
        return $_return;
    }
}
