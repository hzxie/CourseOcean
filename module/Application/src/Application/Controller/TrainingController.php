<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 课程的Controller, 用于完成课程的相关操作.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class TrainingController extends AbstractActionController
{
    /**
     * 通过课程类型的唯一英文缩写查找课程类型的唯一标识符.
     * @param  String $catelogySlug - 课程类型的唯一英文缩写
     * @return 课程类型的唯一标识符
     */
    private function getCourseTypeId($catelogySlug)
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseType         = $courseTypeTable->getCatelogyUsingId($catelogySlug);

        if ( $courseType != null ) {
            return $courseType->courseTypeId;
        } 
        return 0;
    }

    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    private function getResultSetArray($resultSet)
    {
        $returnArray = array();
        
        if ( $resultSet == null ) {
            return $returnArray;
        }
        foreach ( $resultSet as $rowSet ) {
            $rowArray = (array)$rowSet;
            array_push($returnArray, $rowArray);
        }
        return $returnArray;
    }

    /**
     * 显示近期开课页面.
     * @return 一个包含页面所需参数的数组
     */
    public function lecturesAction()
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->getAllCourseTypes();

        return array(
            'courseTypes'   => $courseTypes,
        );
    }

    /**
     * 获取近期开课的课程信息.
     * @return 一个包含近期开课课程信息的JSON数组
     */
    public function getLecturesAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE     = 10;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $lectureTable   = $serviceManager->get('Application\Model\LectureTable');
        $lectures       = null;

        if ( $courseTypeSlug === 'all' ) {
            $lectures   = $lectureTable->getAllLectures($offset, $NUMBER_OF_COURSES_PER_PAGE);
        } else if ( $courseTypeId != 0 ) {
            $lectures   = $lectureTable->getLecturesUsingCategory($courseTypeId, $offset, $NUMBER_OF_COURSES_PER_PAGE);
        }

        $result   = array(
            'isSuccessful'  => $lectures != null && $lectures->count() != 0,
            'lectures'      => $this->getResultSetArray($lectures),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    public function getLectureTotalPagesAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE     = 10;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);

        $serviceManager = $this->getServiceLocator();
        $lectureTable   = $serviceManager->get('Application\Model\LectureTable');
        $totalPages     = ceil($lectureTable->getCount($courseTypeId) / $NUMBER_OF_COURSES_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }
    
    /**
     * 显示课程库页面.
     * @return 一个包含页面所需参数的数组
     */
    public function coursesAction()
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->getAllCourseTypes();

        return array(
            'courseTypes'   => $courseTypes,
        );
    }

    /**
     * 获取课程列表.
     * @return 一个包含课程信息的JSON数组
     */
    public function getCoursesAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE     = 10;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $courses        = null;

        if ( $courseTypeSlug === 'all' ) {
            $courses    = $courseTable->getAllCourses($offset, $NUMBER_OF_COURSES_PER_PAGE);
        } else if ( $courseTypeId != 0 ) {
            $courses    = $courseTable->getCoursesUsingCategory($courseTypeId, $offset, $NUMBER_OF_COURSES_PER_PAGE);
        }

        $result   = array(
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取课程页面数量.
     * @return 一个包含课程页面数量的JSON数组.
     */
    public function getCourseTotalPagesAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE     = 10;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);

        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $totalPages     = ceil($courseTable->getCount($courseTypeId) / $NUMBER_OF_COURSES_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 显示课程的详细信息.
     * @return 一个包含页面所需参数的数组
     */
    public function courseAction()
    {
        $courseId       = $this->params()->fromQuery('courseId');
        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $course         = $courseTable->getCourseUsingCourseId($courseId);

        if ( $course == null ) {
            return $this->notFoundAction();
        }
        
        $teacherTable       = $serviceManager->get('Application\Model\TeacherTable');
        $teacher            = $teacherTable->getTeacherUsingUid($course->teacherId);
        $courseModuleTable  = $serviceManager->get('Application\Model\CourseModuleTable');
        $courseModules      = $courseModuleTable->getCourseModulesUsingCourseId($course->courseId);
        $lectureTable       = $serviceManager->get('Application\Model\LectureTable');
        $lectures           = $lectureTable->getLectureUsingCourseId($course->courseId);
        return array(
            'course'        => $course,
            'teacher'       => $teacher,
            'courseModules' => $this->getResultSetArray($courseModules),
            'lectures'      => $this->getResultSetArray($lectures),
        );
    }

    /**
     * 获取某个课程的详细信息.
     * @return 一个包含课程详细信息的JSON数组
     */
    public function getCourseAction()
    {
        $courseArray    = $this->courseAction();

        $result         = array(
            'isSuccessful'  => is_array($courseArray),
            'course'        => is_array($courseArray) ? $courseArray['course'] : null,
            'teacher'       => is_array($courseArray) ? $courseArray['teacher'] : null,
            'courseModules' => is_array($courseArray) ? $courseArray['courseModules'] : null,
            'lectures'      => is_array($courseArray) ? $courseArray['lectures'] : null,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    public function getCommentsAction()
    {
        $NUMBER_OF_COMMENTS_PER_PAGE    = 1;
        $courseId                       = $this->params()->fromQuery('courseId');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_COMMENTS_PER_PAGE;

        $serviceManager                 = $this->getServiceLocator();
        $commentTable                   = $serviceManager->get('Application\Model\CommentTable');
        $comments                       = $commentTable->getCommentUsingCourseId($courseId, $offset, $NUMBER_OF_COMMENTS_PER_PAGE);

        $result         = array(
            'isSuccessful'  => $comments != null && $comments->count() != 0,
            'comments'      => $this->getResultSetArray($comments),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取评论页面数量.
     * @return 一个包含评论页面数量的JSON数组.
     */
    public function getCommentTotalPagesAction()
    {
        $NUMBER_OF_COMMENTS_PER_PAGE    = 1;
        $courseId                       = $this->params()->fromQuery('courseId');

        $serviceManager                 = $this->getServiceLocator();
        $commentTable                   = $serviceManager->get('Application\Model\CommentTable');
        $totalPages                     = ceil($commentTable->getCountUsingCourseId($courseId) / $NUMBER_OF_COMMENTS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 显示讲师团队页面.
     * @return 一个包含页面所需参数的数组
     */
    public function teachersAction()
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->getAllCourseTypes();

        return array(
            'courseTypes'   => $courseTypes,
        );
    }

    /**
     * 获取讲师列表.
     * @return 一个包含讲师信息的JSON数组.
     */
    public function getTeachersAction()
    {
        $NUMBER_OF_TEACHERS_PER_PAGE    = 18;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_TEACHERS_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');
        $teachers       = null;

        if ( $courseTypeSlug === 'all' ) {
            $teachers   = $teacherTable->getAllApprovedTeachers($offset, $NUMBER_OF_TEACHERS_PER_PAGE);
        } else if ( $courseTypeId != 0 ) {
            $teachers   = $teacherTable->getTeachersUsingCategory($courseTypeId, $offset, $NUMBER_OF_TEACHERS_PER_PAGE);
        }

        $result   = array(
            'isSuccessful'  => $teachers != null && $teachers->count() != 0,
            'teachers'      => $this->getResultSetArray($teachers),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }
    
    /**
     * 获取讲师页面数量.
     * @return 一个包含讲师页面数量的JSON数组.
     */
    public function getTeacherTotalPagesAction()
    {
        $NUMBER_OF_TEACHERS_PER_PAGE    = 18;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);

        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');
        $totalPages     = ceil($teacherTable->getCount($courseTypeId) / $NUMBER_OF_TEACHERS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 显示讲师的详细信息.
     * @return 一个包含页面所需参数的数组
     */
    public function teacherAction()
    {
        $uid            = $this->params()->fromQuery('teacherId');
        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');
        $teacher        = $teacherTable->getTeacherUsingUid($uid);

        if ( $teacher == null ) {
            return $this->notFoundAction();
        }

        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $courses        = $courseTable->getCoursesUsingTeacherId($uid);
        return array(
            'teacher'   => $teacher,
            'courses'   => $this->getResultSetArray($courses),
        );
    }

    /**
     * 获取某个讲师的详细信息.
     * @return 一个包含某个讲师详细信息的JSON数组.
     */
    public function getTeacherAction()
    {
        $teacherArray   = $this->teacherAction();

        $result         = array(
            'isSuccessful'  => is_array($teacherArray),
            'teacher'       => is_array($teacherArray) ? $teacherArray['teacher'] : null,
            'courses'       => is_array($teacherArray) ? $teacherArray['courses'] : null,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    public function postsAction()
    {

    }

    public function postAction()
    {

    }
}