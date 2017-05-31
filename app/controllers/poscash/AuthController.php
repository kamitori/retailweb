<?php
namespace RW\Controllers\Poscash;
use RW\Auth;
use RW\Models\Admins;
use RW\Models\JTUser;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;


class AuthController extends ControllerBase
{
    public function indexAction()
    {	$this->assets
	            ->collection('css')                    
	            ->addCss('/bower_components/bootstrap/dist/css/bootstrap.min.css')
	            ->addCss('/bower_components/font-awesome/css/font-awesome.css')
	            ->addCss('/'.THEME.'css/font.css')
	            ->addCss('/'.THEME.'css/main.css')
	            ->addCss('/'.THEMEPOSCASH.'css/poscash.css')
	            ->setSourcePath(PUBLIC_PATH)
	            ->setTargetPath(PUBLIC_PATH.DS.THEMEPOSCASH.'css/poscash.min.css')
	            ->setTargetUri('/'.THEMEPOSCASH.'css/poscash.min.css')
	            ->join(true)
	            ->addFilter(new \Phalcon\Assets\Filters\Cssmin());
	    $this->assets
	            ->collection('js')
	            ->addJs('/bower_components/jquery/dist/jquery.min.js')
	            ->addJs('/bower_components/jquery-ui/jquery-ui.min.js')
	            ->addJs('/bower_components/bootstrap/dist/js/bootstrap.min.js')
	            ->addJs('/'.THEMEPOSCASH.'js/poscash.js')
	            ->setSourcePath(PUBLIC_PATH)
	            ->setTargetPath(PUBLIC_PATH. DS .THEMEPOSCASH.'js/poscash.min.js')
	            ->setTargetUri('/'.THEMEPOSCASH.'js/poscash.min.js')
	            ->join(true)
	            ->addFilter(new \Phalcon\Assets\Filters\Jsmin());
        $this->view->disable();
        $message = $this->dispatcher->getParam("message");
        $this->view->partial('poscash/Auth/index');
        if($message){
        	$this->view->setParamToView('message', $message);
        }
    }

    public function authorizeAction(){
    	if($this->request->isPost()){
    		$username = $this->request->hasPost('username')?$this->request->getPost('username'):'';
    		$password = $this->request->hasPost('password')?$this->request->getPost('password'):'';
			$this->view->disable();
	        $admin = JTUser::findFirst(array(
	        					array('user_name'=>$username)
	        				));

	        if ($admin) {
	        	if (md5(md5($password)) == $admin->user_password) {
		            $this->auth->saveSuccessLogin($admin);
			        $this->session->set('auth-identity', array(
			            'id' => $admin->_id,
			            'name' => $admin->name,
			            'token' => $this->security->hash(md5($admin->id . $admin->name))
			        ));
			        return $this->response->redirect($_SERVER['HTTP_REFERER']);
		        }else{
			        $this->view->setPartialsDir('../');
		            $this->dispatcher->setParams(array('message'=>'Wrong username or password'));
					$this->dispatcher->forward(array(
						'namespace'     => 'RW\Controllers\Poscash',
						'controller'    => 'Auth',
						'action'        => 'index'
					));
					return false;
		        }
	        }else{
		        $this->view->setPartialsDir('../');
	        	$this->dispatcher->setParams(array('message'=>'Wrong username or password'));
				$this->dispatcher->forward(array(
					'namespace'     => 'RW\Controllers\Poscash',
					'controller'    => 'Auth',
					'action'        => 'index'
				));
				return false;
	        }     
    	}else{
    		return $this->response->redirect($_SERVER['HTTP_REFERER']);
    	}
    }

    public function logoutAction(){
    	$this->auth->remove();
    	return $this->response->redirect('/poscash');
    }
}
