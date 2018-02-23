<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

use Application\Controller\BaseController;
use Application\Helper\MailSender;
use Application\Model\CommentTable;
use Application\Model\CompanyTable;
use Application\Model\CompanyFieldTable;
use Application\Model\CourseTable;
use Application\Model\CourseCompositionTable;
use Application\Model\CourseModuleTable;
use Application\Model\CourseTypeTable;
use Application\Model\EmailValidationTable;
use Application\Model\LectureTable;
use Application\Model\LectureAttendanceTable;
use Application\Model\LectureScheduleTable;
use Application\Model\PersonTable;
use Application\Model\PositionTable;
use Application\Model\RequirementTable;
use Application\Model\TeacherTable;
use Application\Model\TeachingFieldTable;
use Application\Model\UserTable;
use Application\Model\UserGroupTable;

/**
 * 账户的Controller, 用于完成账户的相关操作.
 * 
 * @author Haozhe Xie <cshzxie@gmail.com>
 */
class AccountsController extends BaseController {
    /**
     * AccountsController的构造函数. 
     */
    public function __construct(CommentTable $commentTable,
        CompanyTable $companyTable, 
        CompanyFieldTable $companyFieldTable,
        CourseTable $courseTable,
        CourseCompositionTable $courseCompositionTable,
        CourseModuleTable $courseModuleTable,
        CourseTypeTable $courseTypeTable,
        EmailValidationTable $emailValidationTable,
        LectureTable $lectureTable,
        LectureAttendanceTable $lectureAttendanceTable,
        LectureScheduleTable $lectureScheduleTable,
        PersonTable $personTable,
        PositionTable $positionTable,
        RequirementTable $requirementTable,
        TeacherTable $teacherTable,
        TeachingFieldTable $teachingFieldTable,
        UserTable $userTable,
        UserGroupTable $userGroupTable) {
        $this->commentTable             = $commentTable;
        $this->companyTable             = $companyTable;
        $this->companyFieldTable        = $companyFieldTable;
        $this->courseTable              = $courseTable;
        $this->courseCompositionTable   = $courseCompositionTable;
        $this->courseModuleTable        = $courseModuleTable;
        $this->courseTypeTable          = $courseTypeTable;
        $this->emailValidationTable     = $emailValidationTable;
        $this->lectureTable             = $lectureTable;
        $this->lectureAttendanceTable   = $lectureAttendanceTable;
        $this->lectureScheduleTable     = $lectureScheduleTable;
        $this->personTable              = $personTable;
        $this->positionTable            = $positionTable;
        $this->requirementTable         = $requirementTable;
        $this->teacherTable             = $teacherTable;
        $this->teachingFieldTable       = $teachingFieldTable;
        $this->userTable                = $userTable;
        $this->userGroupTable           = $userGroupTable;
    }

    /**
     * 显示用户登录页面.
     * @return 返回一个包含页面所需参数的数组
     */
    public function loginAction() {
        if ( $this->isEnableAutoLogin() ) {
            return $this->redirect()->toRoute('accounts', [
                'controller'    => 'accounts',
                'action'        => 'dashboard'
            ]);
        }
        $this->destroySession();

        $forwardUrl = $this->params()->fromQuery('forward', '/accounts/dashboard');
        return new ViewModel([
            'forwardUrl'    => $forwardUrl,
        ]);
    }

    /**
     * 检查用户是否已经启用了自动登录功能.
     * @return 用户是否已经启用了自动登录功能
     */
    private function isEnableAutoLogin() {
        $session            = new Container('co_session');
        $isEnableAutoLogin  = $session->offsetGet('allowAutoLogin');
        return $isEnableAutoLogin;
    }

    /**
     * 销毁用户的Session.
     */
    private function destroySession() {
        $session    = new Container('co_session');
        $session->getManager()->getStorage()->clear('co_session');
    }

