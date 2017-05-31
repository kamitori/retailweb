<?php
namespace RW\Controllers\Admin;

use RW\Auth\Exception as AuthException;

class AuthController extends ControllerBase {

    private $authExpire;

    public function initialize()
    {
        parent::initialize();
        $this->authExpire = strtotime('+7 day');        
    }

    public function authorizeAction()
    {
        return $this->response(['error' => 1, 'message' => 'Please login before continue!'], 401, 'Unauthorized');
    }

    public function tokenValidateAction()
    {
        $responseCode = 401;
        $responseMessage = 'Unauthorized';
        $arrReturn = ['error' => 1];
        if( $session = $this->auth->getIdentity() ) {
            $token = $this->request->getHeader('access-token');
            $uid = $this->request->getHeader('uid');
            $expiry = (double)$this->request->getHeader('expiry');

            if ($token === $session['token']
                    && $expiry > time()) {
                $responseCode = 200;
                $responseMessage = '';
                $arrReturn = ['error' => 0, 'data' => $session];
            } else {
                $this->auth->remove();
            }
        } else {
            $this->auth->remove();
        }
        return $this->response($arrReturn, $responseCode, $responseMessage);
    }

    public function loginAction()
    {
        $responseCode = 401;
        $responseMessage = 'Unauthorized';
        $responseHeader = [];
        try {
            $data = $this->getPost();
            if ($this->request->isPost()) {
                $validation = new \Phalcon\Validation;
                $validation->add(
                    'email',
                    new \Phalcon\Validation\Validator\PresenceOf(
                        array(
                            'message' => 'Email is required.'
                        )
                    )
                )->add(
                    'password',
                    new \Phalcon\Validation\Validator\PresenceOf(
                        array(
                            'message' => 'Password is required.'
                        )
                    )
                );
                if ($validation->validate($data) == false) {
                    $arrReturn = ['error' => 1, 'message' => 'false $data'];
                    foreach ($form->getMessages() as $message) {
                        $arrReturn['message'] .= '<p>'. $message .'</p>';
                    }
                } else {
                    $this->auth->check(array(
                        'email'     => $data['email'],
                        'password'  => $data['password']
                    ));
                    $arrReturn = ['error' => 0];
                    $session = $this->auth->getIdentity();
                    $responseCode = 200;
                    $responseMessage = '';
                    $responseHeader = [
                                    'Token-Type'    => 'Bearer',
                                    'Access-Token'  => $session['token'],
                                    'Client'        => $session['token'],
                                    'Uid'           => md5(time().$session['id']),
                                    'Expiry'        => $this->authExpire
                                ];
                }
            }
        } catch (AuthException $e) {
            $arrReturn = ['error' => 1, 'message' => $e->getMessage()];
        }
        return $this->response($arrReturn, $responseCode, $responseMessage, $responseHeader);
    }

    public function logoutAction()
    {
        $this->auth->remove();
        return $this->response(['error' => 0]);
    }
}
