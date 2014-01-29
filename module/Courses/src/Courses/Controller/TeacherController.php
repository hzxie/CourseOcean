<?php

namespace Courses\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\BasePath;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class TeacherController extends AbstractActionController
{
	/**
	 * Default method to call in the controller.
	 * 
	 * @return a ViewModel object which contains HTML content
	 */
	public function indexAction()
    {
        return array(
            'teachers'      => $this->getAllTeachers(),
            'courseTypes'   => $this->getAllCourseTypes(),
        );
    }

    /**
     * Get all information of the teachers in the database.
     * @return an array which contains general information of the
     *         teachers
     */
    private function getAllTeachers()
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');

        return $teacherTable->fetchAll();
    }

    /**
     * Get all information of course types in the database.
     * @return an array of objects of CourseType which contains 
     *         informtaion of course type in the database
     */
    private function getAllCourseTypes()
    {
        $sm                 = $this->getServiceLocator();
        $courseTypeTable    = $sm->get('Courses\Model\CourseTypeTable');

        return $courseTypeTable->fetchAll();
    }

    public function detailAction()
    {
    	return array();
    }
}
