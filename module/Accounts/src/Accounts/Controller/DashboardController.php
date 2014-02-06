<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class DashboardController extends AbstractActionController
{
	/**
	 * Default method to call in the controller.
	 * 
	 * It will check if the user has logined, if not, it will 
	 * send a HTTP redirect response.
	 * 
	 * @return a ViewModel object which contains HTML content
	 */
	public function indexAction()
    {
    	if ( !$this->isAllowedToAccess() ) {
    		return $this->sendRedirect('accounts/login');
    	}
        return array(
        	'profile'	=> $this->getUserData(),
        );
    }

    /**
     * Check if the user has logined.
     * @return true if the user has logined
     */
	private function isAllowedToAccess()
	{
		$session    = new Container('itp_session');
		return $session->offsetExists('isLogined');
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
	 * Get user profile from the session.
	 * @return an array which contains user's profile
	 */
	private function getUserData()
	{
		$session    = new Container('itp_session');

		$userData = array(
            'uid'               => $session->offsetGet('uid'),
            'username'          => $session->offsetGet('username'),
            'email'             => $session->offsetGet('email'),
            'userGroupID'     	=> $session->offsetGet('userGroupID'),
            'lastTimeSignIn'  	=> $session->offsetGet('lastTimeSignIn'),
        );
		return $userData;
	}
}
