<?php

namespace Courses\Controller;

use Zend\Http\PhpEnvironment\Response;
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
	public function pageAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $pageNumber                 = $this->params()->fromRoute('id', 1);
        return array(
            'courses'           => $this->getAllCourses($pageNumber, $NUMBER_OF_COURSES_PER_PAGE),
            'currentPageNumber' => $pageNumber,
            'numberOfPages'     => $this->getNumberOfPages($NUMBER_OF_COURSES_PER_PAGE),
            'courseTypes'       => $this->getAllCourseTypes(),
        );
    }

    /**
     * Get all courses in the database.
     * @param  int $pageNumber - current number of the page
     * @param  int $limit - max number of courses in a page
     * @return an array which contains all general information 
     *         of the courses
     */
    private function getAllCourses($pageNumber, $limit)
    {
        $sm             = $this->getServiceLocator();
        $courseTable    = $sm->get('Courses\Model\CourseTable');

        return $courseTable->fetchAll($pageNumber, $limit);
    }

    /**
     * Get total number of pages for the courses.
     * @param  int $limit - max number of courses in a page
     * @return an integer which stands for the total number of pages for 
     *         the courses
     */
    private function getNumberOfPages($limit)
    {
        $sm                 = $this->getServiceLocator();
        $courseTable        = $sm->get('Courses\Model\CourseTable');
        $numberOfCourses    = $courseTable->getNumberOfCourses();

        return ceil( $numberOfCourses / $limit );
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

    /**
     * Display detail information of a certain course.
     * @param  int $courseID - the unique id of the course.
     * @return a ViewModel object which contains HTML content
     */
    public function detailAction()
    {
        $courseID       = $this->params()->fromRoute('id');
        $courseInfo     = $this->getCourseDetailInfo($courseID);

        if ( $courseInfo == null ) {
            return $this->notFoundAction();
        }
    	return array(
            'course'    => $courseInfo,
            'teacher'   => $this->getTeacherInfo($courseInfo['uid']),
        );
    }

    /**
     * Get detail information of a certain course.
     * @param  int $courseID - the unique id of a certain course
     * @return an array which contains all information of a certain 
     *         course
     */
    public function getCourseDetailInfo($courseID)
    {
        $courseInfo     = array();
        $generalInfo    = $this->getGeneralInfo($courseID);
        $metaInfo       = $this->getMetaInfo($courseID);

        $courseInfo    += $this->getGeneralInfoArray($generalInfo);
        $courseInfo    += $this->getMetaInfoArray($metaInfo);

        return $courseInfo;
    }

    /**
     * Get general information of a certain course.
     * @param  int $courseID - the unique id of the course
     * @return an object of Course which contains general information 
     *         of a cerain course
     */
    private function getGeneralInfo($courseID)
    {
        $sm                 = $this->getServiceLocator();
        $courseTable        = $sm->get('Courses\Model\CourseTable');

        return $courseTable->getGeneralInfo($courseID);
    }

    /**
     * Get general information of a certain course in an array.
     * @param  Course $resultSet - an object of Course which contains
     *         general information of a certain course
     * @return an array which contains general information of a certain
     *         course
     */
    private function getGeneralInfoArray($resultSet)
    {
        $generalInfoArray   = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $key => $value ) {
                $generalInfoArray[ $key ] = $value;
            }
        }
        return $generalInfoArray;
    }

    /**
     * Get detail information of a certain course.
     * @param  int $courseID - the unique id of the course
     * @return an array of an object of CourseMeta which contains detail
     *         information of a certain course
     */
    private function getMetaInfo($courseID)
    {
        $sm                 = $this->getServiceLocator();
        $courseMetaTable    = $sm->get('Courses\Model\CourseMetaTable');

        return $courseMetaTable->getMetaData($courseID);
    }

    /**
     * Get detail information of a certain course in an array.
     * @param  Array $resultSet - an array of an object of CourseMeta which 
     *         contains detail information of a certain course
     * @return an array which contains detail information of a certain
     *         course
     */
    private function getMetaInfoArray($resultSet)
    {
        $metaInfoArray      = array();
        
        if ( $resultSet != null ) {
            foreach ( $resultSet as $resultRow ) {
                $metaInfoArray[ $resultRow->meta_key ] 
                                            = $resultRow->meta_value;
            }
        }
        return $metaInfoArray;
    }

    /**
     * Get information of a teacher of a course.
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
}
