<?php
namespace RW\Controllers;

use RW\Models\Users;
use RW\Models\JTUser;
use RW\Models\JTContact;
use RW\Models\JTCompany;

class UsersController extends ControllerBase
{
    public function indexAction()
    {
        
    }

    public function createAccountAction()
    {
    	if($this->request->isAjax()){
            $arr_return = array('status'=>'error','message'=>'');
    		$first_name = $this->request->getPost('first_name');
            $last_name = $this->request->getPost('last_name');
    		$facebook_id = $this->request->hasPost('facebook_id')?$this->request->getPost('facebook_id'):'';
    		$email = $this->request->getPost('email');
    		$subscribe = $this->request->hasPost('subscribe')?1:0;
    		$phone = $this->request->getPost('phone');
    		if($this->request->hasPost('month') && $this->request->hasPost('day')){
    			$birthday = $this->request->getPost('day').'-'. $this->request->getPost('month');;
    		}
            $address = $this->request->hasPost('address')?$this->request->getPost('address'):'';
            $town_city = $this->request->hasPost('town_city')?$this->request->getPost('town_city'):'';
            $province = $this->request->hasPost('province')?$this->request->getPost('province'):'';
            $province = explode('-',$province);
            $province_id = $province[0];
            $province_name = $province[1];
            $postcode = $this->request->hasPost('postal_code')?$this->request->getPost('postal_code'):'';
    		$password = $this->request->getPost('password');
    		
            // Create JT contact
            $contact = new JTContact();
            $contact->first_name = $first_name;
            $contact->last_name = $last_name;
            $contact->fullname =$first_name.' '.$last_name;
            $contact->password = $this->security->hash($password);
            $contact->email = $email;
            $contact->mobile = $phone;
            $contact->subscribe = $subscribe;
            $contact->phone = $phone;
            $contact->birthday = $birthday;
            $contact->facebook_id = $facebook_id;
            $contact->is_customer = 1;
            $contact->is_employee = 0;

            $company = JTCompany::findFirst(
                                        array(array(
                                                'name' => 'Retail'
                                            )
                                        )
                                    );
            if($company){
                $contact->company = $company->name;
                $contact->company_id = $company->_id;
            }

            $contact_addresses = array();
            $contact_addresses['country'] = 'Canada';
            $contact_addresses['country_id'] = 'CA';
            $contact_addresses['province_state'] = $province_name;
            $contact_addresses['province_state_id'] = $province_id;
            $contact_addresses['address_1'] = $address;
            $contact_addresses['town_city'] = $town_city;
            $contact_addresses['zip_postcode'] = $postcode;
            $contact->addresses = array($contact_addresses);
            if($contact->save()){
                $this->session->set('user',$contact->toArray());
                $arr_return['status'] = 'success';
                
            }else{
                foreach ($contact->getMessages() as $message) {
                    $arr_return['message'] .= $message."<br/>";
                }
            }
            echo json_encode($arr_return);die;
    	}else{
            return $this->response->redirect('/');
        }
    }

