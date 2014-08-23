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
        } else if ( $user->password != $password ) {
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

    /**
     * 显示用户注册页面.
     * @return 一个包含页面所需信息的数组
     */
    public function registerAction()
    {
        if ( $this->isEnableAutoLogin() ) {
            return $this->sendRedirect('accounts/dashboard');
        }
        return array();
    }

    /**
     * 处理用户的注册请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function doRegisterAction()
    {
        $username       = $this->getRequest()->getPost('username');
        $email          = $this->getRequest()->getPost('email');
        $password       = $this->getRequest()->getPost('password');
        $userGroupSlug  = $this->getRequest()->getPost('userGroup');
        $userGroupId    = $this->getUserGroupId($userGroupSlug);

        $user  = array(
            'username'      => $username,
            'email'         => $email,
            'password'      => $password,
            'user_group_id' => $userGroupId,
        );
        $result   = $this->isProfileLegal($user);
        
        if ( $result['isSuccessful'] ) {
            $user['password']       = md5($user['password']);
            $serviceManager         = $this->getServiceLocator();
            $userTable              = $serviceManager->get('Application\Model\UserTable');
            $result['isSuccessful'] = $userTable->createUser($user);
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取用户组的唯一标识符.
     * @param  String $userGroupSlug - 用户组的唯一英文缩写
     * @return 用户组的唯一标识符
     */
    private function getUserGroupId($userGroupSlug)
    {
        $serviceManager     = $this->getServiceLocator();
        $userGroupTable     = $serviceManager->get('Application\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupUsingSlug($userGroupSlug);

        if ( $userGroup != null ) {
            return $userGroup->userGroupId;
        } 
        return 0;
    }

    /**
     * 检查用户提交的个人信息是否合法.
     * @param  Array  $profile - 用户个人信息.
     * @return 一个包含若干标志位的数组
     */
    private function isProfileLegal($profile)
    {
        $result = array(
            'isSuccessful'      => false,
            'isUsernameEmpty'   => empty($profile['username']),
            'isUsernameLegal'   => preg_match('/^[A-Za-z][A-Za-z0-9_]{5,15}$/', ($profile['username'])),
            'isUsernameExists'  => $this->isUsernameExists($profile['username']),
            'isEmailEmpty'      => empty($profile['email']),
            'isEmailLegal'      => preg_match('/^[A-Za-z0-9\._-]+@[A-Za-z0-9_-]+\.[A-Za-z0-9\._-]+$/', ($profile['email'])),
            'isEmailExists'     => $this->isEmailExists($profile['email']),
            'isPasswordEmpty'   => empty($profile['password']),
            'isPasswordLegal'   => strlen($profile['password']) >= 6 && strlen($profile['password']) <= 16,
            'isUserGroupLegal'  => $profile['user_group_id'] != 0,
        );
        $result['isSuccessful'] = !$result['isUsernameEmpty'] && $result['isUsernameLegal'] && !$result['isUsernameExists'] &&
                                  !$result['isEmailEmpty']    && $result['isEmailLegal']    && !$result['isEmailExists'] &&
                                  !$result['isPasswordEmpty'] && $result['isPasswordLegal'] &&  $result['isUserGroupLegal'];
        return $result;
    }

    /**
     * 检查用户名是否存在.
     * @param  String  $username - 用户名
     * @return 用户名是否存在
     */
    private function isUsernameExists($username)
    {
        $serviceManager     = $this->getServiceLocator();
        $userTable          = $serviceManager->get('Application\Model\UserTable');
        $user               = $userTable->getUserUsingUsername($username);

        if ( $user != null ) {
            return true;
        }
        return false;
    }

    /**
     * 检查电子邮件地址是否存在.
     * @param  String  $email - 电子邮件地址
     * @return 电子邮件地址是否存在
     */
    private function isEmailExists($email)
    {
        $serviceManager     = $this->getServiceLocator();
        $userTable          = $serviceManager->get('Application\Model\UserTable');
        $user               = $userTable->getUserUsingEmail($email);

        if ( $user != null ) {
            return true;
        }
        return false;
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
            'email'         => $session->offsetGet('email'),
        );
    }

    /**
     * 获得用户的个人资料.
     * @return 一个包含用户个人资料的JSON数组
     */
    public function getProfileAction()
    {
        $profile    = $this->getUserProfile();
        $uid        = $profile['uid'];
        $userGroup  = ucfirst($profile['userGroupSlug']);

        $function   = 'get'.$userGroup.'Profile';
        $extra      = $this->$function($uid);

        $result     = array(
            'isSuccessful'  => $extra != null,
            'profile'       => $profile,
            'extra'         => $extra,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 获取讲师用户的个人资料.
     * @param  int $uid - 用户唯一标识符
     * @return 一个Teacher对象, 包含讲师用户的个人资料
     */
    private function getTeacherProfile($uid)
    {
        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');
        $teacher        = $teacherTable->getTeacherUsingUid($uid);

        return $teacher;
    }

    public function getTeachingFieldsAction()
    {
        $profile            = $this->getUserProfile();
        $uid                = $profile['uid'];
        $serviceManager     = $this->getServiceLocator();
        $teachingFieldTable = $serviceManager->get('Application\Model\TeachingFieldTable');
        $teachingFields     = $teachingFieldTable->getTeachingFieldsOfUsingUid($uid);

        $result   = array(
            'isSuccessful'      => $teachingFields != null,
            'teachingFields'    => $this->getResultSetArray($teachingFields),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 编辑用户个人资料.
     * @return 一个包含若干标志位的JSON数组
     */
    public function editProfileAction()
    {
        $profile    = $this->getUserProfile();
        $uid        = $profile['uid'];
        $userGroup  = ucfirst($profile['userGroupSlug']);
        $function   = 'edit'.$userGroup.'Profile';

        $result   = $this->$function($uid);
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 编辑讲师用户个人资料.
     * @return 一个包含若干标志位的数组
     */
    private function editTeacherProfile()
    {
        $name           = strip_tags($this->getRequest()->getPost('name'));
        $company        = strip_tags($this->getRequest()->getPost('company'));
        $region         = strip_tags($this->getRequest()->getPost('region'));
        $province       = strip_tags($this->getRequest()->getPost('province'));
        $city           = strip_tags($this->getRequest()->getPost('city'));
        $phone          = strip_tags($this->getRequest()->getPost('phone'));
        $weibo          = strip_tags($this->getRequest()->getPost('weibo'));
        $brief          = strip_tags($this->getRequest()->getPost('brief'));
        $teachingFields = strip_tags($this->getRequest()->getPost('teachingFields'));

        $profile        = $this->getUserProfile();
        $uid            = $profile['uid'];
        $teacher        = array(
            'uid'                   => $uid,
            'teacher_is_approved'   => false,
            'teacher_name'          => $name,
            'teacher_company'       => $company,
            'teacher_region'        => $region,
            'teacher_province'      => $province,
            'teacher_city'          => $city,
            'teacher_phone'         => $phone,
            'teacher_weibo'         => $weibo,
            'teacher_brief'         => $brief,
        );
        $result = $this->isTeacherProfileLegal($teacher);

        if ( $result['isSuccessful'] ) {
            $serviceManager     = $this->getServiceLocator();
            $teacherTable       = $serviceManager->get('Application\Model\TeacherTable');
            $teachingFieldTable = $serviceManager->get('Application\Model\TeachingFieldTable');
            $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
            $isTeacherExists    = $teacherTable->getTeacherUsingUid($uid);
            $courseTypes        = $this->getCourseTypes($courseTypeTable->getAllCourseTypes());

            if ( $isTeacherExists != null ) {
                $teacherTable->updateTeacher($teacher);
            } else {
                $teacherTable->createTeacher($teacher);
            }
            $teachingFieldTable->uid($updateTeachingField, $teachingFields, $courseTypes);
        }
        return $result;
    }

    /**
     * 检查讲师用户所提交的信息是否合法.
     * @param  Array  $teacher - 一个包含讲师信息的数组
     * @return 讲师用户所提交的信息是否合法
     */
    private function isTeacherProfileLegal($teacher)
    {
        $result = array(
            'isSuccessful'              => false,
            'isTeacherNameEmpty'        => empty($teacher['teacher_name']),
            'isTeacherNameLegal'        => mb_strlen($teacher['teacher_name'], 'utf-8') <= 16,
            'isTeacherCompanyEmpty'     => empty($teacher['teacher_company']),
            'isTeacherCompanyLegal'     => mb_strlen($teacher['teacher_company'], 'utf-8') <= 64,
            'isTeacherRegionEmpty'      => empty($teacher['teacher_region']),
            'isTeacherProvinceEmpty'    => empty($teacher['teacher_province']),
            'isTeacherPhoneEmpty'       => empty($teacher['teacher_phone']),
            'isTeacherPhoneLegal'       => preg_match('/^[0-9+]{8,16}$/', $teacher['teacher_phone']),
            'isTeacherWeiboLegal'       => strlen($teacher['teacher_weibo']) <= 32,
            'isTeacherBriefEmpty'       => empty($teacher['teacher_brief']),
            'isTeacherBriefLegal'       => mb_strlen($teacher['teacher_brief'], 'utf-8') <= 640,
        );
        $result['isSuccessful'] = !$result['isTeacherNameEmpty']    &&  $result['isTeacherNameLegal'] &&
                                  !$result['isTeacherCompanyEmpty'] &&  $result['isTeacherCompanyLegal'] &&
                                  !$result['isTeacherRegionEmpty']  && !$result['isTeacherProvinceEmpty'] &&
                                  !$result['isTeacherPhoneEmpty']   &&  $result['isTeacherPhoneLegal'] &&  
                                   $result['isTeacherWeiboLegal']   && !$result['isTeacherBriefEmpty'] &&
                                   $result['isTeacherBriefLegal'];
        return $result;
    }

    /**
     * 获取CourseType的 CourseTypeSlug => CourseType对象 映射数组.
     * @param  ResultSet $courseTypes - 一个ResultSet对象, 包含若干CourseType对象
     * @return 一个CourseType的HashMap
     */
    private function getCourseTypes($courseTypes)
    {
        $hashMap = array();
        foreach ( $courseTypes as $courseType ) {
            $hashMap[$courseType->courseTypeSlug] = $courseType;
        }
        return $hashMap;
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
    public function createCommentAction()
    {
        $profile        = $this->getUserProfile();
        $lectureId      = $this->getRequest()->getPost('lectureId');
        $commentRanking = $this->getRequest()->getPost('commentRanking');
        $commentDetail  = strip_tags($this->getRequest()->getPost('commentDetail'));

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

    /**
     * 处理讲师用户开设课程的请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createLectureAction()
    {
        $courseId               = strip_tags($this->getRequest()->getPost('courseId'));
        $startTime              = strip_tags($this->getRequest()->getPost('startTime'));
        $endTime                = strip_tags($this->getRequest()->getPost('endTime'));
        $region                 = strip_tags($this->getRequest()->getPost('region'));
        $province               = strip_tags($this->getRequest()->getPost('province'));
        $city                   = strip_tags($this->getRequest()->getPost('city'));
        $address                = strip_tags($this->getRequest()->getPost('address'));
        $minCapcity             = strip_tags($this->getRequest()->getPost('minCapcity'));
        $maxCapcity             = strip_tags($this->getRequest()->getPost('maxCapcity'));
        $expense                = strip_tags($this->getRequest()->getPost('expense'));
        $precautions            = strip_tags($this->getRequest()->getPost('precautions'));

        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];
        $lecture                = array(
            'course_id'             => $courseId,
            'lecture_start_time'    => $startTime,
            'lecture_end_time'      => $endTime,
            'lecture_region'        => $region,
            'lecture_province'      => $province,
            'lecture_city'          => $city,
            'lecture_address'       => $address,
            'lecture_min_capcity'   => $minCapcity,
            'lecture_max_capcity'   => $maxCapcity,
            'lecture_expense'       => $expense,
            'lecture_precautions'   => $precautions,
        );
        $result = $this->isLectureLegal($lecture);

        if ( $result['isSuccessful'] ) {
            $serviceManager         = $this->getServiceLocator();
            $lectureTable           = $serviceManager->get('Application\Model\LectureTable');
            $lectureId              = $lectureTable->createLecture($lecture);
            $result                += array(
                'lectureId'         => $lectureId,
            );
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 检查所提交的培训课信息是否合法.
     * @param  Array  $lecture - 一个包含培训课信息的数组
     * @return 培训课信息是否合法
     */
    private function isLectureLegal($lecture)
    {
        $result = array(
            'isSuccessful'          => false,
            'isCourseIdLegal'       => $this->isCourseOwner($lecture['course_id']),
            'isStartTimeEmpty'      => empty($lecture['lecture_start_time']),
            'isStartTimeLegal'      => strtotime($lecture['lecture_start_time']) > strtotime('now'),
            'isEndTimeEmpty'        => empty($lecture['lecture_end_time']),
            'isEndTimeLegal'        => strtotime($lecture['lecture_end_time']) > strtotime($lecture['lecture_start_time']),
            'isRegionEmpty'         => empty($lecture['lecture_region']),
            'isProvinceEmpty'       => empty($lecture['lecture_province']),
            'isCityEmpty'           => empty($lecture['lecture_city']),
            'isAddressEmpty'        => empty($lecture['lecture_address']),
            'isMinCapcityEmpty'     => empty($lecture['lecture_min_capcity']),
            'isMinCapcityLegal'     => intval($lecture['lecture_min_capcity']) && $lecture['lecture_min_capcity'] > 0,
            'isMaxCapcityEmpty'     => empty($lecture['lecture_max_capcity']),
            'isMaxCapcityLegal'     => intval($lecture['lecture_max_capcity']) && $lecture['lecture_max_capcity'] >= $lecture['lecture_max_capcity'],
            'isExpenseEmpty'        => empty($lecture['lecture_expense']),
            'isExpenseLegal'        => intval($lecture['lecture_expense']) && $lecture['lecture_expense'] > 0,
            'isPrecautionsEmpty'    => empty($lecture['lecture_precautions']),
        );
        $result['isSuccessful'] = $result['isCourseIdLegal']   && !$result['isStartTimeEmpty'] &&
                                  $result['isStartTimeLegal']  && !$result['isEndTimeEmpty'] &&
                                  $result['isEndTimeLegal']    && !$result['isRegionEmpty'] &&
                                 !$result['isProvinceEmpty']   && !$result['isCityEmpty'] &&
                                 !$result['isAddressEmpty']    && !$result['isMinCapcityEmpty'] &&
                                  $result['isMinCapcityLegal'] && !$result['isMaxCapcityEmpty'] &&
                                  $result['isMaxCapcityLegal'] && !$result['isExpenseEmpty'] &&
                                  $result['isExpenseLegal']    && !$result['isPrecautionsEmpty'];
        return $result;
    }

    /**
     * 检查讲师用户是否有编辑该课程的权限.
     * @param  int  $courseId - 课程的唯一标识符
     * @return 讲师用户是否有编辑该课程的权限
     */
    private function isCourseOwner($courseId)
    {
        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $courseId       = $courseId;
        $course         = $courseTable->getCourseUsingCourseId($courseId);
        
        $profile        = $this->getUserProfile();
        $teacherId      = $profile['uid'];

        return ( $course != null && $course->teacherId == $teacherId );
    }

    /**
     * 处理讲师用户的创建课程计划请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createLectureScheduleAction()
    {
        $lectureId              = $this->getRequest()->getPost('lectureId');
        $courseModuleId         = $this->getRequest()->getPost('courseModuleId');
        $startTime              = $this->getRequest()->getPost('startTime');
        $endTime                = $this->getRequest()->getPost('endTime');
        $lectureSchedule        = array(
            'lecture_id'                => $lectureId,
            'course_module_id'          => $courseModuleId,
            'course_module_start_time'  => $startTime,
            'course_module_end_time'    => $endTime,
        );

        $serviceManager         = $this->getServiceLocator();
        $lectureScheduleTable   = $serviceManager->get('Application\Model\LectureScheduleTable');
        $result   = array(
            'isSuccessful'  => $lectureScheduleTable->createLectureSchedule($lectureSchedule),
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
     * 处理讲师用户创建课程的请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createCourseAction()
    {
        $courseName             = strip_tags($this->getRequest()->getPost('courseName'));
        $courseTypeSlug         = strip_tags($this->getRequest()->getPost('courseTypeSlug'));
        $courseCycle            = strip_tags($this->getRequest()->getPost('courseCycle'));
        $courseAudience         = strip_tags($this->getRequest()->getPost('courseAudience'));
        $courseBrief            = strip_tags($this->getRequest()->getPost('courseBrief'));
        $courseObjective        = strip_tags($this->getRequest()->getPost('courseObjective'));
        $courseModules          = strip_tags($this->getRequest()->getPost('courseModules'));
        $courseTypeId           = $this->getCourseTypeId($courseTypeSlug);

        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];
        $course                 = array(
            'course_name'       => $courseName,
            'course_type_id'    => $courseTypeId,
            'teacher_id'        => $teacherId,
            'course_cycle'      => $courseCycle,
            'course_audience'   => $courseAudience,
            'course_brief'      => $courseBrief,
            'course_objective'  => $courseObjective,
        );
        $result = $this->isCourseLegal($course);
        
        if ( $result['isSuccessful'] ) {
            $serviceManager         = $this->getServiceLocator();
            $courseTable            = $serviceManager->get('Application\Model\CourseTable');
            $courseId               = $courseTable->createCourse($course);
            $result['isSuccessful'] = $courseCompositionTable->updateCourseComposition($courseId, $courseModules);
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    public function updateCourseAction()
    {
        $courseId               = $this->getRequest()->getPost('courseId');
        $courseTypeSlug         = strip_tags($this->getRequest()->getPost('courseTypeSlug'));
        $courseCycle            = strip_tags($this->getRequest()->getPost('courseCycle'));
        $courseAudience         = strip_tags($this->getRequest()->getPost('courseAudience'));
        $courseBrief            = strip_tags($this->getRequest()->getPost('courseBrief'));
        $courseObjective        = strip_tags($this->getRequest()->getPost('courseObjective'));
        $courseModules          = strip_tags($this->getRequest()->getPost('courseModules'));
        $courseTypeId           = $this->getCourseTypeId($courseTypeSlug);
        
        $serviceManager         = $this->getServiceLocator();
        $courseTable            = $serviceManager->get('Application\Model\CourseTable');
        $courseCompositionTable = $serviceManager->get('Application\Model\CourseCompositionTable');
        $course                 = $courseTable->getCourseUsingCourseId($courseId);
        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];

        
        if ( $course == null || $course->teacherId != $teacherId ) {
            $result = array(
                'isSuccessful'  => false,
            );
        } else {
            $course                 = array(
                'course_id'         => $courseId,
                'course_name'       => $course->courseName,
                'course_type_id'    => $courseTypeId,
                'teacher_id'        => $teacherId,
                'course_cycle'      => $courseCycle,
                'course_audience'   => $courseAudience,
                'course_brief'      => $courseBrief,
                'course_objective'  => $courseObjective,
            );
            $result = $this->isCourseLegal($course);
            
            if ( $result['isSuccessful'] ) {
                $result['isSuccessful'] = $courseTable->updateCourse($course);
                $result['isSuccessful'] = $courseCompositionTable->updateCourseComposition($courseId, $courseModules);
            }
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 检查所提交的课程信息是否合法.
     * @param  Array  $course - 一个包含课程信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function isCourseLegal($course)
    {
        $result = array(
            'isCourseNameEmpty'     => empty($course['course_name']),
            'isCourseNameLegal'     => strlen($course['course_name']) <= 128,
            'isCourseTypeIdLegal'   => $course['course_type_id'] != 0,
            'isCourseCycleEmpty'    => empty($course['course_cycle']),
            'isCourseCycleLegal'    => is_numeric($course['course_cycle']) && $course['course_cycle'] > 0,
            'isCourseAudienceEmpty' => empty($course['course_audience']),
            'isCourseAudienceLegal' => strlen($course['course_audience']) <= 256,
            'isCourseBriefEmpty'    => empty($course['course_brief']),
            'isCourseObjectiveEmpty'=> empty($course['course_objective']),
        );
        $result['isSuccessful'] = !$result['isCourseNameEmpty']     &&  $result['isCourseNameLegal'] &&
                                   $result['isCourseTypeIdLegal']   && !$result['isCourseCycleEmpty'] &&
                                   $result['isCourseCycleLegal']    && !$result['isCourseAudienceEmpty'] &&
                                   $result['isCourseAudienceLegal'] && !$result['isCourseBriefEmpty'] &&
                                  !$result['isCourseObjectiveEmpty'];
        return $result;
    }

    /**
     * 获取某个课程模块的信息.
     * @return 一个包含课程模块信息的数组
     */
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
     * 获取某个课程所包含/某个讲师开设的课程模块.
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
     * 处理讲师用户创建课程模块的请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createCourseModuleAction()
    {
        $courseModuleName       = strip_tags($this->getRequest()->getPost('courseModuleName'));
        $courseModuleCycle      = strip_tags($this->getRequest()->getPost('courseModuleCycle'));
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

    /**
     * 修改课程模块的信息.
     * @return 一个包含若干标志位的JSON数组
     */
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
            $result = array(
                'isSuccessful'  => false,
            );
        } else {
            $courseModule = array(
                'course_module_id'      => $courseModuleId,
                'course_module_name'    => $courseModule->courseModuleName,
                'course_module_cycle'   => $courseModuleCycle,
                'teacher_id'            => $teacherId,
                'course_module_brief'   => $courseModuleBrief,
                'course_module_outline' => $courseModuleOutline,
            );
            $result = $this->isCourseModuleLegal($courseModule);
            
            if ( $result['isSuccessful'] ) {
                $result['isSuccessful'] = $courseModuleTable->updateCourseModule($courseModule);
            }
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 检查所提交的课程模块信息是否合法.
     * @param  Array  $courseModule - 一个包含课程模块信息的数组
     * @return 一个包含若干标志位的数组
     */
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
}