<?php
namespace RW\Auth;

use Phalcon\Mvc\User\Component;
use RW\Models\Admins;
use RW\Models\RememberTokens;
use RW\Models\SuccessLogins;
use RW\Models\FailedLogins;

/**
 * RW\Auth\Auth
 * Manages Authentication/Identity Management in RW
 */
class Auth extends Component
{

    /**
     * Checks the admin credentials
     *
     * @param array $credentials
     * @return boolan
     */
    public function check($credentials)
    {

        // Check if the admin exist
        $admin = Admins::findFirstByEmail($credentials['email']);
        if ($admin == false) {
            $this->registerUserThrottling(0);
            throw new Exception('Wrong email/password combination');
        }

        // Check the password
        if (!$this->security->checkHash($credentials['password'], $admin->password)) {
            $this->registerUserThrottling($admin->id);
            throw new Exception('Wrong email/password combination');
        }

        // Register the successful login
        $this->saveSuccessLogin($admin);

        $this->session->set('auth-identity', array(
            'id' => $admin->id,
            'name' => $admin->name,
            'token' => $this->security->hash(md5($user->id . $admin->name))
        ));
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param RW\Models\Admins $admin
     */
    public function saveSuccessLogin($admin)
    {
        $successLogin = new SuccessLogins();
        $successLogin->admin_id = $admin->id;
        $successLogin->ip_address = $this->request->getClientAddress();
        $successLogin->user_agent = $this->request->getUserAgent();
        $successLogin->created_at = date("d-m-Y H:i:s");
        $successLogin->updated_at = date("d-m-Y H:i:s");
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new Exception($messages[0]);
        }
    }

    /**
     * Implements login throttling
     * Reduces the efectiveness of brute force attacks
     *
     * @param int $adminId
     */
    public function registerUserThrottling($adminId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->admin_id = $adminId;
        $failedLogin->ip_address = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->created_at = date("d-m-Y H:i:s");
        $failedLogin->updated_at = date("d-m-Y H:i:s");
        $failedLogin->save();

        $attempts = FailedLogins::count(array(
            'ip_address = ?0 AND attempted >= ?1',
            'bind' => array(
                $this->request->getClientAddress(),
                time() - 3600 * 6
            )
        ));

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }

    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->session->get('auth-identity');
    }

    /**
     * Returns the current identity
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['name'];
    }

    /**
     * Removes the admin identity information from session
     */
    public function remove()
    {
        $this->session->remove('auth-identity');
    }

    /**
     * Auths the admin by his/her id
     *
     * @param int $id
     */
    public function authAdminById($id)
    {
        $admin = Admins::findFirstById($id);
        if ($admin == false) {
            throw new Exception('The admin does not exist');
        }

        $this->session->set('auth-identity', array(
            'id' => $admin->id,
            'name' => $admin->name
        ));
    }

    /**
     * Get the entity related to admin in the active identity
     *
     * @return RW\Models\Admins
     */
    public function getAdmin()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id'])) {

            $admin = Admins::findFirstById($identity['id']);
            if ($admin == false) {
                throw new Exception('The admin does not exist');
            }

            return $admin;
        }

        return false;
    }
}
