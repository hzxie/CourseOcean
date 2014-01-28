<?php

namespace Courses\Controller;

use Zend\Http\PhpEnvironment\Response;
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

    /**
     * Get all courses in the database.
     * @return an array which contains all general information 
     *         of the courses
     */
    private function getAllCourses()
    {
        $sm             = $this->getServiceLocator();
        $courseTable    = $sm->get('Courses\Model\CourseTable');

        return $courseTable->fetchAll();
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
            $response   = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }
    	return array(
            'course'    => $courseInfo,
            'teacher'   => $this->getTeacherInfo($courseInfo['uid']),
        );
    }

    /**
     * Get detail information of a certain course.
     * @param  int $courseID - the unique id of a certain couese
     * @return an array which contains all information of a certain 
     *         course
     */
    private function getCourseDetailInfo($courseID)
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

    private function getTeacherInfo($uid)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');
        $teacherInfo        = $teacherTable->getTeacherInfo($uid);

        return $this->getTeacherInfoArray($teacherInfo);
    }

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
