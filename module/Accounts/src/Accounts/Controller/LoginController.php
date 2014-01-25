<?php

namespace Accounts\Controller;

use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

/**
 * Handle requests on the login page.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class LoginController extends AbstractActionController
{
	/**
	 * Default method to call in the controller.
     * @return a ViewModel object which contains HTML content
	 */
    public function indexAction()
    {
        $this->destroySession();
        return array();
    }

    /**
     * Handle asynchronous login requests for the users.
     * @return Response a HTTP response object which contains JSON data
     *         infers whether the login is successful
     */
    public function processAction()
    {
    	$username	= $this->getRequest()->getPost('username');
    	$password	= $this->getRequest()->getPost('password');

    	$result 	= array(
    		'isSuccessful'		=> false,
    		'isUsernameEmpty'	=> empty($username),
    		'isPasswordEmpty'	=> empty($password),
    		'isAccountValid'	=> $this->verifyAccount($username, $password),
    	);

    	if ( $result['isAccountValid'] ) {
            $this->createSession(1);
            $result['isSuccessful'] = true;
        }

    	$response = $this->getResponse();
    	$response->setStatusCode(200);
    	$response->setContent( Json::encode($result) );
    	return $response;
    }

    /**
     * Verify if the username and password is valid.
     * @param  String $username - the username or email of an account
     * @param  String $password - the password of an account
     * @return true if the username and password is valid
     */
    private function verifyAccount($username, $password)
    {
    	if ( empty($username) || empty($password) ) {
    		return false;
    	}

    	$sm 				= $this->getServiceLocator();
		$userTable 			= $sm->get('Accounts\Model\UserTable');

    	if ( !$this->isEmailAddress($username) ) {
    		return $userTable->verifyAccountByUsername($username, md5($password));
    	} else {
    		return $userTable->verifyAccountByEmail($username, md5($password));
    	}
    }

    /**
     * Check if the username is an email address.
     * @param  String  $username - the username or email of an account
     * @return true if the username is an email address
     */
    private function isEmailAddress($username)
    {
    	return strpos($username, '@');
    }

    /**
     * Create a session for a logined user.
     */
    private function createSession($uid)
    {
        $session    = new Container('itp_session');
        $session->offsetSet('isLogined', true);
    }

    /**
     * Destroy a session when the user reach the login page.
     */
    private function destroySession()
    {
        $session    = new Container('itp_session');
        $session->getManager()->getStorage()->clear('itp_session');
    }
}
