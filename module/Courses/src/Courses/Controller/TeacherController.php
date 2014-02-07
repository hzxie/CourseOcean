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
    public function pageAction()
    {
        $NUMBER_OF_TEACHERS_PER_PAGE = 15;
        $pageNumber                  = $this->params()->fromRoute('id', 1);
        return array(
            'teachers'          => $this->getAllTeachers($pageNumber, $NUMBER_OF_TEACHERS_PER_PAGE),
            'currentPageNumber' => $pageNumber,
            'numberOfPages'     => $this->getNumberOfPages($NUMBER_OF_TEACHERS_PER_PAGE),
            'courseTypes'       => $this->getAllCourseTypes(),
        );
    }

    /**
     * Get all information of the teachers in the database.
     * @return an array which contains general information of the
     *         teachers
     */
    private function getAllTeachers($pageNumber, $limit)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');

        return $teacherTable->fetchAll($pageNumber, $limit);
    }

    /**
     * Get total number of pages for the teachers.
     * @param  int $limit - max number of teachers in a page
     * @return an integer which stands for the total number of pages for 
     *         the teachers
     */
    private function getNumberOfPages($limit)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');
        $numberOfTeachers   = $teacherTable->getNumberOfTeachers();

        return ceil( $numberOfTeachers / $limit );
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
