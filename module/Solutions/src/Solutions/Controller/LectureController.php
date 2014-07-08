<?php

namespace Solutions\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Helper\BasePath;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class LectureController extends AbstractActionController
{
    /**
     * Default method to call in the controller.
     * 
     * @return a ViewModel object which contains HTML content
     */
    public function pageAction()
    {
        $NUMBER_OF_LECTURES_PER_PAGE = 10;
        $pageNumber                  = $this->params()->fromRoute('param', 1);
        return array(
            'lectures'          => $this->getAllLectures($pageNumber, $NUMBER_OF_LECTURES_PER_PAGE),
            'currentPageNumber' => $pageNumber,
            'numberOfPages'     => $this->getNumberOfPages($NUMBER_OF_LECTURES_PER_PAGE),
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
        $lectureTable   = $sm->get('Solutions\Model\LectureTable');

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
        $lectureTable       = $sm->get('Solutions\Model\LectureTable');
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
        $courseTypeTable    = $sm->get('Solutions\Model\CourseTypeTable');

        return $courseTypeTable->fetchAll();
    }

    /**
     * Display detail information of a certain lecture.
     * @param  int $courseID - the unique id of the course.
     * @return a ViewModel object which contains HTML content
     */
    public function detailAction()
    {
        $lectureID      = $this->params()->fromRoute('param');
        $lectureInfo    = $this->getLectureDetailInfo($lectureID);
        $uid            = $this->isLogined();

        if ( $lectureInfo == null ) {
            return $this->notFoundAction();
        }
        return array(
            'lecture'       => $lectureInfo,
            'isLogined'     => $uid,
            'isAttended'    => $this->isAttended($uid, $lectureInfo['lecture_id']),
            'teacher'       => $this->getTeacherInfo($lectureInfo['teacher_id']),
            'comments'      => $this->getCommentInfo($lectureInfo['lecture_id']),
        );
    }

    /**
     * Get detail information of a certain lecture.
     * @param  int $lectureID - the unique id of a certain lecture
     * @return an array which contains all information of a certain 
     *         lecture
     */
    private function getLectureDetailInfo($lectureID)
    {
        $lectureInfo        = array();
        $generalInfo        = $this->getGeneralInfo($lectureID);
        $courseInfo         = $this->getCourseInfo($lectureID);

        if ( $generalInfo == null ) {
            return $lectureInfo;
        }
        $courseID           = $generalInfo->lecture_id;

        $lectureInfo       += $this->getGeneralInfoArray($generalInfo);
        $lectureInfo       += array(
            'courses'       => $this->getCourseInfoArray($courseInfo),
        );

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
        $lectureTable       = $sm->get('Solutions\Model\LectureTable');

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


    private function getCourseInfo($lectureID)
    {
        $sm             = $this->getServiceLocator();
        $courseTable    = $sm->get('Solutions\Model\CourseTable');

        return $courseTable->getCoursesOfLecture($lectureID);
    }

    private function getCourseInfoArray($resultSet)
    {
        $courses        = array();
        foreach ( $resultSet as $course ) {
            array_push( $courses, $this->getGeneralInfoArray($course) );
        }
        return $courses;
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

    private function getCommentInfo($lectureID)
    {
        $sm                 = $this->getServiceLocator();
        $commentTable       = $sm->get('Solutions\Model\CommentTable');
        $commentInfo        = $commentTable->getCommentsOfCourse($lectureID, 0, 10);

        return $this->getCommentInfoArray($commentInfo);
    }

    private function getCommentInfoArray($resultSet)
    {
        $comments   = array();
        foreach ( $resultSet as $comment ) {
            array_push( $comments, $this->getGeneralInfoArray($comment) );
        }
        return $comments;
    }

    /**
     * Check if the user has logined.
     * @return true if the user has logined
     */
    private function isLogined()
    {
        $session    = new Container('itp_session');
        return $session->offsetGet('uid');
    }

    /**
     * Check if the user has attended a lecture.
     * @param  int $uid - the unique id of the user
     * @param  int $lectureID - the unique id of the lecture
     * @return true if the user has attended the lecture
     */
    private function isAttended($uid, $lectureID)
    {
        $sm                 = $this->getServiceLocator();
        $attendanceTable    = $sm->get('Solutions\Model\LectureAttendanceTable');
        
        return $attendanceTable->isAttended($uid, $lectureID);
    }

    /**
     * Handle asynchronous attending lecture requests for the users.
     * @return a HTTP response object which contains JSON data infers
     *         whether the attendance operation is successful
     */
    public function attendLectureAction()
    {
        $lectureID      = $this->getRequest()->getPost('lectureID');
        $uid            = $this->isLogined();

        /* Check if the attend action is out of date */
        $isOutOfDate = false;
        /* Check if the lecture is full */

        $result         = array(
            'isSuccessful'  => $this->attendLecture($uid, $lectureID, $isOutOfDate),
        );

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * Handle asynchronous attending lecture requests for the users.
     * @param  int $uid - the unique id of the user
     * @param  int $lectureID - the unique id of the lecture
     * @return true if the operation is successful
     */
    private function attendLecture($uid, $lectureID)
    {
        if ( $uid == 0 || $lectureID == 0 ) {
            return false;
        }

        $sm                 = $this->getServiceLocator();
        $attendanceTable    = $sm->get('Solutions\Model\LectureAttendanceTable');
        $attendanceRecord  = array(
            'uid'           => $uid,
            'lecture_id'    => $lectureID,
        );
        
        return $attendanceTable->attendLecture($attendanceRecord);
    }
}
