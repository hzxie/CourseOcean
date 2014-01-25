<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Helper\BasePath;

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
    		return $this->sendRedirect('login');
    	}
        return array();
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
	private function sendRedirect($redirectPath)
	{
		$response = new Response();
        $response->setStatusCode(302);
        $response->getHeaders()
                 ->addHeaderLine('Location', $redirectPath);

        return $response;
	}
}
