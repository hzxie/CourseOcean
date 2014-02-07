<?php

namespace Courses\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\BasePath;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class RecentlyController extends AbstractActionController
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
            'lectures'          => $this->getAllLectures($pageNumber, $NUMBER_OF_COURSES_PER_PAGE),
            'currentPageNumber' => $pageNumber,
            'numberOfPages'     => $this->getNumberOfPages($NUMBER_OF_COURSES_PER_PAGE),
            'courseTypes'       => $this->getAllCourseTypes(),
        );
    }

    /**
     * Get all lectures in the database.
     * @param  int $pageNumber - current number of the page
     * @param  int $limit - max number of lectures in a page
     * @return an array which contains all general information 
     *         of the lectures
     */
    private function getAllLectures($pageNumber, $limit)
    {
        $sm             = $this->getServiceLocator();
        $lectureTable   = $sm->get('Courses\Model\LectureTable');

        return $lectureTable->fetchAll($pageNumber, $limit);
    }

    /**
     * Get total number of pages for the lectures.
     * @param  int $limit - max number of lectures in a page
     * @return an integer which stands for the total number of pages for 
     *         the lectures
     */
    private function getNumberOfPages($limit)
    {
        $sm                 = $this->getServiceLocator();
        $lectureTable       = $sm->get('Courses\Model\LectureTable');
        $numberOfLectures   = $lectureTable->getNumberOfLectures();

        return ceil( $numberOfLectures / $limit );
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
     * Display detail information of a certain lecture.
     * @param  int $courseID - the unique id of the course.
     * @return a ViewModel object which contains HTML content
     */
    public function detailAction()
    {
        $lectureID      = $this->params()->fromRoute('id');
        $lectureInfo    = $this->getLectureDetailInfo($lectureID);

        if ( $lectureInfo == null ) {
            return $this->notFoundAction();
        }
        return array(
            'lecture'   => $lectureInfo,
            'teacher'   => $this->getTeacherInfo($lectureInfo['uid']),
        );
    }

    
    private function getLectureDetailInfo($lectureID)
    {
        $lectureInfo        = array();
        $generalInfo        = $this->getGeneralInfo($lectureID);
        $lectureMetaInfo    = $this->getLectureMetaInfo($lectureID);

        $courseID           = $generalInfo->course_id;
        $courseMetaInfo     = $this->getCourseMetaInfo($courseID);

        $lectureInfo   += $this->getGeneralInfoArray($generalInfo);
        $lectureInfo   += $this->getMetaInfoArray($lectureMetaInfo);
        $lectureInfo   += $this->getMetaInfoArray($courseMetaInfo);

        return $lectureInfo;
    }

    /**
     * Get general information of a certain lecture.
     * @param  int $lectureID - the unique id of the lecture
     * @return an object of Lecture which contains general information 
     *         of a cerain lecture
     */
    private function getGeneralInfo($lectureID)
    {
        $sm                 = $this->getServiceLocator();
        $lectureTable       = $sm->get('Courses\Model\LectureTable');

        return $lectureTable->getGeneralInfo($lectureID);
    }

    /**
     * Get general information of a certain lecture in an array.
     * @param  Course $resultSet - an object of Course which contains
     *         general information of a certain lecture
     * @return an array which contains general information of a certain
     *         lecture
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
     * Get detail information of a certain lecture.
     * @param  int $lectureID - the unique id of the lecture
     * @return an array of an object of CourseMeta which contains detail
     *         information of a certain lecture
     */
    private function getLectureMetaInfo($lectureID)
    {
        $sm                 = $this->getServiceLocator();
        $lectureMetaTable   = $sm->get('Courses\Model\LectureMetaTable');

        return $lectureMetaTable->getMetaData($lectureID);
    }

    /**
     * Get detail information of a certain course.
     * @param  int $courseID - the unique id of the course
     * @return an array of an object of CourseMeta which contains detail
     *         information of a certain course
     */
    private function getCourseMetaInfo($courseID)
    {
        $sm                 = $this->getServiceLocator();
        $courseMetaTable    = $sm->get('Courses\Model\CourseMetaTable');

        return $courseMetaTable->getMetaData($courseID);
    }

    /**
     * Get detail information of a certain lecture in an array.
     * @param  Array $resultSet - an array of an object of CourseMeta which 
     *         contains detail information of a certain lecture
     * @return an array which contains detail information of a certain
     *         lecture
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
