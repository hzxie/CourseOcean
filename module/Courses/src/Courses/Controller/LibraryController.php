<?php

namespace Courses\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\BasePath;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class LibraryController extends AbstractActionController
{
	/**
	 * Default method to call in the controller.
	 * 
	 * @return a ViewModel object which contains HTML content
	 */
	public function indexAction()
    {
        return array();
    }

    public function viewAction($course_id = 0)
    {
    	return array();
    }
}
