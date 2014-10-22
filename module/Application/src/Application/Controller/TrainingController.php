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
        $courseType         = $courseTypeTable->getCatelogyUsingSlug($catelogySlug);

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
        $startTime                      = $this->params()->fromQuery('startTime');
        $startTime                      = ( $startTime ? $startTime : date('Y-m-d H:i:s') );
        $endTime                        = $this->params()->fromQuery('endTime');
        $region                         = $this->params()->fromQuery('region');
        $province                       = $this->params()->fromQuery('province');
        $city                           = $this->params()->fromQuery('city');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $lectureTable   = $serviceManager->get('Application\Model\LectureTable');
        $lectures       = $lectureTable->getLecturesUsingFilters($courseTypeId, $startTime, $endTime, 
                                         $region, $province, $city, $offset, $NUMBER_OF_COURSES_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $lectures != null && $lectures->count() != 0,
            'lectures'      => $this->getResultSetArray($lectures),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取近期开课课程页面数量.
     * @return 一个包含课程页面数量的JSON数组.
     */
    public function getLectureTotalPagesAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE     = 10;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $startTime                      = $this->params()->fromQuery('startTime');
        $startTime                      = ( $startTime ? $startTime : date('Y-m-d H:i:s') );
        $endTime                        = $this->params()->fromQuery('endTime');
        $region                         = $this->params()->fromQuery('region');
        $province                       = $this->params()->fromQuery('province');
        $city                           = $this->params()->fromQuery('city');
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);

        $serviceManager = $this->getServiceLocator();
        $lectureTable   = $serviceManager->get('Application\Model\LectureTable');
        $totalPages     = ceil($lectureTable->getCountUsingFilters($courseTypeId, $startTime, $endTime, 
                                              $region, $province, $city) / $NUMBER_OF_COURSES_PER_PAGE);

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
     * 显示课程会话的详细信息页面.
     * @return 一个包含页面所需参数的数组
     */
    public function lectureAction()
    {
        $lectureId      = $this->params()->fromQuery('lectureId');
        $serviceManager = $this->getServiceLocator();
        $lectureTable   = $serviceManager->get('Application\Model\LectureTable');
        $lecture        = $lectureTable->getLectureUsingLectureId($lectureId);

        if ( $lecture == null ) {
            return $this->notFoundAction();
        }
        
        $teacherTable       = $serviceManager->get('Application\Model\TeacherTable');
        $teacher            = $teacherTable->getTeacherUsingUid($lecture->teacherId);
        $courseModuleTable  = $serviceManager->get('Application\Model\CourseModuleTable');
        $courseModules      = $courseModuleTable->getCourseModulesUsingLectureId($lecture->lectureId);
        $lectures           = $lectureTable->getLecturesUsingCourseId($lecture->courseId);
        return array(
            'lecture'       => $lecture,
            'teacher'       => $teacher,
            'courseModules' => $this->getResultSetArray($courseModules),
            'lectures'      => $this->getResultSetArray($lectures),
            'attendance'    => $this->getLectureAttendance($lecture->lectureId),
        );
    }

    /**
     * 获取课程会话的的详细信息.
     * @return 一个包含课程会话详细信息的JSON数组
     */
    private function getLectureAction()
    {
        $lectureArray   = $this->lectureAction();

        $result         = array(
            'isSuccessful'  => is_array($lectureArray),
            'lecture'       => is_array($lectureArray) ? $lectureArray['lecture'] : null,
            'teacher'       => is_array($lectureArray) ? $lectureArray['teacher'] : null,
            'courseModules' => is_array($lectureArray) ? $lectureArray['courseModules'] : null,
            'lectures'      => is_array($lectureArray) ? $lectureArray['lectures'] : null,
            'attendance'    => is_array($lectureArray) ? $lectureArray['attendance'] : null,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取课程会话报名情况.
     * @param  int $lectureId - 课程会话的唯一标识符
     * @return 一个包含课程会话报名情况的数组
     */
    private function getLectureAttendance($lectureId)
    {
        $attendance = array(
            'hasAttended'   => false,
            'participants'  => 0,
        );

        $serviceManager             = $this->getServiceLocator();
        $lectureAttendanceTable     = $serviceManager->get('Application\Model\LectureAttendanceTable');
        $attendance['participants'] = intval($lectureAttendanceTable->getCountUsingLectureId($lectureId));

        $uid = $this->getLoginUserUid();
        if ( $uid != 0 ) {
            $attendance['hasAttended'] = $lectureAttendanceTable->getLectureAttendanceUsingUidAndLectureId($uid, $lectureId);
        }
        return $attendance;
    }

    /**
     * 获取已登录用户的用户唯一标识符
     * @return 用户的用户唯一标识符
     */
    private function getLoginUserUid()
    {
        $session    = new Container('itp_session');
        return $session->offsetGet('uid');
    }

    /**
     * 处理用户参加课程的请求.
     * @return 一个包含操作是否成功标志位的JSON数组
     */
    public function attendLectureAction()
    {
        $result                 = array(
            'isSuccessful'      => false,
        );
        $uid                    = $this->getLoginUserUid();
        $lectureId              = $this->params()->fromQuery('lectureId');
        $participants           = $this->params()->fromQuery('participants');

        if ( $uid != 0 ) {
            $serviceManager         = $this->getServiceLocator();
            $lectureTable           = $serviceManager->get('Application\Model\LectureTable');
            $lecture                = $lectureTable->getLectureUsingLectureId($lectureId);
            $maxCapcity             = $lecture->maxCapcity;
            $lectureAttendanceTable = $serviceManager->get('Application\Model\LectureAttendanceTable');
            $lectureAttendance      = array(
                'uid'           => $uid,
                'lecture_id'    => $lectureId,
                'serial_code'   => $this->getSerialCode($uid, $lectureId),
                'total_times'   => $participants,
                'remain_times'  => $participants,
            );

            $alreadyParticipants    = $lectureAttendanceTable->getCountUsingLectureId($lectureId);
            if ( $alreadyParticipants + $participants <= $maxCapcity && $uid != $lecture->teacherId ) {
                $result['isSuccessful'] = $lectureAttendanceTable->createLectureAttendance($lectureAttendance);
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取报名序列号.
     * @param  int $uid       - 用户的唯一标识符
     * @param  int $lectureId - 课程会话的唯一标识符
     * @return 报名序列号
     */
    private function getSerialCode($uid, $lectureId)
    {
        return uniqid($uid + $lectureId);
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
        $totalPages     = ceil($courseTable->getCountUsingCategory($courseTypeId) / $NUMBER_OF_COURSES_PER_PAGE);

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
        $lectures           = $lectureTable->getLecturesUsingCourseId($course->courseId);
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
        $NUMBER_OF_COMMENTS_PER_PAGE    = 20;
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
        $NUMBER_OF_COMMENTS_PER_PAGE    = 20;
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

    /**
     * 通过培训动态分类的唯一英文缩写查找培训动态分类的唯一标识符.
     * @param  String $catelogySlug - 培训动态分类的唯一英文缩写
     * @return 培训动态分类的唯一标识符
     */
    private function getPostCategoryId($catelogySlug)
    {
        $serviceManager     = $this->getServiceLocator();
        $postCategoryTable  = $serviceManager->get('Application\Model\PostCategoryTable');
        $postCategory       = $postCategoryTable->getCatelogyUsingSlug($catelogySlug);

        if ( $postCategory != null ) {
            return $postCategory->postCategoryId;
        } 
        return 0;
    }

    /**
     * 显示培训动态的页面.
     * @return 一个包含页面所需参数的数组
     */
    public function postsAction()
    {
        $serviceManager     = $this->getServiceLocator();
        $postCategoryTable  = $serviceManager->get('Application\Model\PostCategoryTable');
        $postCategories     = $postCategoryTable->getAllPostCategories();

        return array(
            'postCategories'    => $postCategories,
        );
    }

    /**
     * 获取培训动态.
     * @return 一个包含培训动态的JSON数组
     */
    public function getPostsAction()
    {
        $NUMBER_OF_POSTS_PER_PAGE       = 10;
        $postCategorySlug               = $this->params()->fromQuery('category');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $postCategoryId                 = $this->getPostCategoryId($postCategorySlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_POSTS_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $postTable      = $serviceManager->get('Application\Model\PostTable');
        $posts          = null;

        if ( $postCategorySlug === 'all' ) {
            $posts   = $postTable->getAllPosts($offset, $NUMBER_OF_POSTS_PER_PAGE);
        } else if ( $postCategoryId != 0 ) {
            $posts   = $postTable->getPostsUsingCategory($postCategoryId, $offset, $NUMBER_OF_POSTS_PER_PAGE);
        }

        $result   = array(
            'isSuccessful'  => $posts != null && $posts->count() != 0,
            'posts'         => $this->getResultSetArray($posts),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取培训动态的页面数量.
     * @return 一个包含培训动态页面数量的JSON数组
     */
    public function getPostTotalPagesAction()
    {
        $NUMBER_OF_POSTS_PER_PAGE       = 10;
        $postCategorySlug               = $this->params()->fromQuery('category');
        $postCategoryId                 = $this->getPostCategoryId($postCategorySlug);

        $serviceManager = $this->getServiceLocator();
        $postTable      = $serviceManager->get('Application\Model\PostTable');
        $totalPages     = ceil($postTable->getCount($postCategoryId) / $NUMBER_OF_POSTS_PER_PAGE);

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
     * 显示培训动态的详细信息.
     * @return 一个包含页面所需参数的数组
     */
    public function postAction()
    {
        $postId         = $this->params()->fromQuery('postId');
        $serviceManager = $this->getServiceLocator();
        $postTable      = $serviceManager->get('Application\Model\PostTable');
        $post           = $postTable->getPostUsingPostId($postId);

        if ( $post == null ) {
            return $this->notFoundAction();
        }

        $posts          = $postTable->getPostsUsingCategory($post->postCategoryId, 0, 10);
        return array(
            'post'      => $post,
            'posts'     => $this->getResultSetArray($posts),
        );
    }

    /**
     * 获取某个培训动态的详细信息.
     * @return 一个包含培训动态详细信息的JSON数组
     */
    public function getPostAction()
    {
        $postArray      = $this->postAction();

        $result         = array(
            'isSuccessful'  => is_array($postArray),
            'post'          => is_array($postArray) ? $postArray['post'] : null,
            'posts'         => is_array($postArray) ? $postArray['posts'] : null,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }
}