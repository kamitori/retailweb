<?php
namespace RW\Controllers\Kiosk;
use RW\Auth;
use RW\Models\Admins;
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
    		$email = $this->request->hasPost('email')?$this->request->getPost('email'):'';
    		$password = $this->request->hasPost('password')?$this->request->getPost('password'):'';
			$this->view->disable();
	        $admin = Admins::findFirstByEmail($email);
	        if ($admin == false) {
	        	$this->view->setPartialsDir('../');
	        	$this->dispatcher->setParams(array('message'=>'Wrong email or password'));
				$this->dispatcher->forward(array(
					'namespace'     => 'RW\Controllers\Poscash',
					'controller'    => 'Auth',
					'action'        => 'index'
				));
				return false;
	        }else{
	        	if (!$this->security->checkHash($password, $admin->password)) {
		            $this->view->setPartialsDir('../');
		            $this->dispatcher->setParams(array('message'=>'Wrong email or password'));
					$this->dispatcher->forward(array(
						'namespace'     => 'RW\Controllers\Poscash',
						'controller'    => 'Auth',
						'action'        => 'index'
					));
					return false;
		        }else{
		        	$this->auth->saveSuccessLogin($admin);
			        $this->session->set('auth-identity', array(
			            'id' => $admin->id,
			            'name' => $admin->name,
			            'token' => $this->security->hash(md5($admin->id . $admin->name))
			        ));
			        return $this->response->redirect($_SERVER['HTTP_REFERER']);
		        }
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
