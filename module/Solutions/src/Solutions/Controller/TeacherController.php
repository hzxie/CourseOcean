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
        $NUMBER_OF_TEACHERS_PER_PAGE = 12;
        $pageNumber                  = $this->params()->fromRoute('param', 1);
        $catelogySlug                = $this->params()->fromQuery('catelogy');
        $catelogyID                  = $this->getCatelogyID($catelogySlug);

        if ( $catelogySlug != null && $catelogyID == null ) {
            return $this->notFoundAction();
        }
        return array(
            'teachers'          => $this->getTeachers($pageNumber, $NUMBER_OF_TEACHERS_PER_PAGE, $catelogyID),
            'currentPageNumber' => $pageNumber,
            'numberOfPages'     => $this->getNumberOfPages($NUMBER_OF_TEACHERS_PER_PAGE, $catelogyID),
            'courseTypes'       => $this->getAllCourseTypes(),
            'catelogySlug'      => $catelogySlug,
        );
    }

    /**
     * Get the unique id of the course type by its slug.
     * @param  String $courseTypeSlug - the unique slug of the couse type
     * @return the unique id of the course type
     */
    private function getCatelogyID($courseTypeSlug)
    {
        $sm                 = $this->getServiceLocator();
        $courseTypeTable    = $sm->get('Solutions\Model\CourseTypeTable');
        $courseType         = $courseTypeTable->getCourseTypeID($courseTypeSlug);

        if ( $courseType == null ) {
            return null;
        }
        return $courseType->course_type_id;
    }

    /**
     * Get all information of the teachers in the database.
     * @return an array which contains general information of the
     *         teachers
     */
    private function getTeachers($pageNumber, $limit, $catelogyID)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');

        return $teacherTable->fetchAll($pageNumber, $limit, $catelogyID);
    }

    /**
     * Get total number of pages for the teachers.
     * @param  int $limit - max number of teachers in a page
     * @return an integer which stands for the total number of pages for 
     *         the teachers
     */
    private function getNumberOfPages($limit, $catelogyID)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');
        $numberOfTeachers   = $teacherTable->getNumberOfTeachers($catelogyID);

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
            'teacher'        => $teacherInfo,
            'lectures'       => $this->getLectureInfo($uid),
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
    private function getLectureInfo($uid)
    {
        $sm                 = $this->getServiceLocator();
        $lectureTable       = $sm->get('Solutions\Model\LectureTable');
        $lectureInfo        = $lectureTable->getLectureOfTeacher($uid);

        return $this->getLectureInfoArray($lectureInfo);
    }

    /**
     * Get a list of courses which are powered by the teacher within an array.
     * @param  ResultSet $resultSet - an array of objects of Course which contains 
     *         general information of the courses which are powered by the teacher
     * @return an array which contains general information of the courses 
     *         which are powered by the teacher
     */
    private function getLectureInfoArray($resultSet)
    {
        $lectureInfoArray   = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $lecture ) {
                array_push($lectureInfoArray, array(
                    'lecture_id'     => $lecture->lecture_id,
                    'lecture_name'   => $lecture->lecture_name,
                ));
            }
        }
        return $lectureInfoArray;
    }
}
