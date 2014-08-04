<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 账户的Controller, 用于完成账户的相关操作.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class AccountsController extends AbstractActionController
{
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
     * 显示用户登录页面.
     * @return 返回一个包含页面所需参数的数组
     */
    public function loginAction()
    {
        if ( $this->isEnableAutoLogin() ) {
            return $this->sendRedirect('accounts/dashboard');
        }
        $this->destroySession();

        $forwardUrl = $this->params()->fromQuery('forward', '/accounts/dashboard');
        return array(
            'forwardUrl'    => $forwardUrl,
        );
    }

    /**
     * 检查用户是否已经启用了自动登录功能.
     * @return 用户是否已经启用了自动登录功能
     */
    private function isEnableAutoLogin()
    {
        $session            = new Container('itp_session');

        $isEnableAutoLogin  = $session->offsetGet('allowAutoLogin');
        return $isEnableAutoLogin;
    }

    /**
     * HTTP重定向请求.
     * @param  String $redirectPath - 重定向的相对路径
     * @return HTTP重定向请求的对象
     */
    private function sendRedirect($redirectPath)
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * 获取网站的基础路径(如localhost/itp).
     * @return 网站的基础路径
     */
    private function basePath()
    {
        $renderer   = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url        = $renderer->basePath();
        return $url;
    }

    /**
     * 销毁用户的Session.
     */
    private function destroySession()
    {
        $session    = new Container('itp_session');
        $session->getManager()->getStorage()->clear('itp_session');
    }

    /**
     * 处理用户的登录请求.
     * @return 包含若干标志位的JSON数组
     */
    public function doLoginAction()
    {
        $username       = $this->getRequest()->getPost('username');
        $password       = $this->getRequest()->getPost('password');
        $allowAutoLogin = $this->getRequest()->getPost('remember_me');

        $result     = array(
            'isSuccessful'      => false,
            'isUsernameEmpty'   => empty($username),
            'isPasswordEmpty'   => empty($password),
            'isAccountValid'    => false,
        );

        if ( !$result['isUsernameEmpty'] && !$result['isPasswordEmpty'] ) {
            $user = $this->verifyAccount($username, $password);

            if ( $user != null ) {
                $result['isAccountValid']   = true;
                $result['isSuccessful']     = true;
                $this->createSession($user, $allowAutoLogin);
            }
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 验证账户有效性.
     * @param  String $username - 用户名
     * @param  String $password - 密码
     * @return 一个用户对象或空引用
     */
    private function verifyAccount($username, $password)
    {
        if ( empty($username) || empty($password) ) {
            return false;
        }

        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');
        $user           = null;

        if ( !$this->isEmailAddress($username) ) {
            $user = $userTable->getUserUsingUsername($username);
        } else {
            $user = $userTable->getUserUsingEmail($username);
        }
        if ( $user == null ) {
            return null;
        } else if ( $user->password != md5($password) ) {
            return null;
        }
        return $user;
    }

    /**
     * 验证所使用的用户名是否为电子邮件地址.
     * @param  String  $username - 用户登录所使用的用户名
     * @return 所使用的用户名是否为电子邮件地址
     */
    private function isEmailAddress($username)
    {
        return strpos($username, '@');
    }

    /**
     * 为已登录的用户创建Session.
     * @param  User    $user           - 一个用户对象
     * @param  boolean $allowAutoLogin - 是否允许自动登陆
     */
    private function createSession($user, $allowAutoLogin)
    {
        $session    = new Container('itp_session');
        
        $session->offsetSet('isLogined', true);
        $session->offsetSet('allowAutoLogin', $allowAutoLogin);
        $session->offsetSet('uid', $user->uid);
        $session->offsetSet('username', $user->username);
        $session->offsetSet('email', $user->email);
        $session->offsetSet('userGroupSlug', $user->userGroupSlug);
    }

    /**
     * 处理用户的注销请求.
     * @return 一个HTTP重定向请求
     */
    public function logoutAction()
    {
        $this->destroySession();
        return $this->sendRedirect('accounts/login');
    }

    public function registerAction()
    {
        return array();
    }

    public function doRegisterAction()
    {

    }

    /**
     * 显示个人中心页面.
     * @return 一个包含页面所需信息的数组.
     */
    public function dashboardAction()
    {
        if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/login');
        }

        return array(
            'profile'   => $this->getUserProfile(),
        );
    }

    /**
     * 检查用户是否已经登录.
     * @return 用户是否已经登录
     */
    private function isAllowedToAccess()
    {
        $session    = new Container('itp_session');
        return $session->offsetExists('isLogined');
    }

    /**
     * 获取所请求页面的内容.
     * @return 包含页面内容的HTML字符串
     */
    public function getPageContentAction()
    {
        $pageName = $this->params()->fromQuery('pageName');

        $profile  = $this->getUserProfile();
        $view     = new ViewModel(array(
            'profile'   => $this->getUserProfile(),
        ));
        $view->setTerminal(true);
        $view->setTemplate("application/accounts/dashboard/$pageName.phtml");
        return $view;
    }

    /**
     * 获取用户所在的用户组.
     * @return 一个包含用户基本信息的数组
     */
    private function getUserProfile()
    {
        $session    = new Container('itp_session');
        return array(
            'uid'           => $session->offsetGet('uid'),
            'username'      => $session->offsetGet('username'),
            'userGroupSlug' => $session->offsetGet('userGroupSlug'),
        );
    }

    /**
     * 获取某个用户参加培训的信息.
     * @return 一个包含用户参加培训信息的JSON数组
     */
    public function getLectureAttendanceAction()
    {
        $profile                    = $this->getUserProfile();
        $NUMBER_OF_RECORDS_PER_PAGE = 10;
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_RECORDS_PER_PAGE;

        $serviceManager     = $this->getServiceLocator();
        $attendanceTable    = $serviceManager->get('Application\Model\LectureAttendanceTable');
        $attendanceRecords  = $attendanceTable->getLectureAttendanceUsingUid($profile['uid'], $offset, $NUMBER_OF_RECORDS_PER_PAGE);

        $result = array(
            'isSuccessful'  => $attendanceRecords != null && $attendanceRecords->count() != 0,
            'records'       => $this->getResultSetArray($attendanceRecords),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取某个用户参加培训的信息的页面数量.
     * @return 一个包含某个用户参加培训的信息页面数量的JSON数组
     */
    public function getLectureAttendanceTotalPagesAction()
    {
        $profile                    = $this->getUserProfile();
        $NUMBER_OF_RECORDS_PER_PAGE = 10;

        $serviceManager     = $this->getServiceLocator();
        $attendanceTable    = $serviceManager->get('Application\Model\LectureAttendanceTable');
        $totalPages         = ceil($attendanceTable->getCountUsingUid($profile['uid']) / $NUMBER_OF_RECORDS_PER_PAGE);

        $result = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 处理用户评价课程的请求.
     * @return 包含若干标志位的JSON数组
     */
    public function addCommentAction()
    {
        $profile        = $this->getUserProfile();
        $lectureId      = $this->params()->fromQuery('lectureId');
        $commentRanking = $this->params()->fromQuery('commentRanking');
        $commentDetail  = strip_tags($this->params()->fromQuery('commentDetail'));
        $comment        = array(
            'lecture_id'        => $lectureId,
            'reviewer_uid'      => $profile['uid'],
            'comment_ranking'   => $commentRanking,
            'comment_detail'    => $commentDetail,
        );

        $isSuccessful   = $this->isCommentLegal($comment);
        if ( $isSuccessful ) {
            $serviceManager = $this->getServiceLocator();
            $commentTable   = $serviceManager->get('Application\Model\CommentTable');
            $isSuccessful   = $commentTable->createComment($comment);
        }

        $result = array(
            'isSuccessful'  => $isSuccessful,
            'commentRanking'=> $commentRanking,
            'commentDetail' => $commentDetail,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 检查所提交评论信息的合法性.
     * @param  Array  $comment - 一个包含评论信息的数组
     * @return 提交评论信息的合法性
     */
    private function isCommentLegal($comment)
    {
        $serviceManager     = $this->getServiceLocator();
        $attendanceTable    = $serviceManager->get('Application\Model\LectureAttendanceTable');
        $attendance         = $attendanceTable->getLectureAttendanceUsingUidAndLectureId($comment['reviewer_uid'], $comment['lecture_id']);
        
        $result = array(
            'isSuccessful'      => false,
            'isAttended'        => $attendance != null,
            'hasCommented'      => $attendance != null && $attendance->commentDetail != null,
            'isRankingLegal'    => $comment['comment_ranking'] > 0 && $comment['comment_ranking'] <= 5,
            'isDetailEmpty'     => empty($comment['comment_detail']),
        );
        $result['isSuccessful'] = $result['isAttended'] && $result['isRankingLegal'] && 
                                 !$result['hasCommented'] && !$result['isDetailEmpty'];
        return $result['isSuccessful'];
    }

    /**
     * 获取某个讲师用户所开设课程的信息.
     * @return 一个包含讲师用户所开设课程的信息的JSON数组
     */
    public function getOpeningCoursesAction()
    {
        $profile                    = $this->getUserProfile();
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $serviceManager     = $this->getServiceLocator();
        $courseTable        = $serviceManager->get('Application\Model\CourseTable');
        $courses            = $courseTable->getCoursesUsingTeacherId($profile['uid'], $offset, $NUMBER_OF_COURSES_PER_PAGE);

        $result = array(
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取某个讲师用户所开设课程的页面数量.
     * @return 一个包含讲师用户所开设课程的页面数量的JSON数组
     */
    public function getOpeningCourseTotalPagesAction()
    {
        $profile                    = $this->getUserProfile();
        $NUMBER_OF_COURSES_PER_PAGE = 10;

        $serviceManager     = $this->getServiceLocator();
        $courseTable        = $serviceManager->get('Application\Model\CourseTable');
        $totalPages         = ceil($courseTable->getCountUsingTeacherId($profile['uid']) / $NUMBER_OF_COURSES_PER_PAGE);

        $result = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

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

    public function getCourseTypesAction()
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->getAllCourseTypes();

        $result = array(
            'isSuccessful'  => $courseTypes != null && $courseTypes->count() != 0,
            'courseTypes'   => $this->getResultSetArray($courseTypes),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取某个讲师用户所开设培训课的信息.
     * @return 一个包含讲师用户所开设培训课的信息的JSON数组
     */
    public function getOpeningLecturesAction()
    {
        $profile                        = $this->getUserProfile();
        $NUMBER_OF_LECTURES_PER_PAGE    = 10;
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_LECTURES_PER_PAGE;

        $serviceManager     = $this->getServiceLocator();
        $lectureTable       = $serviceManager->get('Application\Model\LectureTable');
        $lectures           = $lectureTable->getLecturesUsingTeacherId($profile['uid'], $offset, $NUMBER_OF_LECTURES_PER_PAGE);

        $result = array(
            'isSuccessful'  => $lectures != null && $lectures->count() != 0,
            'lectures'      => $this->getResultSetArray($lectures),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取某个讲师开设培训课的页面数量
     * @return 一个包含讲师开设培训课的页面数量的JSON数组
     */
    public function getOpeningLectureTotalPagesAction()
    {
        $profile                        = $this->getUserProfile();
        $NUMBER_OF_LECTURES_PER_PAGE    = 10;

        $serviceManager     = $this->getServiceLocator();
        $lectureTable       = $serviceManager->get('Application\Model\LectureTable');
        $totalPages         = ceil($lectureTable->getCountUsingTeacherId($profile['uid']) / $NUMBER_OF_LECTURES_PER_PAGE);

        $result = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    public function addLectureAction()
    {

    }

    private function isLectureLegal()
    {

    }

    public function getCourseModuleAction()
    {
        $courseModuleId     = $this->params()->fromQuery('courseModuleId');

        $serviceManager     = $this->getServiceLocator();
        $courseModuleTable  = $serviceManager->get('Application\Model\CourseModuleTable');
        $courseModule       = null;

        if ( $courseModuleId != null ) {
            $profile        = $this->getUserProfile();
            $teacherId      = $profile['uid'];
            $courseModule   = $courseModuleTable->getCoureModuleUsingCourseModuleId($courseModuleId);

            if ( $courseModule == null || $courseModule->teacherId != $teacherId ) {
                $courseModule = null;
            }
        }

        $result = array(
            'isSuccessful'  => $courseModule != null,
            'courseModule'  => $courseModule,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取某个课程所包含的课程模块.
     * @return 一个包含课程模块信息的JSON数组
     */
    public function getCourseModulesAction()
    {
        $courseId   = $this->params()->fromQuery('courseId');

        $serviceManager     = $this->getServiceLocator();
        $courseModuleTable  = $serviceManager->get('Application\Model\CourseModuleTable');
        $courseModules      = null;
        
        if ( $courseId == null ) {
            $profile        = $this->getUserProfile();
            $teacherId      = $profile['uid'];
            $courseModules  = $courseModuleTable->getCourseModulesUsingTeacherId($teacherId);
        } else {
            $courseModules  = $courseModuleTable->getCourseModulesUsingCourseId($courseId);
        }

        $result = array(
            'isSuccessful'  => $courseModules != null && $courseModules->count() != 0,
            'courseModules' => $this->getResultSetArray($courseModules),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取某个课程的信息.
     * @return 一个包含课程信息的JSON数组
     */
    public function getCourseAction()
    {
        $courseId   = $this->params()->fromQuery('courseId');

        $serviceManager     = $this->getServiceLocator();
        $courseTable        = $serviceManager->get('Application\Model\CourseTable');
        $course             = null;

        if ( $courseId != null ) {
            $profile        = $this->getUserProfile();
            $teacherId      = $profile['uid'];
            $course         = $courseTable->getCourseUsingCourseId($courseId);

            if ( $course == null || $course->teacherId != $teacherId ) {
                $course     = null;
            }
        }

        $result = array(
            'isSuccessful'  => $course != null,
            'course'        => $course,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 处理讲师用户创建课程模块的请求.
     * @return 一个包含若干标志位的JSON数组.
     */
    public function addCourseModuleAction()
    {
        $courseModuleName       = strip_tags($this->getRequest()->getPost('courseModuleName'));
        $courseModuleCycle      = $this->getRequest()->getPost('courseModuleCycle');
        $courseModuleBrief      = strip_tags($this->getRequest()->getPost('courseModuleBrief'));
        $courseModuleOutline    = strip_tags($this->getRequest()->getPost('courseModuleOutline'));
        
        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];
        $courseModule           = array(
            'course_module_name'    => $courseModuleName,
            'course_module_cycle'   => $courseModuleCycle,
            'teacher_id'            => $teacherId,
            'course_module_brief'   => $courseModuleBrief,
            'course_module_outline' => $courseModuleOutline,
        );
        $result = $this->isCourseModuleLegal($courseModule);
        
        if ( $result['isSuccessful'] ) {
            $serviceManager     = $this->getServiceLocator();
            $courseModuleTable  = $serviceManager->get('Application\Model\CourseModuleTable');
            $courseModuleId     = $courseModuleTable->createCourseModule($courseModule);
            $result            += array(
                'courseModuleId'    => $courseModuleId,
            );
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    public function updateCourseModuleAction()
    {
        $courseModuleId         = $this->getRequest()->getPost('courseModuleId');
        $courseModuleCycle      = $this->getRequest()->getPost('courseModuleCycle');
        $courseModuleBrief      = strip_tags($this->getRequest()->getPost('courseModuleBrief'));
        $courseModuleOutline    = strip_tags($this->getRequest()->getPost('courseModuleOutline'));

        $serviceManager         = $this->getServiceLocator();
        $courseModuleTable      = $serviceManager->get('Application\Model\CourseModuleTable');
        $courseModule           = $courseModuleTable->getCoureModuleUsingCourseModuleId($courseModuleId);
        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];

        if ( $courseModule == null || $courseModule->teacherId != $teacherId ) {
            return array(
                'isSuccessful'  => false,
            );
        }
        $courseModule = array(
            'course_module_id'      => $courseModuleId,
            'course_module_name'    => $courseModule->courseModuleName,
            'course_module_cycle'   => $courseModuleCycle,
            'teacher_id'            => $teacherId,
            'course_module_brief'   => $courseModuleBrief,
            'course_module_outline' => $courseModuleOutline,
        );
        $result                 = $this->isCourseModuleLegal($courseModule);
        
        if ( $result['isSuccessful'] ) {
            $result['isSuccessful'] = $courseModuleTable->updateCourseModule($courseModule);
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    public function isCourseModuleLegal($courseModule)
    {
        $result = array(
            'isSuccessful'                  => false,
            'isCourseModuleNameEmpty'       => empty($courseModule['course_module_name']),
            'isCourseModuleLegal'           => mb_strlen($courseModule['course_module_name'], 'utf-8') <= 128,
            'isCourseModuleCycleEmpty'      => empty($courseModule['course_module_cycle']),
            'isCourseModuleCycleLegal'      => is_numeric($courseModule['course_module_cycle']) && $courseModule['course_module_cycle'] > 0,
            'isCourseModuleBriefEmpty'      => empty($courseModule['course_module_brief']),
            'isCourseModuleOutlineEmpty'    => empty($courseModule['course_module_outline']),
        );

        $result['isSuccessful'] = !$result['isCourseModuleNameEmpty']  &&  $result['isCourseModuleLegal'] &&
                                  !$result['isCourseModuleCycleEmpty'] &&  $result['isCourseModuleCycleLegal'] &&
                                  !$result['isCourseModuleBriefEmpty'] && !$result['isCourseModuleOutlineEmpty'];
        return $result;
    }

    public function addCourseAction()
    {

    }

    private function isCourseLegal()
    {

    }

}