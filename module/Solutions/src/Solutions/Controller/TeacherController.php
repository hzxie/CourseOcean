<?php

namespace Solutions\Controller;

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
        $NUMBER_OF_TEACHERS_PER_PAGE = 16;
        $pageNumber                  = $this->params()->fromRoute('param', 1);
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
        $courseTypeTable    = $sm->get('Solutions\Model\CourseTypeTable');

        return $courseTypeTable->fetchAll();
    }

    /**
     * Get detail information of a teacher.
     * @return a ViewModel object which contains HTML content
     */
    public function detailAction()
    {
        $uid                = $this->params()->fromRoute('param');
        $teacherInfo        = $this->getTeacherInfo($uid);

        if ( $teacherInfo == null ) {
            return $this->notFoundAction();
        }
        return array(
            'teacher'       => $teacherInfo,
            'courses'       => $this->getCourseInfo($uid),
        );
    }

    /**
     * Get information of a teacher.
     * @param  int $uid - the unique id of the user
     * @return an object of Teacher which contains information of a 
     *         certain teacher
     */
    private function getTeacherInfo($uid)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');
        $teacherInfo        = $teacherTable->getTeacherInfo($uid);

        return $this->getTeacherInfoArray($teacherInfo);
    }

    /**
     * Get information of a teacher of a course within an array.
     * @param  Teacher $resultSet - an object of Teacher which contains 
     *         information of a certain teacher
     * @return an array which contains information of a certain teacher
     */
    private function getTeacherInfoArray($resultSet)
    {
        $teacherInfoArray   = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $key => $value ) {
                $teacherInfoArray[ $key ] = $value;
            }
        }
        return $teacherInfoArray;
    }

    /**
     * Get a list of courses which are powered by the teacher.
     * @param  int $uid - the unique id of the teacher
     * @return an array which contains general information of the courses 
     *         which are powered by the teacher
     */
    private function getCourseInfo($uid)
    {
        $sm                 = $this->getServiceLocator();
        $courseTable        = $sm->get('Solutions\Model\CourseTable');
        $courseInfo         = $courseTable->getCourseOfTeacher($uid);

        return $this->getCourseInfoArray($courseInfo);
    }

    /**
     * Get a list of courses which are powered by the teacher within an array.
     * @param  ResultSet $resultSet - an array of objects of Course which contains 
     *         general information of the courses which are powered by the teacher
     * @return an array which contains general information of the courses 
     *         which are powered by the teacher
     */
    private function getCourseInfoArray($resultSet)
    {
        $courseInfoArray    = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $course ) {
                array_push($courseInfoArray, array(
                    'course_id'     => $course->course_id,
                    'course_name'   => $course->course_name,
                ));
            }
        }
        return $courseInfoArray;
    }
}