    /**
     * 处理用户的登录请求.
     * @return 包含若干标志位的JSON数组
     */
    public function doLoginAction() {
        $username       = $this->getRequest()->getPost('username');
        $password       = $this->getRequest()->getPost('password');
        $allowAutoLogin = $this->getRequest()->getPost('rememberMe', false);

        $result     = [
            'isSuccessful'      => false,
            'isUsernameEmpty'   => empty($username),
            'isPasswordEmpty'   => empty($password),
            'isAccountValid'    => false,
        ];

        if ( !$result['isUsernameEmpty'] && !$result['isPasswordEmpty'] ) {
            $user = $this->verifyAccountUsingUsernameAndPassword($username, $password);

            if ( $user != null ) {
                $result['isAccountValid']   = true;
                $result['isSuccessful']     = true;
                $this->createSession($user, $allowAutoLogin);
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 验证账户有效性.
     * @param  String $username - 用户名
     * @param  String $password - 密码
     * @return 一个用户对象或空引用
     */
    private function verifyAccountUsingUsernameAndPassword($username, $password) {
        if ( empty($username) || empty($password) ) {
            return false;
        }

        $user           = null;
        if ( !$this->isEmailAddress($username) ) {
            $user = $this->userTable->getUserUsingUsername($username);
        } else {
            $user = $this->userTable->getUserUsingEmail($username);
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
    private function isEmailAddress($username) {
        return strpos($username, '@');
    }

    /**
     * 为已登录的用户创建Session.
     * @param  User    $user           - 一个用户对象
     * @param  boolean $allowAutoLogin - 是否允许自动登陆
     */
    private function createSession($user, $allowAutoLogin) {
        $session    = new Container('co_session');
        
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
    public function logoutAction() {
        $this->destroySession();
        return $this->redirect()->toRoute('accounts', [
            'controller'    => 'accounts',
            'action'        => 'login',
            'logout'        => 'true',
        ]);
    }

    /**
     * 处理用户的重置密码请求.
     * @return 一个包含页面所需信息的数组.
     */
    public function resetPasswordAction() {
        if ( $this->isEnableAutoLogin() ) {
            return $this->redirect()->toRoute('accounts', [
                'controller'    => 'accounts',
                'action'        => 'dashboard',
            ]);
        }

        $email                = $this->params()->fromQuery('email');
        $keycode              = $this->params()->fromQuery('keycode');
        $isConfidentialSetted = ($email != null && $keycode != null);
        $isConfidentialValid  = $this->isConfidentialValid($email, $keycode);

        return [
            'isConfidentialSetted'  => $isConfidentialSetted,
            'isConfidentialValid'   => $isConfidentialValid,
            'email'                 => $email,
            'keycode'               => $keycode,
        ];
    }

    /**
     * 检查密码重置凭据是否合法.
     * @param  String $email   - 用户的电子邮件地址
     * @param  String $keycode - 重置密码的凭据
     * @return 密码重置凭据是否合法
     */
    private function isConfidentialValid($email, $keycode) {
        $confidential = $this->emailValidationTable->verifyConfidential($email, $keycode);
        return ( $confidential != null );
    }

    /**
     * 处理用户重设密码的请求. 用于验证用户身份的合法性.
     * @return 一个包含验证结果的JSON数组
     */
    public function doConfirmConfidentialAction() {
        $username = $this->getRequest()->getPost('username');
        $email    = $this->getRequest()->getPost('email');
        $result   = [
            'isSuccessful'          => false,
            'isUsernameEmpty'       => empty($username),
            'isEmailEmpty'          => empty($email),
            'isConfidentialValid'   => false,
        ];

        if ( !$result['isUsernameEmpty'] && !$result['isEmailEmpty'] ) {
            if ( $this->verifyAccountUsingUsernameAndEmail($username, $email) ) {
                $result['isConfidentialValid']  = true;
                $result['isSuccessful']         = true;
                $this->sendResetPasswordEmail($username, $email);
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 通过验证账户有效性.
     * @param  String $username - 用户名
     * @param  String $email    - 电子邮件地址
     * @return 用户名和邮件地址的二元组是否有效
     */
    private function verifyAccountUsingUsernameAndEmail($username, $email) {
        if ( empty($username) || empty($email) ) {
            return false;
        }

        $user = $this->userTable->getUserUsingUsername($username);
        if ( $user == null ) {
            return false;
        } else if ( $user->email != $email ) {
            return false;
        }
        return true;
    }

    /**
     * 发送重设密码的电子邮件.
     * @param  String $username - 用户名
     * @param  String $email    - 电子邮件地址
     */
    private function sendResetPasswordEmail($username, $email) {
        $keycode    = $this->generateRandomString(32);
        $this->saveConfidential($email, $keycode);
        
        return $this->sendMail('重置您的账户密码', [
            'username'  => $username,
            'email'     => $email,
            'keycode'   => $keycode,
        ], 'reset', $email);
    }

    /**
     * 生成随机字符串.
     * @param  int $length - 随机字符串的长度
     * @return 一个指定长度的随机字符串
     */
    private function generateRandomString($length) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * 将密码重置凭据保存至数据库.
     * @param  String $email   - 用户的电子邮件地址
     * @param  String $keycode - 重置密码的凭据
     */
    private function saveConfidential($email, $keycode) {
        $confidential = $this->emailValidationTable->getConfidentialUsingEmail($email);

        if ( $confidential != null ) {
            $this->emailValidationTable->deleteConfidential($email);
        }
        $confidential   = [
            'email'     => $email,
            'keycode'   => $keycode,
        ];
        $this->emailValidationTable->createConfidential($confidential);
    }

    /**
     * 处理用户重置密码的请求.
     * @return 一个包含密码重置结果的JSON数组
     */
    public function doResetPasswordAction() {
        $email              = $this->getRequest()->getPost('email');
        $keycode            = $this->getRequest()->getPost('keycode');
        $newPassword        = $this->getRequest()->getPost('newPassword');
        $confirmPassword    = $this->getRequest()->getPost('confirmPassword');

        $result = [
            'isSuccessful'      => false,
            'isPasswordEmpty'   => empty($newPassword),
            'isPasswordLegal'   => strlen($newPassword) >= 6 && strlen($newPassword) <= 16,
            'isPasswordMatched' => $newPassword == $confirmPassword,
            'isKeyCodeValid'    => $this->isConfidentialValid($email, $keycode),
        ];
        $result['isSuccessful'] = !$result['isPasswordEmpty']   && $result['isPasswordLegal'] &&
                                   $result['isPasswordMatched'] && $result['isKeyCodeValid'];

        if ( $result['isSuccessful'] ) {
            $this->removeConfidential($email);
            $this->doResetPassword($email, $newPassword);
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 删除重置密码凭据.
     * 当用户重置密码后, 删除该凭据.
     * @param  String $email - 用户的电子邮件地址
     */
    private function removeConfidential($email) {
        $this->emailValidationTable->deleteConfidential($email);
    }

    /**
     * 处理用户重置密码的请求.
     * @param  String $email       - 用户的电子邮件地址
     * @param  String $newPassword - 新密码
     */
    private function doResetPassword($email, $newPassword) {
        $user    = $this->userTable->getUserUsingEmail($email);
        $arrUser = [
            'uid'       => $user->uid,
            'password'  => md5($newPassword),
        ];
        $this->userTable->updateUser($arrUser);
    }

    /**
     * 显示用户注册页面.
     * @return 一个包含页面所需信息的数组
     */
    public function registerAction() {
        if ( $this->isEnableAutoLogin() ) {
            return $this->redirect()->toRoute('accounts', [
                'controller'    => 'accounts',
                'action'        => 'dashboard'
            ]);
        }
        return new ViewModel();
    }

    /**
     * 处理用户的注册请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function doRegisterAction() {
        $username       = $this->getRequest()->getPost('username');
        $email          = $this->getRequest()->getPost('email');
        $password       = $this->getRequest()->getPost('password');
        $userGroupSlug  = $this->getRequest()->getPost('userGroup');
        $userGroupId    = $this->getUserGroupId($userGroupSlug);

        $user   = [
            'username'      => $username,
            'email'         => $email,
            'password'      => $password,
            'user_group_id' => $userGroupId,
        ];
        $result = $this->isProfileLegal($user);
        
        if ( $result['isSuccessful'] ) {
            $user['password']       = md5($user['password']);
            $result['isSuccessful'] = $this->userTable->createUser($user);
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取用户组的唯一标识符.
     * @param  String $userGroupSlug - 用户组的唯一英文缩写
     * @return 用户组的唯一标识符
     */
    private function getUserGroupId($userGroupSlug) {
        $userGroup = $this->userGroupTable->getUserGroupUsingSlug($userGroupSlug);
        // 管理员用户不允许被注册
        if ( $userGroup != null && $userGroupSlug != 'administrator' ) {
            return $userGroup->userGroupId;
        } 
        return 0;
    }

    /**
     * 检查用户提交的个人信息是否合法.
     * @param  Array  $profile - 用户个人信息.
     * @return 一个包含若干标志位的数组
     */
    private function isProfileLegal($profile) {
        $result = [
            'isSuccessful'      => false,
            'isUsernameEmpty'   => empty($profile['username']),
            'isUsernameLegal'   => preg_match('/^[A-Za-z][A-Za-z0-9_]{5,15}$/', ($profile['username'])),
            'isUsernameExists'  => $this->isUsernameExists($profile['username']),
            'isEmailEmpty'      => empty($profile['email']),
            'isEmailLegal'      => $this->isEmailLegal($profile['email']),
            'isEmailExists'     => $this->isEmailExists($profile['email']),
            'isPasswordEmpty'   => empty($profile['password']),
            'isPasswordLegal'   => strlen($profile['password']) >= 6 && strlen($profile['password']) <= 16,
            'isUserGroupLegal'  => $profile['user_group_id'] != 0,
        ];
        $result['isSuccessful'] = !$result['isUsernameEmpty'] && $result['isUsernameLegal'] && !$result['isUsernameExists'] &&
                                  !$result['isEmailEmpty']    && $result['isEmailLegal']    && !$result['isEmailExists'] &&
                                  !$result['isPasswordEmpty'] && $result['isPasswordLegal'] &&  $result['isUserGroupLegal'];
        return $result;
    }

    /**
     * 检查电子邮件地址是否合法.
     * @param  String  $email - 电子邮件地址
     * @return 电子邮件地址是否合法
     */
    private function isEmailLegal($email) {
        return strlen($email) <= 64 && preg_match('/^[A-Za-z0-9\._-]+@[A-Za-z0-9_-]+\.[A-Za-z0-9\._-]+$/', ($email));
    }

    /**
     * 检查用户名是否存在.
     * @param  String  $username - 用户名
     * @return 用户名是否存在
     */
    private function isUsernameExists($username) {
        $user = $this->userTable->getUserUsingUsername($username);
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
    private function isEmailExists($email) {
        $user = $this->userTable->getUserUsingEmail($email);
        if ( $user != null ) {
            return true;
        }
        return false;
    }

    /**
     * 显示个人中心页面.
     * @return 一个包含页面所需信息的数组.
     */
    public function dashboardAction() {
        if ( !$this->isAllowedToAccess() ) {
            return $this->redirect()->toRoute('accounts', [
                'controller'    => 'accounts',
                'action'        => 'login'
            ]);
        }
        
        $profile = $this->getProfile();
        if ( $profile['profile']['userGroupSlug'] == 'administrator' ) {
            return $this->redirect()->toRoute('administration');
        }
        return $profile;
    }

    /**
     * 获取用户的个人信息.
     * @return 一个包含用户个人信息的数组
     */
    private function getProfile() {
        $profile    = $this->getUserProfile();
        $uid        = $profile['uid'];
        $userGroup  = ucfirst($profile['userGroupSlug']);

        $function   = 'get'.$userGroup.'Profile';
        $extra      = $this->$function($uid);;

        $result     = [
            'isSuccessful'  => $extra != null,
            'profile'       => $profile,
            'extra'         => $extra,
        ];
        return $result;
    }

    /**
     * 获取所请求页面的内容.
     * @return 包含页面内容的HTML字符串
     */
    public function getPageContentAction() {
        $pageName = $this->params()->fromQuery('pageName');
        $profile  = $this->getUserProfile();
        $view     = new ViewModel([
            'profile'   => $profile,
        ]);
        $view->setTerminal(true);

        $template = "/application/accounts/dashboard/$pageName.phtml";
        $resolver = $this->getEvent()
                         ->getApplication()
                         ->getServiceManager()
                         ->get('Zend\View\Resolver\TemplatePathStack');
        
        if ( !$resolver->resolve($template) ) {
            return $this->notFoundAction();
        }
        $view->setTemplate($template);
        return $view;
    }

    /**
     * 获取用户的基本信息.
     * @return 一个包含用户基本信息的数组
     */
    private function getUserProfile() {
        $session    = new Container('co_session');
        return [
            'uid'           => $session->offsetGet('uid'),
            'username'      => $session->offsetGet('username'),
            'userGroupSlug' => $session->offsetGet('userGroupSlug'),
            'email'         => $session->offsetGet('email'),
        ];
    }

    /**
     * 获得用户的个人资料.
     * @return 一个包含用户个人资料的JSON数组
     */
    public function getProfileAction() {
        $result   = $this->getProfile();

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取个人用户的个人资料.
     * @param  int $uid - 用户唯一标识符
     * @return 一个Person对象, 包含个人用户的个人资料
     */
    private function getPersonProfile($uid) {
        $person = $this->personTable->getPersonUsingUid($uid);
        return $person;
    }

    /**
     * 获取全部的职位信息.
     * @return 一个包含工作职位信息的JSON数组
     */
    public function getPositionsAction() {
        $positions = $this->positionTable->getAllPositions();
        $result    = [
            'isSuccessful'  => $positions != null,
            'positions'     => $this->getResultSetArray($positions),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取讲师用户的个人资料.
     * @param  int $uid - 用户唯一标识符
     * @return 一个Teacher对象, 包含讲师用户的个人资料
     */
    private function getTeacherProfile($uid) {
        $teacher = $this->teacherTable->getTeacherUsingUid($uid);
        return $teacher;
    }

    /**
     * 获取某个讲师的授课领域.
     * @return 一个包含授课领域信息的JSON数组
     */
    public function getTeachingFieldsAction() {
        $profile        = $this->getUserProfile();
        $uid            = $profile['uid'];
        $teachingFields = $this->teachingFieldTable->getTeachingFieldsOfUsingUid($uid);
        $result         = [
            'isSuccessful'      => $teachingFields != null,
            'teachingFields'    => $this->getResultSetArray($teachingFields),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    public function getCourseTypesAction() {
        $courseTypes = $this->courseTypeTable->getAllCourseTypes();

        $result = [
            'isSuccessful'  => $courseTypes != null && $courseTypes->count() != 0,
            'courseTypes'   => $this->getResultSetArray($courseTypes),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取企业用户的个人资料.
     * @param  int $uid - 用户唯一标识符
     * @return 一个Company对象, 包含企业用户的个人资料
     */
    private function getCompanyProfile($uid) {
        $company = $this->companyTable->getCompanyUsingUid($uid);
        return $company;
    }

    /**
     * 获取所有公司领域的信息.
     * @return 一个包含公司领域信息的JSON数组
     */
    public function getCompanyFieldsAction() {
        $companyFields  = $this->companyFieldTable->getAllCompanyFields();
        $result         = [
            'isSuccessful'      => $companyFields != null,
            'companyFields'     => $this->getResultSetArray($companyFields),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取管理员的个人信息.
     * (Dummy Function, 仅供getProfile()函数调用).
     * @return 一个包含管理员个人信息的数组
     */
    private function getAdministratorProfile() {
        return [];
    }

    /**
     * 修改用户的密码.
     * @return 一个包含若干标志位的JSON数组
     */
    public function changePasswordAction() {
        $oldPassword        = $this->getRequest()->getPost('oldPassword');
        $newPassword        = $this->getRequest()->getPost('newPassword');
        $confirmPassword    = $this->getRequest()->getPost('confirmPassword');

        $result = [
            'isSuccessful'              => false,
            'isOldPasswordEmpty'        => empty($oldPassword),
            'isOldPasswordValid'        => false,
            'isNewPasswordEmpty'        => empty($newPassword),
            'isNewPasswordLegal'        => strlen($newPassword) >= 6 && strlen($newPassword) <= 16,
            'isConfirmPasswordMatched'  => $newPassword == $confirmPassword,
        ];
        $result['isSuccessful'] = !$result['isOldPasswordEmpty'] && !$result['isNewPasswordEmpty'] &&
                                   $result['isNewPasswordLegal'] &&  $result['isConfirmPasswordMatched'];
        
        if ( $result['isSuccessful'] ) {
            $profile        = $this->getUserProfile();
            $uid            = $profile['uid'];
            $user           = $this->userTable->getUserUsingUid($uid);

            if ( $user->password == md5($oldPassword) ) {
                $result['isOldPasswordValid']   = true;
                $userArray                      = [
                    'uid'                       => $uid,
                    'password'                  => md5($newPassword),
                ];
                $this->userTable->updateUser($userArray);
            } else {
                $result['isSuccessful'] = false;
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 编辑用户个人资料.
     * @return 一个包含若干标志位的JSON数组
     */
    public function editProfileAction() {
        $profile    = $this->getUserProfile();
        $uid        = $profile['uid'];
        $userGroup  = ucfirst($profile['userGroupSlug']);
        $function   = "edit{$userGroup}Profile";
        
        $result     = $this->editEmailAddress();
        $result    += $this->$function($uid);
        $response   = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 修改用户电子邮件地址.
     * @return 一个包含若干标志位的数组
     */
    private function editEmailAddress() {
        $profile         = $this->getUserProfile();
        $uid             = $profile['uid'];
        $oldEmailAddress = $profile['email'];
        $newEmailAddress = strip_tags($this->getRequest()->getPost('email'));

        $result          = [
            'isEmailEmpty'  => empty($newEmailAddress),
            'isEmailLegal'  => $this->isEmailLegal($newEmailAddress),
        ];
        $isSuccessful    = !$result['isEmailEmpty'] && $result['isEmailLegal'];

        if ( $isSuccessful && $oldEmailAddress != $newEmailAddress ) {
            $this->emailValidationTable->deleteConfidential($oldEmailAddress);
            $this->userTable->updateUser([
                'uid'                => $uid,
                'email'              => $newEmailAddress,
                'is_email_validated' => 0,
            ]);

            // Update Email in Session
            $session = new Container('co_session');
            $session->offsetSet('email', $newEmailAddress);
        }
        return $result;
    }

    /**
     * 编辑个人用户的个人资料.
     * @return 一个包含若干标志位的JSON数组
     */
    public function editPersonProfile() {
        $name           = strip_tags($this->getRequest()->getPost('name'));
        $region         = strip_tags($this->getRequest()->getPost('region'));
        $province       = strip_tags($this->getRequest()->getPost('province'));
        $city           = strip_tags($this->getRequest()->getPost('city'));
        $company        = strip_tags($this->getRequest()->getPost('company'));
        $positionSlug   = strip_tags($this->getRequest()->getPost('position'));
        $phone          = strip_tags($this->getRequest()->getPost('phone'));
        $positionId     = $this->getPositionId($positionSlug);

        $profile        = $this->getUserProfile();
        $uid            = $profile['uid'];
        $person         = [
            'uid'                   => $uid,
            'person_name'           => $name,
            'person_region'         => $region,
            'person_province'       => $province,
            'person_city'           => $city,
            'person_company'        => $company,
            'person_position_id'    => $positionId,
            'person_phone'          => $phone,
        ];
        
        $result = $this->isPersonProfileLegal($person);
        if ( $result['isSuccessful'] ) {
            $isPersonExists     = $this->personTable->getPersonUsingUid($uid);

            if ( $isPersonExists != null ) {
                $this->personTable->updatePerson($person);
            } else {
                $this->personTable->createPerson($person);
            }
        }
        return $result;
    }

    /**
     * 通过工作职位的唯一英文缩写获取工作职位的唯一标识符.
     * @param  String $positionSlug - 工作职位的唯一英文缩写
     * @return 工作职位的唯一标识符
     */
    private function getPositionId($positionSlug) {
        $position = $this->positionTable->getPositionUsingSlug($positionSlug);

        if ( $position != null ) {
            return $position->positionId;
        } 
        return 0;
    }

    /**
     * 检查个人用户所提交的信息是否合法.
     * @param  Array  $person - 一个包含个人用户信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function isPersonProfileLegal($person) {
        $result = [
            'isSuccessful'              => false,
            'isPersonNameEmpty'         => empty($person['person_name']),
            'isPersonNameLegal'         => mb_strlen($person['person_name'], 'utf-8') <= 16,
            'isPersonRegionEmpty'       => empty($person['person_region']),
            'isPersonProvinceEmpty'     => empty($person['person_province']),
            'isPersonCompanyEmpty'      => empty($person['person_company']),
            'isPersonCompanyLegal'      => mb_strlen($person['person_company'], 'utf-8') <= 64,
            'isPersonPositionLegal'     => $person['person_position_id'] != 0,
            'isPersonPhoneEmpty'        => empty($person['person_phone']),
            'isPersonPhoneLegal'        => preg_match('/^[0-9+-]{8,16}$/', $person['person_phone']),
        ];
        $result['isSuccessful'] = !$result['isPersonNameEmpty']     &&  $result['isPersonNameLegal'] &&
                                  !$result['isPersonRegionEmpty']   && !$result['isPersonProvinceEmpty'] &&
                                  !$result['isPersonCompanyEmpty']  &&  $result['isPersonCompanyLegal'] &&
                                   $result['isPersonPositionLegal'] && !$result['isPersonPhoneEmpty'] &&
                                   $result['isPersonPhoneLegal'];
        return $result;
    }

    /**
     * 编辑讲师用户个人资料.
     * @return 一个包含若干标志位的数组
     */
    private function editTeacherProfile() {
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
        $teacher        = [
            'uid'                   => $uid,
            'teacher_name'          => $name,
            'teacher_company'       => $company,
            'teacher_region'        => $region,
            'teacher_province'      => $province,
            'teacher_city'          => $city,
            'teacher_phone'         => $phone,
            'teacher_weibo'         => $weibo,
            'teacher_brief'         => $brief,
        ];
        
        $result = $this->isTeacherProfileLegal($teacher);
        if ( $result['isSuccessful'] ) {
            $isTeacherExists    = $this->teacherTable->getTeacherUsingUid($uid);
            $courseTypes        = $this->getCourseTypes($this->courseTypeTable->getAllCourseTypes());

            if ( $isTeacherExists != null ) {
                $this->teacherTable->updateTeacher($teacher);
            } else {
                $this->teacherTable->createTeacher($teacher);
            }
            $this->teachingFieldTable->updateTeachingField($uid, $teachingFields, $courseTypes);
            $this->userTable->pendingUser($uid);
        }
        return $result;
    }

    /**
     * 检查讲师用户所提交的信息是否合法.
     * @param  Array  $teacher - 一个包含讲师信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function isTeacherProfileLegal($teacher) {
        $result = [
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
        ];
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
    private function getCourseTypes($courseTypes) {
        $hashMap = [];
        foreach ( $courseTypes as $courseType ) {
            $hashMap[$courseType->courseTypeSlug] = $courseType;
        }
        return $hashMap;
    }

    /**
     * 编辑企业用户个人资料.
     * @return 一个包含若干标志位的数组
     */
    private function editCompanyProfile() {
        $name               = strip_tags($this->getRequest()->getPost('name'));
        $region             = strip_tags($this->getRequest()->getPost('region'));
        $province           = strip_tags($this->getRequest()->getPost('province'));
        $city               = strip_tags($this->getRequest()->getPost('city'));
        $address            = strip_tags($this->getRequest()->getPost('address'));
        $companyFieldSlug   = strip_tags($this->getRequest()->getPost('companyField'));
        $scale              = strip_tags($this->getRequest()->getPost('scale'));
        $phone              = strip_tags($this->getRequest()->getPost('phone'));
        $companyFieldId     = $this->getCompanyFieldId($companyFieldSlug);

        $profile            = $this->getUserProfile();
        $uid                = $profile['uid'];
        $company            = [
            'uid'                   => $uid,
            'company_name'          => $name,
            'company_region'        => $region,
            'company_province'      => $province,
            'company_city'          => $city,
            'company_address'       => $address,
            'company_field_id'      => $companyFieldId,
            'company_scale'         => $scale,
            'company_phone'         => $phone,
        ];
        
        $result = $this->isCompanyProfileLegal($company);
        if ( $result['isSuccessful'] ) {
            $isCompanyExists = $this->companyTable->getCompanyUsingUid($uid);

            if ( $isCompanyExists != null ) {
                $this->companyTable->updateCompany($company);
            } else {
                $this->companyTable->createCompany($company);
            }
        }
        return $result;
    }

    /**
     * 检查企业用户所提交的信息是否合法.
     * @param  Array $company - 一个包含企业信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function isCompanyProfileLegal($company) {
        $result = [
            'isSuccessful'              => false,
            'isCompanyNameEmpty'        => empty($company['company_name']),
            'isCompanyNameLegal'        => mb_strlen($company['company_name'], 'utf-8') <= 64,
            'isCompanyRegionEmpty'      => empty($company['company_region']),
            'isCompanyProvinceEmpty'    => empty($company['company_province']),
            'isCompanyAddressEmpty'     => empty($company['company_address']),
            'isCompanyAddressLegal'     => mb_strlen($company['company_address'], 'utf-8') <= 128,
            'isCompanyFieldLegal'       => $company['company_field_id'] != 0,
            'isCompanyScaleLegal'       => in_array($company['company_scale'], [1, 10, 100, 1000, 10000]),
            'isCompanyPhoneEmpty'       => empty($company['company_phone']),
            'isCompanyPhoneLegal'       => preg_match('/^[0-9+-]{8,16}$/', $company['company_phone']),
        ];
        $result['isSuccessful'] = !$result['isCompanyNameEmpty']    &&  $result['isCompanyNameLegal'] &&
                                  !$result['isCompanyRegionEmpty']  && !$result['isCompanyProvinceEmpty'] &&
                                  !$result['isCompanyAddressEmpty'] &&  $result['isCompanyAddressLegal'] &&  
                                   $result['isCompanyFieldLegal']   &&  $result['isCompanyScaleLegal'] &&
                                  !$result['isCompanyPhoneEmpty']   &&  $result['isCompanyPhoneLegal'];
        return $result;
    }

    /**
     * 通过公司领域的唯一英文缩写获取获取公司领域的唯一标识符.
     * @param  String $companyFieldSlug - 公司领域的唯一英文缩写
     * @return 公司领域的唯一标识符
     */
    private function getCompanyFieldId($companyFieldSlug) {
        $companyField = $this->companyFieldTable->getCompanyFieldUsingSlug($companyFieldSlug);
        if ( $companyField != null ) {
            return $companyField->companyFieldId;
        } 
        return 0;
    }

    /**
     * 获取某个用户参加培训的信息.
     * @return 一个包含用户参加培训信息的JSON数组
     */
    public function getLectureAttendanceAction() {
        $profile                    = $this->getUserProfile();
        $NUMBER_OF_RECORDS_PER_PAGE = 10;
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_RECORDS_PER_PAGE;

        $attendanceRecords          = $this->lectureAttendanceTable->getLectureAttendanceUsingUid(
                                        $profile['uid'], $offset, $NUMBER_OF_RECORDS_PER_PAGE);
        $totalPages                 = ceil($this->lectureAttendanceTable->getCountUsingUid($profile['uid']) 
                                        / $NUMBER_OF_RECORDS_PER_PAGE);
        $result = [
            'isSuccessful'  => $attendanceRecords != null && $attendanceRecords->count() != 0,
            'records'       => $this->getResultSetArray($attendanceRecords),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 处理用户评价课程的请求.
     * @return 包含若干标志位的JSON数组
     */
    public function createCommentAction() {
        $profile        = $this->getUserProfile();
        $lectureId      = $this->getRequest()->getPost('lectureId');
        $commentRanking = $this->getRequest()->getPost('commentRanking');
        $commentDetail  = strip_tags($this->getRequest()->getPost('commentDetail'));

        $comment        = [
            'lecture_id'        => $lectureId,
            'reviewer_uid'      => $profile['uid'],
            'comment_ranking'   => $commentRanking,
            'comment_detail'    => $commentDetail,
        ];

        $isSuccessful   = $this->isCommentLegal($comment);
        if ( $isSuccessful ) {
            $isSuccessful   = $this->commentTable->createComment($comment);
        }
        $result = [
            'isSuccessful'  => $isSuccessful,
            'commentRanking'=> $commentRanking,
            'commentDetail' => $commentDetail,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 检查所提交评论信息的合法性.
     * @param  Array  $comment - 一个包含评论信息的数组
     * @return 提交评论信息的合法性
     */
    private function isCommentLegal($comment) {
        $attendance = $this->lectureAttendanceTable->getLectureAttendanceUsingUidAndLectureId(
                        $comment['reviewer_uid'], $comment['lecture_id']);
        $result     = [
            'isSuccessful'      => false,
            'isAttended'        => $attendance != null,
            'hasCommented'      => $attendance != null && $attendance->commentDetail != null,
            'isRankingLegal'    => $comment['comment_ranking'] > 0 && $comment['comment_ranking'] <= 5,
            'isDetailEmpty'     => empty($comment['comment_detail']),
        ];
        $result['isSuccessful'] = $result['isAttended'] && $result['isRankingLegal'] && 
                                 !$result['hasCommented'] && !$result['isDetailEmpty'];
        return $result['isSuccessful'];
    }

    /**
     * 获取某个讲师用户所开设课程的信息.
     * @return 一个包含讲师用户所开设课程的信息的JSON数组
     */
    public function getOpeningCoursesAction() {
        $profile                    = $this->getUserProfile();
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $courses    = $this->courseTable->getCoursesUsingTeacherId(
                        $profile['uid'], $offset, $NUMBER_OF_COURSES_PER_PAGE);
        $totalPages = ceil($this->courseTable->getCountUsingTeacherId($profile['uid']) 
                        / $NUMBER_OF_COURSES_PER_PAGE);
        $result     = [
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 通过课程类型的唯一英文缩写查找课程类型的唯一标识符.
     * @param  String $catelogySlug - 课程类型的唯一英文缩写
     * @return 课程类型的唯一标识符
     */
    private function getCourseTypeId($catelogySlug) {
        $courseType = $this->courseTypeTable->getCatelogyUsingSlug($catelogySlug);
        if ( $courseType != null ) {
            return $courseType->courseTypeId;
        } 
        return 0;
    }

    /**
     * 获取某个讲师用户所开设培训课的信息.
     * @return 一个包含讲师用户所开设培训课的信息的JSON数组
     */
    public function getOpeningLecturesAction() {
        $profile                        = $this->getUserProfile();
        $NUMBER_OF_LECTURES_PER_PAGE    = 10;
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_LECTURES_PER_PAGE;

        $lectures   = $this->lectureTable->getLecturesUsingTeacherId(
                        $profile['uid'], $offset, $NUMBER_OF_LECTURES_PER_PAGE);
        $totalPages = ceil($this->lectureTable->getCountUsingTeacherId($profile['uid']) 
                        / $NUMBER_OF_LECTURES_PER_PAGE);
        $result     = [
            'isSuccessful'  => $lectures != null && $lectures->count() != 0,
            'lectures'      => $this->getResultSetArray($lectures),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 处理讲师用户开设课程的请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createLectureAction() {
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
        $lecture                = [
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
        ];
        $result = $this->isLectureLegal($lecture);

        if ( $result['isSuccessful'] ) {
            $lectureId = $this->lectureTable->createLecture($lecture);
            $result   += [
                'lectureId'         => $lectureId,
            ];
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 检查所提交的培训课信息是否合法.
     * @param  Array  $lecture - 一个包含培训课信息的数组
     * @return 培训课信息是否合法
     */
    private function isLectureLegal($lecture) {
        $result = [
            'isSuccessful'          => false,
            'isCourseIdLegal'       => $this->isCourseOwner($lecture['course_id']),
            'isStartTimeEmpty'      => empty($lecture['lecture_start_time']),
            'isStartTimeLegal'      => strtotime($lecture['lecture_start_time']) > strtotime('now'),
            'isEndTimeEmpty'        => empty($lecture['lecture_end_time']),
            'isEndTimeLegal'        => strtotime($lecture['lecture_end_time']) > strtotime($lecture['lecture_start_time']),
            'isRegionEmpty'         => empty($lecture['lecture_region']),
            'isProvinceEmpty'       => empty($lecture['lecture_province']),
            'isAddressEmpty'        => empty($lecture['lecture_address']),
            'isMinCapcityEmpty'     => empty($lecture['lecture_min_capcity']),
            'isMinCapcityLegal'     => intval($lecture['lecture_min_capcity']) && $lecture['lecture_min_capcity'] > 0,
            'isMaxCapcityEmpty'     => empty($lecture['lecture_max_capcity']),
            'isMaxCapcityLegal'     => intval($lecture['lecture_max_capcity']) && $lecture['lecture_max_capcity'] >= $lecture['lecture_min_capcity'],
            'isExpenseEmpty'        => empty($lecture['lecture_expense']),
            'isExpenseLegal'        => intval($lecture['lecture_expense']) && $lecture['lecture_expense'] > 0,
            'isPrecautionsEmpty'    => empty($lecture['lecture_precautions']),
        ];
        $result['isSuccessful'] = $result['isCourseIdLegal']   && !$result['isStartTimeEmpty'] &&
                                  $result['isStartTimeLegal']  && !$result['isEndTimeEmpty'] &&
                                  $result['isEndTimeLegal']    && !$result['isRegionEmpty'] &&
                                 !$result['isProvinceEmpty']   && !$result['isAddressEmpty'] && 
                                 !$result['isMinCapcityEmpty'] &&  $result['isMinCapcityLegal'] && 
                                 !$result['isMaxCapcityEmpty'] &&  $result['isMaxCapcityLegal'] && 
                                 !$result['isExpenseEmpty']    &&  $result['isExpenseLegal'] && 
                                 !$result['isPrecautionsEmpty'];
        return $result;
    }

    /**
     * 检查讲师用户是否有编辑该课程的权限.
     * @param  int  $courseId - 课程的唯一标识符
     * @return 讲师用户是否有编辑该课程的权限
     */
    private function isCourseOwner($courseId) {
        $course     = $this->courseTable->getCourseUsingCourseId($courseId);
        $profile    = $this->getUserProfile();
        $teacherId  = $profile['uid'];

        return ($course != null && $course->teacherId == $teacherId);
    }

    /**
     * 处理讲师用户的创建课程计划请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createLectureScheduleAction() {
        $lectureId              = $this->getRequest()->getPost('lectureId');
        $courseModuleId         = $this->getRequest()->getPost('courseModuleId');
        $startTime              = $this->getRequest()->getPost('startTime');
        $endTime                = $this->getRequest()->getPost('endTime');
        $lectureSchedule        = [
            'lecture_id'                => $lectureId,
            'course_module_id'          => $courseModuleId,
            'course_module_start_time'  => $startTime,
            'course_module_end_time'    => $endTime,
        ];

        $result = [
            'isSuccessful'  => $this->lectureScheduleTable->createLectureSchedule($lectureSchedule),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取某个课程的信息.
     * @return 一个包含课程信息的JSON数组
     */
    public function getCourseAction() {
        $courseId   = $this->params()->fromQuery('courseId');
        $course     = null;

        if ( $courseId != null ) {
            $profile    = $this->getUserProfile();
            $teacherId  = $profile['uid'];
            $course     = $this->courseTable->getCourseUsingCourseId($courseId);

            if ( $course == null || $course->teacherId != $teacherId ) {
                $course = null;
            }
        }
        $result = [
            'isSuccessful'  => $course != null,
            'course'        => $course,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 处理讲师用户创建课程的请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createCourseAction() {
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
        $course                 = [
            'course_name'       => $courseName,
            'course_type_id'    => $courseTypeId,
            'teacher_id'        => $teacherId,
            'course_cycle'      => $courseCycle,
            'course_audience'   => $courseAudience,
            'course_brief'      => $courseBrief,
            'course_objective'  => $courseObjective,
        ];
        $result = $this->isCourseLegal($course);
        
        if ( $result['isSuccessful'] ) {
            $courseId               = $this->courseTable->createCourse($course);
            $result['isSuccessful'] = $this->courseCompositionTable->updateCourseComposition(
                                        $courseId, $courseModules);
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 处理讲师用户更新课程信息的请求.
     * @return 一个包含若干标识位的JSON数组
     */
    public function updateCourseAction() {
        $courseId               = $this->getRequest()->getPost('courseId');
        $courseTypeSlug         = strip_tags($this->getRequest()->getPost('courseTypeSlug'));
        $courseCycle            = strip_tags($this->getRequest()->getPost('courseCycle'));
        $courseAudience         = strip_tags($this->getRequest()->getPost('courseAudience'));
        $courseBrief            = strip_tags($this->getRequest()->getPost('courseBrief'));
        $courseObjective        = strip_tags($this->getRequest()->getPost('courseObjective'));
        $courseModules          = strip_tags($this->getRequest()->getPost('courseModules'));
        $courseTypeId           = $this->getCourseTypeId($courseTypeSlug);
        
        $course                 = $this->courseTable->getCourseUsingCourseId($courseId);
        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];
        
        if ( $course == null || $course->teacherId != $teacherId ) {
            $result = [
                'isSuccessful'  => false,
            ];
        } else {
            $course                 = [
                'course_id'         => $courseId,
                'course_name'       => $course->courseName,
                'course_type_id'    => $courseTypeId,
                'teacher_id'        => $teacherId,
                'course_cycle'      => $courseCycle,
                'course_audience'   => $courseAudience,
                'course_brief'      => $courseBrief,
                'course_objective'  => $courseObjective,
            ];
            $result = $this->isCourseLegal($course);
            
            if ( $result['isSuccessful'] ) {
                $result['isSuccessful'] = $this->courseTable->updateCourse($course);
                $result['isSuccessful'] = $this->courseCompositionTable->updateCourseComposition(
                                            $courseId, $courseModules);
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 检查所提交的课程信息是否合法.
     * @param  Array  $course - 一个包含课程信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function isCourseLegal($course) {
        $result = [
            'isCourseNameEmpty'     => empty($course['course_name']),
            'isCourseNameLegal'     => mb_strlen($course['course_name'], 'utf-8') <= 128,
            'isCourseTypeIdLegal'   => $course['course_type_id'] != 0,
            'isCourseCycleEmpty'    => empty($course['course_cycle']),
            'isCourseCycleLegal'    => is_numeric($course['course_cycle']) && $course['course_cycle'] > 0,
            'isCourseAudienceEmpty' => empty($course['course_audience']),
            'isCourseAudienceLegal' => mb_strlen($course['course_audience'], 'utf-8') <= 256,
            'isCourseBriefEmpty'    => empty($course['course_brief']),
            'isCourseObjectiveEmpty'=> empty($course['course_objective']),
        ];
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
    public function getCourseModuleAction() {
        $courseModuleId     = $this->params()->fromQuery('courseModuleId');
        $courseModule       = null;

        if ( $courseModuleId != null ) {
            $profile        = $this->getUserProfile();
            $teacherId      = $profile['uid'];
            $courseModule   = $this->courseModuleTable->getCoureModuleUsingCourseModuleId($courseModuleId);

            if ( $courseModule == null || $courseModule->teacherId != $teacherId ) {
                $courseModule = null;
            }
        }

        $result = [
            'isSuccessful'  => $courseModule != null,
            'courseModule'  => $courseModule,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取某个课程所包含/某个讲师开设的课程模块.
     * @return 一个包含课程模块信息的JSON数组
     */
    public function getCourseModulesAction() {
        $courseId       = $this->params()->fromQuery('courseId');
        $courseModules  = null;
        
        if ( $courseId == null ) {
            $profile        = $this->getUserProfile();
            $teacherId      = $profile['uid'];
            $courseModules  = $this->courseModuleTable->getCourseModulesUsingTeacherId($teacherId);
        } else {
            $courseModules  = $this->courseModuleTable->getCourseModulesUsingCourseId($courseId);
        }
        $result = [
            'isSuccessful'  => $courseModules != null && $courseModules->count() != 0,
            'courseModules' => $this->getResultSetArray($courseModules),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 处理讲师用户创建课程模块的请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createCourseModuleAction() {
        $courseModuleName       = strip_tags($this->getRequest()->getPost('courseModuleName'));
        $courseModuleCycle      = strip_tags($this->getRequest()->getPost('courseModuleCycle'));
        $courseModuleBrief      = strip_tags($this->getRequest()->getPost('courseModuleBrief'));
        $courseModuleOutline    = strip_tags($this->getRequest()->getPost('courseModuleOutline'));
        
        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];
        $courseModule           = [
            'course_module_name'    => $courseModuleName,
            'course_module_cycle'   => $courseModuleCycle,
            'teacher_id'            => $teacherId,
            'course_module_brief'   => $courseModuleBrief,
            'course_module_outline' => $courseModuleOutline,
        ];
        $result = $this->isCourseModuleLegal($courseModule);
        
        if ( $result['isSuccessful'] ) {
            $courseModuleId     = $this->courseModuleTable->createCourseModule($courseModule);
            $result            += [
                'courseModuleId'    => $courseModuleId,
            ];
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 修改课程模块的信息.
     * @return 一个包含若干标志位的JSON数组
     */
    public function updateCourseModuleAction() {
        $courseModuleId         = $this->getRequest()->getPost('courseModuleId');
        $courseModuleCycle      = $this->getRequest()->getPost('courseModuleCycle');
        $courseModuleBrief      = strip_tags($this->getRequest()->getPost('courseModuleBrief'));
        $courseModuleOutline    = strip_tags($this->getRequest()->getPost('courseModuleOutline'));

        $courseModule           = $this->courseModuleTable->getCoureModuleUsingCourseModuleId($courseModuleId);
        $profile                = $this->getUserProfile();
        $teacherId              = $profile['uid'];

        if ( $courseModule == null || $courseModule->teacherId != $teacherId ) {
            $result = array(
                'isSuccessful'  => false,
            );
        } else {
            $courseModule = [
                'course_module_id'      => $courseModuleId,
                'course_module_name'    => $courseModule->courseModuleName,
                'course_module_cycle'   => $courseModuleCycle,
                'teacher_id'            => $teacherId,
                'course_module_brief'   => $courseModuleBrief,
                'course_module_outline' => $courseModuleOutline,
            ];
            $result = $this->isCourseModuleLegal($courseModule);
            
            if ( $result['isSuccessful'] ) {
                $result['isSuccessful'] = $this->courseModuleTable->updateCourseModule($courseModule);
            }
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 检查所提交的课程模块信息是否合法.
     * @param  Array  $courseModule - 一个包含课程模块信息的数组
     * @return 一个包含若干标志位的数组
     */
    public function isCourseModuleLegal($courseModule) {
        $result = [
            'isSuccessful'                  => false,
            'isCourseModuleNameEmpty'       => empty($courseModule['course_module_name']),
            'isCourseModuleLegal'           => mb_strlen($courseModule['course_module_name'], 'utf-8') <= 128,
            'isCourseModuleCycleEmpty'      => empty($courseModule['course_module_cycle']),
            'isCourseModuleCycleLegal'      => is_numeric($courseModule['course_module_cycle']) && $courseModule['course_module_cycle'] > 0,
            'isCourseModuleBriefEmpty'      => empty($courseModule['course_module_brief']),
            'isCourseModuleOutlineEmpty'    => empty($courseModule['course_module_outline']),
        ];
        $result['isSuccessful'] = !$result['isCourseModuleNameEmpty']  &&  $result['isCourseModuleLegal'] &&
                                  !$result['isCourseModuleCycleEmpty'] &&  $result['isCourseModuleCycleLegal'] &&
                                  !$result['isCourseModuleBriefEmpty'] && !$result['isCourseModuleOutlineEmpty'];
        return $result;
    }

    /**
     * 加载用户的需求列表.
     * @return 一个包含用户需求列表的JSON数组
     */
    public function getRequirementsAction() {
        $NUMBER_OF_REQUIREMENT_PER_PAGE = 10;
        $profile                        = $this->getUserProfile();
        $uid                            = $profile['uid'];
        $userGroupSlug                  = $profile['userGroupSlug'];
        $isTeacher                      = ($userGroupSlug == 'teacher');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_REQUIREMENT_PER_PAGE;

        $requirements                   = $this->requirementTable->getRequirementUsingUid($uid, $isTeacher, $offset, $NUMBER_OF_REQUIREMENT_PER_PAGE);
        $result                         = [
            'isSuccessful'              => $requirements != null && $requirements->count() != 0,
            'requirements'              => $this->getResultSetArray($requirements),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 通过培训需求的唯一标识符获取培训需求对象.
     * @return 一个包含培训需求对象信息的JSON数组
     */
    public function getRequirementAction() {
        $requirementId  = $this->params()->fromQuery('requirementId');
        $requirement    = $this->requirementTable->getRequirementUsingRequirementId($requirementId);
        $profile        = $this->getUserProfile();
        $uid            = $profile['uid'];
        $userGroupSlug  = $profile['userGroupSlug'];

        if ( $userGroupSlug == 'teacher' && $requirement->toUid != $uid ) {
            $requirement    = null;
        } else if ( $userGroupSlug != 'teacher' && $requirement->fromUid != $uid ) {
            $requirement    = null;
        }

        $result   = array(
            'isSuccessful'  => $requirement != null,
            'requirement'   => $requirement,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 处理用户创建需求的请求.
     * @return 一个包含若干标志位的JSON数组
     */
    public function createRequirementAction() {
        $teacherId      = strip_tags($this->getRequest()->getPost('teacherId'));
        $courseId       = strip_tags($this->getRequest()->getPost('courseId'));
        $participants   = strip_tags($this->getRequest()->getPost('participants'));
        $startTime      = strip_tags($this->getRequest()->getPost('startTime'));
        $endTime        = strip_tags($this->getRequest()->getPost('endTime'));
        $region         = strip_tags($this->getRequest()->getPost('region'));
        $province       = strip_tags($this->getRequest()->getPost('province'));
        $city           = strip_tags($this->getRequest()->getPost('city'));
        $address        = strip_tags($this->getRequest()->getPost('address'));
        $detail         = strip_tags($this->getRequest()->getPost('detail'));

        $profile        = $this->getUserProfile();
        $uid            = $profile['uid'];
        $userGroupSlug  = $profile['userGroupSlug'];
        $requirement    = [
            'requirement_from_uid'      => $uid,
            'requirement_to_uid'        => ($teacherId == null ? null : $teacherId),
            'requirement_course_id'     => ($courseId == null ? null : $courseId),
            'requirement_participants'  => $participants,
            'requirement_start_time'    => $startTime,
            'requirement_end_time'      => $endTime,
            'requirement_region'        => $region,
            'requirement_province'      => $province,
            'requirement_city'          => $city,
            'requirement_address'       => $address,
            'requirement_detail'        => $detail,
        ];
        
        $result = $this->isRequirementLegal($requirement, $userGroupSlug);
        if ( $result['isSuccessful'] ) {
            $requirementId      = $this->requirementTable->createRequirement($requirement);
            $result            += array(
                'requirementId' => $requirementId,
            );         
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 检查用户所提交的需求信息是否合法.
     * @param  Array  $requirement - 一个包含用户需求信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function isRequirementLegal($requirement, $userGroupSlug) {
        $result                     = [
            'isSuccessful'          => false,
            'isTeacherIdEmpty'      => empty($requirement['requirement_to_uid']),
            'isTeacherExists'       => $this->isTeacherExists($requirement['requirement_to_uid']),
            'isCourseIdEmpty'       => empty($requirement['requirement_course_id']),
            'isCourseExists'        => $this->isCourseExists($requirement['requirement_course_id'], $requirement['requirement_to_uid']),
            'isStartTimeEmpty'      => empty($requirement['requirement_start_time']),
            'isStartTimeLegal'      => strtotime($requirement['requirement_start_time']) > strtotime('now'),
            'isEndTimeEmpty'        => empty($requirement['requirement_end_time']),
            'isEndTimeLegal'        => strtotime($requirement['requirement_end_time']) > strtotime($requirement['requirement_start_time']),
            'isParticipantsEmpty'   => empty($requirement['requirement_participants']),
            'isParticipantsLegal'   => intval($requirement['requirement_participants']) && $requirement['requirement_participants'] > 0,
            'isRegionEmpty'         => empty($requirement['requirement_region']),
            'isProvinceEmpty'       => empty($requirement['requirement_province']),
            'isAddressEmpty'        => empty($requirement['requirement_address']),
            'isAddressLegal'        => mb_strlen($requirement['requirement_address'], 'utf-8') <= 128,
            'isDetailEmpty'         => empty($requirement['requirement_detail']),
        ];

        if ( $userGroupSlug == 'company' ) {
            $result['isSuccessful'] =  $result['isTeacherExists']     &&  $result['isCourseExists'] &&
                                      !$result['isStartTimeEmpty']    &&  $result['isStartTimeLegal'] &&
                                      !$result['isParticipantsEmpty'] &&  $result['isParticipantsLegal'] &&
                                      !$result['isEndTimeEmpty']      &&  $result['isEndTimeLegal'] &&
                                      !$result['isRegionEmpty']       && !$result['isProvinceEmpty'] &&
                                      !$result['isAddressEmpty']      &&  $result['isAddressLegal'] &&
                                      !$result['isDetailEmpty'];
        } else {
            $result['isSuccessful'] = !$result['isTeacherIdEmpty']  &&  $result['isTeacherExists'] &&
                                      !$result['isCourseIdEmpty']   &&  $result['isCourseExists'] &&
                                      !$result['isStartTimeEmpty']  &&  $result['isStartTimeLegal'] &&
                                      !$result['isEndTimeEmpty']    &&  $result['isEndTimeLegal'] &&
                                      !$result['isRegionEmpty']     && !$result['isProvinceEmpty'];
        }
        return $result;
    }

    /**
     * 检查用户所提交的讲师是否存在.
     * @param  int  $teacherId - 讲师的用户唯一标识符
     * @return 用户所提交的讲师是否存在
     */
    private function isTeacherExists($teacherId) {
        if ( $teacherId == null ) {
            return true;
        }

        $teacher = $this->teacherTable->getTeacherUsingUid($teacherId);
        return ($teacher != null);
    }

    /**
     * 检查用户所提交的课程唯一标识符是否存在以及与讲师信息是否匹配.
     * @param  int  $courseId  - 课程的唯一标识符
     * @param  int  $teacherId - 讲师的用户唯一标识符
     * @return 用户所提交的课程唯一标识符是否存在
     */
    private function isCourseExists($courseId, $teacherId) {
        if ( $courseId == null ) {
            return true;
        }

        $course = $this->courseTable->getCourseUsingCourseId($courseId);
        if ( $course != null && $course->teacherId == $teacherId ) {
            return true;
        }
        return false;
    }

    /**
     * The data access object of Comment.
     * @var CommentTable
     */
    
    private $commentTable;
    
    /**
     * The data access object of Company.
     * @var CompanyTable
     */
    private $companyTable;
    
    /**
     * The data access object of CompanyField.
     * @var CompanyFieldTable
     */
    private $companyFieldTable;
    
    /**
     * The data access object of Course.
     * @var CourseTable
     */
    private $courseTable;
    
    /**
     * The data access object of CourseComposition.
     * @var CourseCompositionTable
     */
    private $courseCompositionTable;
    
    /**
     * The data access object of CourseModule.
     * @var CourseModuleTable
     */
    private $courseModuleTable;
    
    /**
     * The data access object of CourseType.
     * @var CourseTypeTable
     */
    private $courseTypeTable;
    
    /**
     * The data access object of EmailValidation.
     * @var EmailValidationTable
     */
    private $emailValidationTable;
    
    /**
     * The data access object of Lecture.
     * @var LectureTable
     */
    private $lectureTable;
    
    /**
     * The data access object of LectureAttendance.
     * @var LectureAttendanceTable
     */
    private $lectureAttendanceTable;
    
    /**
     * The data access object of LectureSchedule.
     * @var LectureScheduleTable
     */
    private $lectureScheduleTable;
    
    /**
     * The data access object of Person.
     * @var PersonTable
     */
    private $personTable;
    
    /**
     * The data access object of Position.
     * @var PositionTable
     */
    private $positionTable;
    
    /**
     * The data access object of Requirement.
     * @var RequirementTable
     */
    private $requirementTable;
    
    /**
     * The data access object of Teacher.
     * @var TeacherTable
     */
    private $teacherTable;
    
    /**
     * The data access object of TeachingField.
     * @var TeachingFieldTable
     */
    private $teachingFieldTable;
    
    /**
     * The data access object of User.
     * @var UserTable
     */
    private $userTable;
    
    /**
     * The data access object of UserGroup.
     * @var UserGroupTable
     */
    private $userGroupTable;
}