<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
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
        if ( $this->isEnableAutoLogin() ) {
            return $this->sendRedirect('accounts/dashboard');
        }

        $this->destroySession();
        $param  = $this->params()->fromQuery();
        return array(
            'redirectUrl'   => $this->getRedirectUrl($param),
        );
    }

    /**
     * Check if the user has enabled auto login.
     * @return true if the user has enabled auto login
     */
    private function isEnableAutoLogin()
    {
        $session            = new Container('itp_session');

        $isEnableAutoLogin  = $session->offsetGet('allowAutoLogin');
        return $isEnableAutoLogin;
    }

    /**
     * Get the redirect url after login.
     * @param  Array $param - an array which contains information of 
     *         previous page
     * @return a string which stand for the redirect url
     */
    private function getRedirectUrl($param)
    {
        $basePath       = $this->basePath('');

        foreach ( $param as $key => $value ) {
            if ( $key == 'lectureId' ) {
                return $basePath.'/courses/lecture/detail/'.$value;
            } else if ( $key == 'newsId' ) {
                return $basePath.'/courses/news/detail/'.$value;
            } else if ( $key == 'courseId' ) {
                return $basePath.'/courses/course/detail/'.$value;
            } else {
                return $basePath.'/accounts/dashboard';
            }
        }
    }

    /**
     * Get the base path of the website.
     * @return a string which stand for the base path of the website
     */
    private function basePath()
    {
        $renderer   = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url        = $renderer->basePath();
        return $url;
    }

    /**
     * Handle asynchronous login requests for the users.
     * @return a HTTP response object which contains JSON data
     *         infers whether the login is successful
     */
    public function processAction()
    {
        $username       = $this->getRequest()->getPost('username');
        $password       = $this->getRequest()->getPost('password');
        $allowAutoLogin = $this->getRequest()->getPost('remember_me');

        $result     = array(
            'isSuccessful'      => false,
            'isUsernameEmpty'   => empty($username),
            'isPasswordEmpty'   => empty($password),
            'isAccountValid'    => $this->verifyAccount($username, $password),
        );

        if ( $result['isAccountValid'] ) {
            $userData   = $this->parseUserData($result['isAccountValid']);
            $this->createSession($userData, $allowAutoLogin);
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

        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

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
     * Get user data from the login query result.
     * @param  User $user - an object of User which contains user's
     *         profile
     * @return an array which is needed in the session.
     */
    private function parseUserData($user)
    {
        $userData = array(
            'uid'               => $user->uid,
            'username'          => $user->username,
            'email'             => $user->email,
            'isActivated'       => $user->is_activated,
            'userGroupID'       => $user->user_group_id,
            'lastTimeSignIn'    => $user->last_time_signin,
        );

        return $userData;
    }

    /**
     * Create a session for a logined user.
     * @param  Array $userData - an array which contains user's profile
     */
    private function createSession($userData, $allowAutoLogin)
    {
        $session    = new Container('itp_session');
        
        $session->offsetSet('isLogined', true);
        $session->offsetSet('allowAutoLogin', $allowAutoLogin);
        $session->offsetSet('uid', $userData['uid']);
        $session->offsetSet('username', $userData['username']);
        $session->offsetSet('email', $userData['email']);
        $session->offsetSet('isActivated', $userData['isActivated']);
        $session->offsetSet('userGroupID', $userData['userGroupID']);
        $session->offsetSet('lastTimeSignIn', $userData['lastTimeSignIn']);
    }


    /**
     * Handle asynchronous logout requests for the users.
     * @return a ViewModel object which contains HTML content
     */
    public function logoutAction()
    {
        $this->destroySession();

        return $this->sendRedirect();
    }

    /**
     * Send HTTP redirect reponse.
     * @param  String $redirectPath - the pasth to redirect
     * @return an HTTP redirect reponse object
     */
    private function sendRedirect($redirectPath = '')
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * Destroy a session for logined user.
     */
    private function destroySession()
    {
        $session    = new Container('itp_session');
        $session->getManager()->getStorage()->clear('itp_session');
    }
}