    public function checkUserAction()
    {
    	if($this->request->isAjax()){
            $return = 0;
    		$email = $this->request->getPost('email');
    		$user = JTContact::findFirst(array(array('email'=>$email)));
            if($user){
                if($user->facebook_id!=''){
                    if($this->request->hasPost('fb_id') && $this->request->getPost('fb_id') == $user->facebook_id){
                        $this->session->set('user',$user);
                        $return = 1;
                    }
                }else{
                    if($this->request->hasPost('fb_id') && $this->request->getPost('fb_id')!=''){
                        $user->facebook_id = $this->request->getPost('fb_id');
                        $this->session->set('user',$user->toArray());
                        $return = 1;
                    }
                }
            }
            echo $return;
    		die;
    	}
    }
    public function loginAction(){
        $this->view->_link_ = $this->session->has('_link_') ? $this->session->get('_link_') : "";
    }
    public function loginposAction(){
        $this->assets
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
        $this->view->message = $this->session->has('message') ? $this->session->get('message') : "";
        $this->view->baseURL = URL;
        $this->view->_link_ = $this->session->has('_link_') ? $this->session->get('_link_') : "";
        $this->view->partial('frontend/Users/loginPOS');
    }
    public function signinposAction(){

        if(!$this->session->has('user')){
            $username = $this->request->hasPost('username')?$this->request->getPost('username'):'';
            $password = $this->request->hasPost('password')?$this->request->getPost('password'):'';
            //echo md5(md5('anvy123').'anvysercurity');
            $admin = JTContact::findFirst(array(array('full_name'=>$username)));

            $pwd = '9a0d10368cb4fb5b3d40f649bd8c0e22';
            $info = getInfo();
            if($info['url'] == 'http://bmspos.vimpact.ca' || $info['url'] == 'http://bmsdemo_pos.com'){
                $pwd = '573b8b8865482a95f1b7cc5c22ee792c'; //pwd: admin
            }

            if($username=='admin' && md5(md5($password).'anvysercurity') == $pwd){
                $add = array();
                $add['_id'] = '100000000000000000000000';
                $add['full_name'] = 'System Admin';
                if($admin)
                    $admin->token = $this->security->hash(md5($add['_id'] . $add['full_name']));
                $this->session->set('user',$add);
                $this->session->set('message','');

                // Create cookies
                if(!$this->cookies->has("cookies_order_pos")){
                    $key_cookies = uniqid();
                    $this->cookies->set('cookies_order_pos',$key_cookies,time()+365*86400);
                    $this->cookies->send();
                    $arr_cookies_id = json_decode(file_get_contents("cookies_id.json"),true);
                    if(count($arr_cookies_id)){
                        $arr_cookies_id[] = $key_cookies;
                    }else{
                        $arr_cookies_id[1] = $key_cookies;
                    }
                    file_put_contents("cookies_id.json", json_encode($arr_cookies_id));
                }
                return $this->response->redirect($_SERVER['HTTP_REFERER']);
            }

            if ($admin) {
                if (md5(md5(trim($password)).(string)$admin->_id) == $admin->password){
                    $admin->token = $this->security->hash(md5($admin->id . $admin->name));
                    $this->session->set('user',$admin->toArray());
                    $this->session->set('message','');

                    // Create cookies
                    if(!$this->cookies->has("cookies_order_pos")){
                        $key_cookies = uniqid();
                        $this->cookies->set('cookies_order_pos',$key_cookies,time()+365*86400);
                        $this->cookies->send();
                        $arr_cookies_id = json_decode(file_get_contents("cookies_id.json"),true);
                        if(count($arr_cookies_id)){
                            $arr_cookies_id[] = $key_cookies;
                        }else{
                            $arr_cookies_id[1] = $key_cookies;
                        }
                        file_put_contents("cookies_id.json", json_encode($arr_cookies_id));
                    }

                    return $this->response->redirect($_SERVER['HTTP_REFERER']);
                }else{
                    $this->session->set('message','Wrong password. Please check again');
                    return $this->response->redirect('/');
                    return false;
                }
            }else{
                $this->session->set('message','Wrong Username. Please check again');
                return $this->response->redirect('/');
                return false;
            }
        }else{
            return $this->response->redirect('/');
        }
    }
    public function signinAction()
    {
    	$arr_return = array('status'=>'error','message'=>'');
    	if($this->request->isAjax()){
            if(!$this->session->has('user')){
	    		$email = $this->request->hasPost('email')?$this->request->getPost('email'):'';
	    		$user = JTContact::findFirst(array(array('email'=>$email)));
	    		$password = $this->request->hasPost('password')?$this->request->getPost('password'):'';
				if ($user) {
					if ($this->security->checkHash($password, $user->password)) {
						$this->session->set('user',$user->toArray());
						$arr_return['status'] = 'success';
					}else{
						$arr_return['message'] = 'Wrong email or password. Please check again';
					}
				}else{
					$arr_return['message'] = 'Wrong email or password. Please check again';
				}
                // echo json_encode($arr_return);die;
                return $this->response($arr_return);
            }else{
                return $this->response->redirect('/');
            }
    	}else{
            return $this->response->redirect('/');
        }
    }

    public function logoutAction()
    {
        if($this->session->remove("user")){
            $this->session->remove("user");
        }
        return $this->response->redirect('/');
    }
    public function checkPermissionAction(){
        $this->view->disable();
        if($this->session->has('user')){
            $user = $this->session->get('user');
            if($user['_id'] == '100000000000000000000000'){
                echo 'ok';die;
            }
        }
        echo 'none';die;
    }
    public function checkPassAction(){ //check pass admin
        $this->view->disable();
        $password = $this->request->hasPost('hashkey')?$this->request->getPost('hashkey'):'';
        if(md5(md5($password).'anvysercurity') == '9a0d10368cb4fb5b3d40f649bd8c0e22'){
            echo 'ok';die;
        }
        echo 'none';die;
    }

    public function checkIsloggedInAction(){
        
        if (!$this->request->isAjax()) {
            return $this->error404();
        }

        $this->view->disable();
        $is_logged = false;
        $full_name = '';
        if($this->session->has('user')){
            $user = $this->session->get('user');
            if(isset($user['full_name']))
            {
                $full_name = $user['full_name'];    
            }
            
            $is_logged = true;
        }

        return $this->response(['status'=>$is_logged, 'full_name'=>$full_name]);
    }

}
