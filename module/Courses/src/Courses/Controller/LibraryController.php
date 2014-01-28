<?php

namespace Courses\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\BasePath;

/**
 * The controller of the library of the courses.
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
        return array(
            'courses'   => $this->getAllCourses()
        );
    }

    private function getAllCourses()
    {
        $sm             = $this->getServiceLocator();
        $courseTable    = $sm->get('Courses\Model\CourseTable');

        return $courseTable->fetchAll();
    }

    public function detailAction($course_id = 0)
    {
    	return array();
    }
}
