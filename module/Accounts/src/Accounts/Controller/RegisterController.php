<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * 处理用户注册请求的控制器.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class RegisterController extends AbstractActionController
{
    /**
     * 该控制器的默认方法, 显示用户注册页面.
     * @return 一个包含了HTML内容的ViewModel对象
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * 处理用户的注册请求, 处理用户的基本注册信息请求.
     * @return 一个含有若干标志位的JSON数组
     */
    public function processAction()
    {
        $basicInfoArray             = $this->getBasicInfoArray();
        $result                     = $this->verifyBasicInfo($basicInfoArray);
        
        if ( $result['isSuccessful'] ) {
            $result['isSuccessful'] = $this->processBasicAction($basicInfoArray);
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 从用户的POST请求中提取用户的基本信息, 并以数组形式返回.
     * @return 一个包含了用户基本信息的数组
     */
    private function getBasicInfoArray()
    {
        $username               = $this->getRequest()->getPost('username');
        $email                  = $this->getRequest()->getPost('email');
        $password               = $this->getRequest()->getPost('password');
        $confirmPassword        = $this->getRequest()->getPost('password-again');
        $userGroupSlug          = $this->getRequest()->getPost('user-group');

        return array(
            'username'          => strip_tags($username),
            'email'             => strip_tags($email),
            'password'          => $password,
            'confirmPassword'   => strip_tags($confirmPassword),
            'userGroupSlug'     => ucfirst(strip_tags($userGroupSlug)),
            'userGroupId'       => strip_tags($this->getUserGroupId($userGroupSlug)),
        );
    }

    /**
     * 检查用户所填写的信息是否合法.
     * @param  Array $basicInfo - 包含用户基本信息的数组
     * @return 一个含有若干标志位的数组
     */
    private function verifyBasicInfo($basicInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isUsernameEmpty'           => empty($basicInfo['username']),
            'isUsernameLegal'           => $this->isUsernameLegal($basicInfo['username']),
            'isUsernameExists'          => $this->isUsernameExists($basicInfo['username']),
            'isEmailEmpty'              => empty($basicInfo['email']),
            'isEmailLegal'              => $this->isEmailLegal($basicInfo['email']),
            'isEmailExists'             => $this->isEmailExists($basicInfo['email']),
            'isPasswordEmpty'           => empty($basicInfo['password']),
            'isPasswordLegal'           => $this->isPasswordLegal($basicInfo['password']),
            'isConfirmPasswordEmpty'    => empty($basicInfo['confirmPassword']),
            'isConfirmPasswordMatched'  => ($basicInfo['password'] == $basicInfo['confirmPassword']),
            'isUserGroupLegal'          => ($basicInfo['userGroupId'] != 0),
        );
        $result['isSuccessful']         = !$result['isUsernameEmpty']        &&  $result['isUsernameLegal']          &&
                                          !$result['isUsernameExists']       && !$result['isEmailEmpty']             && 
                                           $result['isEmailLegal']           && !$result['isEmailExists']            &&
                                          !$result['isPasswordEmpty']        &&  $result['isPasswordLegal']          &&
                                          !$result['isConfirmPasswordEmpty'] &&  $result['isConfirmPasswordMatched'] &&
                                           $result['isUserGroupLegal'];
        return $result;
    }

    /**
     * 检查用户的用户名是否合法.
     * 规则: 用户的用户名必须以英文字母开头, 长度在6-16(包含边界)个字符之间.
     * 
     * @param  String  $username - 用户的用户名
     * @return 用户的用户名是否合法
     */
    private function isUsernameLegal($username)
    {
        return (bool)preg_match('/^[a-z][0-9a-z_]{5,15}$/i', $username);
    }

    /**
     * 检查用户的用户名是否已经被使用.
     * @param  String  $username - 用户的用户名
     * @return 用户的用户名是否已经被使用
     */
    private function isUsernameExists($username)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        return $userTable->isUsernameExists($username);
    }

    /**
     * 检查用户的电子邮件地址是否合法.
     * @param  String  $email - 用户的电子邮件地址
     * @return 用户的电子邮件地址是否合法
     */
    private function isEmailLegal($email)
    {
        return (bool)preg_match('/^[A-Z0-9._%-]{4,18}@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);
    }

    /**
     * 检查用户的电子邮件地址是否已经被使用.
     * @param  String  $email - 用户的电子邮件地址
     * @return 用户的电子邮件地址是否已经被使用
     */
    private function isEmailExists($email)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        return $userTable->isEmailExists($email);
    }

    /**
     * 检查用户的密码是否合法
     * 规则: 用户密码的长度必须在6-16(包含边界)个字符之间.
     * 
     * @param  String  $password - 用户的密码
     * @return 用户的密码是否合法
     */
    private function isPasswordLegal($password)
    {
        $MIN_LENGTH_OF_PASSWORD     = 6;
        $MAX_LENGTH_OF_PASSWORD     = 16;
        $length                     = strlen($password);
        return ( $length >= $MIN_LENGTH_OF_PASSWORD && 
                 $length <= $MAX_LENGTH_OF_PASSWORD );
    }

    /**
     * 通过用户组的唯一简写(slug)以获取用户组的唯一标识符.
     * @param  String $userGroupSlug - 用户组的唯一简写
     * @return 用户组的唯一标识符
     */
    private function getUserGroupId($userGroupSlug)
    {
        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupId($userGroupSlug);

        if ( $userGroup == null ) {
            return null;
        }
        return $userGroup->user_group_id;
    }

    /**
     * 通过用户组的唯一标识符以获取用户组的唯一简写(slug).
     * @param  int $userGroupId - 用户组的唯一标识符
     * @return 用户组的唯一简写(slug)
     */
    private function getUserGroupSlug($userGroupId)
    {
        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupSlug($userGroupId);

        if ( $userGroup == null ) {
            return null;
        }
        return ucfirst($userGroup->user_group_slug);
    }

    /**
     * 将用户的基本信息添加至用户表(users).
     * 在本函数中会对用户的密码进行加密存储.
     * @param  Array $basicInfoArray - 含有用户基本信息的数组
     * @return 用户的唯一标识符(uid)
     */
    private function processBasicAction($basicInfoArray)
    {
        $this->createSession($basicInfoArray);

        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        $basicInfo          = array(
            'username'      => $basicInfoArray['username'],
            'password'      => md5($basicInfoArray['password']),
            'email'         => $basicInfoArray['email'],
            'user_group_id' => $basicInfoArray['userGroupId'],
        );
        
        return $userTable->createNewUser($basicInfo);
    }

    /**
     * 为该用户创建Session. 该Session包含了一些用户必要的信息.
     * @param  Array $basicInfoArray - 包含了用户基本信息的数组
     */
    private function createSession($basicInfoArray)
    {
        $session    = new Container('itp_session');
        
        $session->offsetSet('isLogined', true);
        $session->offsetSet('username', $basicInfoArray['username']);
        $session->offsetSet('email', $basicInfoArray['email']);
        $session->offsetSet('isActivated', false);
        $session->offsetSet('userGroupSlug', 
                            $this->getUserGroupSlug($basicInfoArray['userGroupId']));
    }

    /**
     * 从Session中获取用户的信息, 以便用户继续完成注册.
     * @return 返回包含用户部分信息的数组
     */
    private function getSessionData()
    {
        $session    = new Container('itp_session');

        $sessionData = array(
            'uid'               => $session->offsetGet('uid'),
            'username'          => $session->offsetGet('username'),
            'email'             => $session->offsetGet('email'),
            'userGroupSlug'     => ucfirst($session->offsetGet('userGroupSlug')),
        );
        return $sessionData;
    }

    /**
     * HTTP重定向请求.
     * @param  String $redirectPath - 重定向的相对路径
     * @return HTTP重定向请求的对象.
     */
    private function sendRedirect($redirectPath = '')
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * 显示验证用户电子邮件地址的页面.
     * [拦截] 若用户还没有填写基本信息, 则跳转至注册页面.
     * [拦截] 若用户已经验证了电子邮件地址, 则跳转至完善个人资料页面.
     * 
     * @return 一个包含了HTML内容的ViewModel对象
     */
    public function emailAction()
    {
        if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/register');
        }
        if ( $this->isActivated() ) {
            return $this->sendRedirect('accounts/register/complete');
        }

        $this->verifyEmail();        
        return array(
            'email'     => $this->getEmailAddress(),
        );
    }

    /**
     * 验证用户电子邮件地址的准备工作:
     *     1. 产生验证码并保存至数据库
     *     2. 向用户的电子邮箱发送一封验证邮件
     */
    private function verifyEmail()
    {
        $email  = $this->getEmailAddress();
        $guid   = $this->getGUId();

        $this->saveToDatabase($email, $guid);
        $this->sendValidationEmail($email, $guid);
    }

    /**
     * 检查用户是否已经登陆, 是否尝试非法访问.
     * @return 用户是否已经登陆
     */
    private function isAllowedToAccess()
    {
        $session    = new Container('itp_session');
        return $session->offsetExists('isLogined');
    }

    /**
     * 从Session中获取用户的电子邮件地址.
     * @return 用户的电子邮件地址
     */
    private function getEmailAddress()
    {
        $session    = new Container('itp_session');
        return $session->offsetGet('email');
    }

    /**
     * 将验证信息(用户电子邮件地址 + 验证码)保存至数据库.
     * @param  String $email - 用户的电子邮件地址
     * @param  String $guid  - 用户激活邮箱的验证码
     */
    private function saveToDatabase($email, $guid)
    {
        $sm                 = $this->getServiceLocator();
        $validationTable    = $sm->get('Accounts\Model\EmailValidationTable');
        $record             = array(
            'email'         => $email,
            'guid'          => $guid,
        );

        return $validationTable->createRecord($record);
    }

    /**
     * 发送验证邮件至用户的邮箱.
     * @param  String $email - 用户的电子邮件地址
     * @param  String $guid  - 用户激活邮箱的验证码
     */
    private function sendValidationEmail($email, $guid)
    {
        $message = new Message();
        $message->addTo($email)
                ->addFrom('noreply@zjhzxhz.com', 'IT培训平台')
                ->setSubject('请完成在IT培训平台的注册')
                ->setBody($this->getMailContent($email, $guid));

        $transport = new SmtpTransport();
        $transport->send($message);
    }

    /**
     * 生成用户激活邮箱所使用的验证码.
     * @return 用户激活邮箱的验证码
     */
    private function getGUId()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf( '%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
                        mt_rand(0, 65535), mt_rand(0, 65535), 
                        mt_rand(0, 65535), mt_rand(16384, 20479), 
                        mt_rand(32768, 49151), mt_rand(0, 65535), 
                        mt_rand(0, 65535), mt_rand(0, 65535) );
    }

    /**
     * 生成用户激活邮箱的电子邮件的HTML内容.
     *
     * @todo  完善电子邮件的HTML内容
     * 
     * @param  String $email - 用户的电子邮件地址
     * @param  String $guid  - 用户激活邮箱的验证码
     * @return 电子邮件的HTML内容
     */
    private function getMailContent($email, $guid)
    {
        return "<html><body><a href=\"#email=$email&activation_code=$guid\">单击此处激活账户</a></body></html>";
    }

    /**
     * 处理用户的验证邮箱请求.
     * 邮件中包含的电子邮件地址和激活邮箱验证码通过GET方式发送.
     * 若激活成功, 则跳转至完善个人资料页面; 否则, 停留在本页面.
     * @return HTTP重定向请求的对象
     */
    public function activateAccountAction()
    {
        $email          = $this->getRequest()->getQuery('email');
        $guid           = $this->getRequest()->getQuery('activation_code');

        $isSuccessful   = $this->activateAccount($email, $guid);

        if ( $isSuccessful ) {
            return $this->sendRedirect('accounts/register/complete');
        } else {
            return $this->sendRedirect('accounts/register/email');
        }
    }

    /**
     * 处理用户的验证邮箱请求.
     * @param  String $email - 用户的电子邮件地址
     * @param  String $guid  - 用户激活邮箱的验证码
     * @return 返回该操作是否成功
     */
    private function activateAccount($email, $guid)
    {
        $sm                 = $this->getServiceLocator();
        $validationTable    = $sm->get('Accounts\Model\EmailValidationTable');
        $isSuccessful       = $validationTable->validateEmail($email, $guid);

        if ( $isSuccessful ) {
            $isActivated    = true;
            $this->updateAccountActivated($email, $isActivated);
        }

        return $isSuccessful;
    }

    /**
     * 在数据库表中和Session中更新账户的激活状态(是否验证了电子邮件地址)
     * @param  String $email - 用户的电子邮件地址
     * @param  bool $isActivated - 账户是否被激活
     */
    private function updateAccountActivated($email, $isActivated)
    {
        $this->updateSessionAccountActivated($isActivated);
        $this->updateDatabaseAccountActivated($email, $isActivated);
    }

    /**
     * 在Session中更新账户的激活状态(是否验证了电子邮件地址)
     * @param  bool $isActivated - 账户是否被激活
     */
    private function updateSessionAccountActivated($isActivated)
    {
        $session    = new Container('itp_session');
        $session->offsetSet('isActivated', true);
    }

    /**
     * 在用户表(users)中更新账户的激活状态(是否验证了电子邮件地址)
     * @param  String $email - 用户的电子邮件地址
     * @param  bool $isActivated - 账户是否被激活
     */
    private function updateDatabaseAccountActivated($email, $isActivated)
    {
        $sm         = $this->getServiceLocator();
        $userTable  = $sm->get('Accounts\Model\UserTable');

        return $userTable->updateAccountActivated($email, $isActivated);
    }

    /**
     * 显示完善个人信息页面. 该页面将提示不同类型的用户分别完善它们的
     * 个性化资料.
     * [拦截] 若用户尚未验证电子邮件地址, 则跳转至验证电子邮件地址页面.
     * [拦截] 若用户已经激活, 则跳转至用户控制面板(Dashboard).
     * 
     * @return 一个包含了HTML内容的ViewModel对象
     */
    public function completeAction()
    {
        if ( !$this->isActivated() ) {
            return $this->sendRedirect('accounts/register/email');
        }
        if ( $this->isProfileCompleted() ) {
            return $this->sendRedirect('accounts/dashboard');
        }

        $sessionData        = $this->getSessionData();
        return array(
            'username'      => $sessionData['username'],
            'email'         => $sessionData['email'],
            'userGroupSlug' => $sessionData['userGroupSlug'],
            'workPositions' => $this->getWorkPositions(),
            'courseTypes'   => $this->getCourseTypes(),
        );
    }

    /**
     * 检查用户是否已经验证了电子邮件地址.
     * @return 用户是否已经验证了电子邮件地址
     */
    private function isActivated()
    {
        $session    = new Container('itp_session');
        return $session->offsetGet('isActivated');
    }

    /**
     * 检查用户是否已经完善了个人资料.
     *
     * @param  int  $uid - 用户的唯一标识符
     * @param  int  $userGroupSlug - 用户所在用户组的唯一标识符
     * @return 用户是否已经完善了个人资料
     */
    private function isProfileCompleted()
    {
        $sessionData    = $this->getSessionData();
        $username       = $sessionData['username'];
        $uid            = $this->getUid($username);
        $userGroupSlug  = $sessionData['userGroupSlug'];

        $isProfileCompletedFunc = 'is'.$userGroupSlug.'ProfileCompleted';
        if ( $this->$isProfileCompletedFunc($uid) ) {
            return true;
        }
        return false;
    }

    /**
     * 通过用户的用户名以获得用户的唯一标识符(uid).
     * @param  String $username - 用户的用户名
     * @return 用户的唯一标识符
     */
    private function getUid($username)
    {
        $sm         = $this->getServiceLocator();
        $userTable  = $sm->get('Accounts\Model\UserTable');
        $user       = $userTable->getUidByUsername($username);

        return $user->uid;
    }

    /**
     * 检查一个个人用户是否已经完善了个人信息.
     * @param  int  $uid - 用户的唯一标识符
     * @return 一个个人用户是否已经完善了个人信息
     */
    private function isPersonProfileCompleted($uid)
    {
        $sm             = $this->getServiceLocator();
        $personTable    = $sm->get('Accounts\Model\PersonTable');
        return $personTable->getPersonInfo($uid);
    }

    /**
     * 检查一个教师用户是否已经完善了个人信息.
     * @param  int  $uid - 用户的唯一标识符
     * @return 一个教师用户是否已经完善了个人信息
     */
    private function isTeacherProfileCompleted($uid)
    {
        $sm             = $this->getServiceLocator();
        $TeacherTable   = $sm->get('Accounts\Model\TeacherTable');
        return $TeacherTable->getTeacherInfo($uid);
    }

    /**
     * 检查一个企业用户是否已经完善了个人信息.
     * @param  int  $uid - 用户的唯一标识符
     * @return 一个企业用户是否已经完善了个人信息
     */
    private function isCompanyProfileCompleted($uid)
    {
        $sm             = $this->getServiceLocator();
        $companyTable   = $sm->get('Accounts\Model\CompanyTable');
        return $companyTable->getCompanyInfo($uid);
    }

    /**
     * 从数据库表(work_positions)中获取获取个人用户所有可选择的工作职位.
     * @return 一个包含了所有工作职位信息的数组
     */
    private function getWorkPositions()
    {
        $sm                 = $this->getServiceLocator();
        $positionTable      = $sm->get('Accounts\Model\PositionTable');
        $positions          = $positionTable->fetchAll();

        return $this->getWorkPositionsArray($positions);
    }

    /**
     * 将TableGateway对象返回的ResultSet对象转换为数组对象, 并以数组形式返回.
     * @param  ResultSet $resultSet - 一个包含了所有工作职位信息的ResultSet对象
     * @return 一个包含了所有工作职位信息的数组
     */
    private function getWorkPositionsArray($resultSet)
    {
        $workPositionsArray = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $key => $value ) {
                $workPositionsArray[ $key ] = $value;
            }
        }
        return $workPositionsArray;
    }

    /**
     * 从数据库表(course_type)中获取所有可选择的课程类型(授课范围).
     * @return 一个包含了所有授课范围信息的数组
     */
    private function getCourseTypes()
    {
        $sm                 = $this->getServiceLocator();
        $courseTypeTable    = $sm->get('Solutions\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->fetchAll();

        return $this->getCourseTypesArray($courseTypes);
    }

    /**
     * 将TableGateway对象返回的ResultSet对象转换为数组对象, 并以数组形式返回.
     * @param  ResultSet $resultSet - 一个包含了所有授课范围信息的ResultSet对象
     * @return 一个包含了所有授课范围信息的数组
     */
    private function getCourseTypesArray($resultSet)
    {
        $courseTypesArray = array();

        if ( $resultSet != null ) {
            foreach ( $resultSet as $key => $value ) {
                $courseTypesArray[ $key ] = $value;
            }
        }
        return $courseTypesArray;
    }

    /**
     * 处理用户完善个人附加信息的请求.
     * @return 一个含有若干标志位的JSON数组
     */
    public function processCompleteProfileAction()
    {
        $sessionData        = $this->getSessionData();
        $uid                = $sessionData['uid'];
        $userGroupSlug      = $sessionData['userGroupSlug'];

        $getInfoArrayFunc   = 'get'.$userGroupSlug.'InfoArray';
        $verifyInfoFunc     = 'verify'.$userGroupSlug.'Info';
        $processActionFunc  = 'process'.$userGroupSlug.'Action';

        $infoArray          = $this->$getInfoArrayFunc();
        $verifyResult       = $this->$verifyInfoFunc($infoArray);
        
        if ( $verifyResult['isSuccessful'] ) {
            $verifyResult['isSuccessful'] = $this->$processActionFunc($uid, $infoArray);
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($verifyResult) );
        return $response;
    }

    /**
     * 为个人用户从HTTP请求中提取个人用户所需的用户信息.
     * @return 一个包含了个人用户的附加信息的数组
     */
    private function getPersonInfoArray()
    {
        $personName         = $this->getRequest()->getPost('real-name');
        $personRegion       = $this->getRequest()->getPost('region');
        $personProvince     = $this->getRequest()->getPost('province');
        $personCity         = $this->getRequest()->getPost('city');
        $personPositionSlug = $this->getRequest()->getPost('work-position');
        $personWorkTime     = $this->getRequest()->getPost('work-time');
        $personPhone        = $this->getRequest()->getPost('mobile-phone');

        return array(
            'personName'            => strip_tags($personName),
            'personRegion'          => strip_tags($personRegion),
            'personProvince'        => strip_tags($personProvince),
            'personCity'            => strip_tags($personCity),
            'personPositionId'      => $this->getPositionId($personPositionSlug),
            'personWorkTime'        => strip_tags($personWorkTime),
            'personPhone'           => strip_tags($personPhone),
        );
    }

    /**
     * 通过工作职位的唯一简写(slug)以获取工作职位的唯一标识符.
     * 在个人用户信息表(people)中, 工作职位信息以工作职位的唯一标识符的形式存储.
     * 
     * @param  String $positionSlug - 工作职位的唯一简写(slug)
     * @return 工作职位的唯一标识符
     */
    private function getPositionId($workPositionSlug)
    {
        $sm                 = $this->getServiceLocator();
        $positionTable      = $sm->get('Accounts\Model\PositionTable');
        $position           = $positionTable->getPositionId($workPositionSlug);

        if ( $position == null ) {
            return null;
        }
        return $position->position_id;
    }

    /**
     * 检查个人用户所提交的附加信息是否合法.
     * @param  Array $personInfo - 个人用户的附加信息
     * @return 一个含有若干标志位的数组
     */
    private function verifyPersonInfo($personInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isPersonNameEmpty'         => empty($personInfo['personName']),
            'isPersonNameLegal'         => $this->isNameLegal($personInfo['personName']),
            'isPersonRegionEmpty'       => empty($personInfo['personRegion']),
            'isPersonProvinceEmpty'     => empty($personInfo['personProvince']),
            'isPersonCityEmpty'         => $this->isCityEmpty($personInfo['personCity'], 
                                                              $personInfo['personProvince']),
            'isWorkPositionLegal'       => ( $personInfo['personPositionId'] != null ),
            'isPersonPhoneEmpty'        => empty($personInfo['personPhone']),
            'isPersonPhoneLegal'        => $this->isPhoneNumberLegal($personInfo['personPhone']),
        );
        $result['isSuccessful']   = !$result['isPersonNameEmpty']     &&  $result['isPersonNameLegal'] &&
                                    !$result['isPersonRegionEmpty']   && !$result['isPersonProvinceEmpty'] &&
                                    !$result['isPersonCityEmpty']     &&  $result['isWorkPositionLegal'] &&
                                    !$result['isPersonPhoneEmpty']    &&  $result['isPersonPhoneLegal'];
        return $result;
    }

    /**
     * 检查用户(个人用户, 教师用户)的真实姓名是否合法.
     * 规则: 用户的真实姓名不应该超过32个字符.
     * 
     * @param  String  $name - 用户的真实姓名
     * @return 用户的真实姓名是否合法
     */
    private function isNameLegal($name)
    {
        $MAX_LENGTH_OF_NAME    = 32;
        return ( strlen($name) <= $MAX_LENGTH_OF_NAME );
    }

    /**
     * 检查用户(个人用户, 教师用户, 企业用户)的联系电话是否合法.
     * 规则: 用户的联系电话应该在7-24(包含边界)字符之间, 且只能由数字和-组成.
     * @param  String $phone - 用户的联系电话
     * @return 用户的联系电话员是否合法
     */
    private function isPhoneNumberLegal($phone)
    {
        return (bool)preg_match('/^[0-9-]{7,24}$/', $phone);
    }

    /**
     * 检查用户(个人用户, 教师用户, 企业用户)是否选择了所在城市.
     * 对于处在直辖市的用户, 在字段应该为空.
     * 
     * @param  String $city - 用户所在的城市
     * @param  String $province - 用户所在的省份
     * @return 用户是否选择了所在城市
     */
    private function isCityEmpty($city, $province)
    {
        $municipalities = array( '北京市', '天津市', '上海市', '重庆市' );
        
        if ( $city != null ) {
            return false;
        } else if ( in_array( $province, $municipalities ) ) {
            return false;
        }

        return true;
    }

    /**
     * 处理个人用户完善个人附加信息的请求.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @param  Array $personInfoArray - 包含个人用户的附加信息的数组
     * @return 数据库操作是否成功
     */
    private function processPersonAction($uid, $personInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $personTable        = $sm->get('Accounts\Model\PersonTable');

        $personInfo         = array(
            'uid'                   => $uid,
            'person_name'           => $personInfoArray['personName'],
            'person_region'         => $personInfoArray['personRegion'],
            'person_province'       => $personInfoArray['personProvince'],
            'person_city'           => $personInfoArray['personCity'],
            'person_position_id'    => $personInfoArray['personPositionId'],
            'person_phone'          => $personInfoArray['personPhone'],
        );

        return $personTable->createNewPerson($personInfo);
    }

    /**
     * 为教师用户从HTTP请求中提取个人用户所需的用户信息.
     * @return 一个包含了教师用户的附加信息的数组
     */
    private function getTeacherInfoArray()
    {
        $teacherName        = $this->getRequest()->getPost('real-name');
        $teacherRegion      = $this->getRequest()->getPost('region');
        $teacherProvince    = $this->getRequest()->getPost('province');
        $teacherCity        = $this->getRequest()->getPost('city');
        $teacherField       = $this->getRequest()->getPost('teaching-field');
        $teacherCompany     = $this->getRequest()->getPost('company');
        $teacherPhone       = $this->getRequest()->getPost('mobile-phone');
        $teacherWeibo       = $this->getRequest()->getPost('weibo');

        return array(
            'teacherName'       => strip_tags($teacherName),
            'teacherRegion'     => strip_tags($teacherRegion),
            'teacherProvince'   => strip_tags($teacherProvince),
            'teacherCity'       => strip_tags($teacherCity),
            'teacherField'      => strip_tags($teacherField),
            'teacherCompany'    => strip_tags($teacherCompany),
            'teacherPhone'      => strip_tags($teacherPhone),
            'teacherWeibo'      => strip_tags($teacherWeibo),
        );
    }

    /**
     * 检查教师用户所提交的附加信息是否合法.
     * @param  Array $teacherInfo - 教师用户的附加信息
     * @return 一个含有若干标志位的数组
     */
    private function verifyTeacherInfo($teacherInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isTeacherNameEmpty'        => empty($teacherInfo['teacherName']),
            'isTeacherNameLegal'        => $this->isNameLegal($teacherInfo['teacherName']),
            'isTeacherRegionEmpty'      => empty($teacherInfo['teacherRegion']),
            'isTeacherProvinceEmpty'    => empty($teacherInfo['teacherProvince']),
            'isTeacherCityEmpty'        => $this->isCityEmpty($teacherInfo['teacherCity'], 
                                                              $teacherInfo['teacherProvince']),
            'isTeachingFieldEmpty'      => empty($teacherInfo['teacherField']),
            'isTeachingFieldLegal'      => $this->isTeachingFieldLegal($teacherInfo['teacherField']),
            'isCompanyNameEmpty'        => empty($teacherInfo['teacherCompany']),
            'isCompanyNameLegal'        => $this->isCompanyNameLegal($teacherInfo['teacherCompany']),
            'isTeacherPhoneEmpty'       => empty($teacherInfo['teacherPhone']),
            'isTeacherPhoneLegal'       => $this->isPhoneNumberLegal($teacherInfo['teacherPhone']),
            'isTeacherWeiboLegal'       => $this->isWeiboAccountLegal($teacherInfo['teacherWeibo'])

        );
        $result['isSuccessful']   = !$result['isTeacherNameEmpty']   &&  $result['isTeacherNameLegal'] &&
                                    !$result['isTeacherRegionEmpty'] && !$result['isTeacherProvinceEmpty'] &&
                                    !$result['isTeacherCityEmpty']   &&
                                    !$result['isTeachingFieldEmpty'] &&  $result['isTeachingFieldLegal'] &&
                                    !$result['isCompanyNameEmpty']   &&  $result['isCompanyNameLegal'] &&
                                    !$result['isTeacherPhoneEmpty']  &&  $result['isTeacherPhoneLegal'] &&
                                     $result['isTeacherWeiboLegal'];
        return $result;
    }

    /**
     * 检查用户(教师用户)的授课范围是否合法
     * @param  String $teachingField - 教师用户授课范围的缩写字符串
     * @return 用户的授课范围是否合法
     */
    private function isTeachingFieldLegal($teachingFieldString)
    {
        $teachingFieldSlugs     = split(',', $teachingFieldString);
        if ( count( $teachingFieldSlugs ) > 3 ) {
            return false;
        }

        $teachingFields = $this->getCourseTypes();
        foreach ( $teachingFieldSlugs as $teachingFieldSlug ) {
            if ( !$this->isTeachingFieldSlugLegal($teachingFieldSlug, $teachingFields) ) {
                return false;
            }
        }
        return true;
    }

    /**
     * 判断教师用户提交的授课范围唯一缩写(slug)是否合法.
     * @param  String $fieldSlug - 授课范围唯一缩写(slug)
     * @param  Array $teachingFields - 所有合法的授课范围(CourseType)对象
     * @return 授课范围唯一缩写(slug)是否合法
     */
    private function isTeachingFieldSlugLegal($fieldSlug, $teachingFields)
    {
        foreach ( $teachingFields as $teachingField ) {
            if ( $teachingField->course_type_slug == $fieldSlug ) {
                return true;
            }
        }
        return false;
    }

    /**
     * 判断用户(教师用户, 企业用户)提交的公司名称是否合法.
     * 规则: 一个合法的公司名称的长度不应该超过64个字符.
     * 
     * @param  String $companyName - 用户所在的公司名称
     * @return 公司名称是否合法
     */
    private function isCompanyNameLegal($companyName)
    {
        $MAX_LENGTH_OF_COMPANY_NANE = 64;
        return ( strlen($companyName) <= $MAX_LENGTH_OF_COMPANY_NANE );
    }

    /**
     * 判断用户(教师用户)的微博账号是否合法.
     * 规则: 一个合法的微博账号的长度不应该超过32个字符.
     * 
     * @param  String  $weiboAccount - 用户的微博账号
     * @return 用户的微博账号是否合法
     */
    private function isWeiboAccountLegal($weiboAccount)
    {
        $MAX_LENGTH_OF_WEIBO_ACCOUNT = 32;
        return ( strlen($weiboAccount) <= $MAX_LENGTH_OF_WEIBO_ACCOUNT );
    }

    /**
     * 处理教师用户完善个人附加信息的请求.
     * 该请求会包含如下2个数据库操作:
     *     1. 向教师附加信息表中添加教师的附加信息
     *     2. 向教师授课范围表中添加教师授课范围的信息(以便推荐相关的教师)
     * 
     * @param  int $uid - 用户的唯一标识符(uid)
     * @param  Array $teacherInfoArray - 一个包含了教师用户附加信息的数组
     * @return 数据库操作是否成功
     */
    private function processTeacherAction($uid, $teacherInfoArray)
    {
        $teachingFieldNames         = $this->processTeachingFieldAction($uid, $teacherInfoArray['teacherField']);
        $isTeachingFieldSuccessful  = ( $teachingFieldNames != null );
        $isBasicInfoSuccessful      = $this->processTeacherInfoAction($uid, $teacherInfoArray, $teachingFieldNames);

        return ( $isBasicInfoSuccessful && $isTeachingFieldSuccessful );
    }

    /**
     * 向教师授课范围表(teaching_field)表中添加授课范围的信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @param  String $teachingFields - 教师用户授课范围的缩写字符串
     * @return 教师用户授课范围的中文字符串(以','分隔)
     */
    private function processTeachingFieldAction($uid, $teachingFields)
    {
        $teachingFieldArray     = $this->getCourseTypes();
        $teachingFieldIds       = array();
        $teachingFieldNames     = '';
        $teachingFieldSlugs     = split(',', $teachingFields);

        foreach ( $teachingFieldSlugs as $teachingFieldSlug ) {
            foreach ( $teachingFieldArray as $teachingField ) {
                if ( $teachingField->course_type_slug == $teachingFieldSlug ) {
                    $teachingFieldId = $teachingField->course_type_id;

                    array_push( $teachingFieldIds, $teachingFieldId );
                    $teachingFieldNames .= $teachingField->course_type_name.',';
                    
                    break;
                }
            }
        }

        $this->processTeachingField($uid, $teachingFieldIds);
        return rtrim($teachingFieldNames, ',');
    }

    /**
     * 向教师授课范围表(teaching_field)表中添加授课范围的信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @param  Array $courseTypeIdArray - 包含了教师授课范围的数组
     * @return 数据库操作是否成功
     */
    private function processTeachingField($uid, $courseTypeIdArray)
    {
        $sm                 = $this->getServiceLocator();
        $teachingFieldTable = $sm->get('Accounts\Model\TeachingFieldTable');

        return $teachingFieldTable->changeTeachingField($uid, $courseTypeIdArray);
    }

    /**
     * 向教师附加信息表(teachers)中添加教师的附加信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @param  Array $teacherInfoArray - 一个包含了教师用户附加信息的数组
     * @param  String $teachingField - 包含了教师授课范围的字符串
     * @return 数据库操作是否成功
     */
    private function processTeacherInfoAction($uid, $teacherInfoArray, $teachingField)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');

        $teacherInfo        = array(
            'uid'               => $uid,
            'teacher_name'      => $teacherInfoArray['teacherName'],
            'teacher_field'     => $teachingField,
            'teacher_region'    => $teacherInfoArray['teacherRegion'],
            'teacher_province'  => $teacherInfoArray['teacherProvince'],
            'teacher_city'      => $teacherInfoArray['teacherCity'],
            'teacher_company'   => $teacherInfoArray['teacherCompany'],
            'teacher_phone'     => $teacherInfoArray['teacherPhone'],
            'teacher_weibo'     => $teacherInfoArray['teacherWeibo'],
        );

        return $teacherTable->createNewTeacher($teacherInfo);
    }

    /**
     * 为企业用户从HTTP请求中提取个人用户所需的用户信息.
     * @return 一个包含了企业用户的附加信息的数组
     */
    private function getCompanyInfoArray()
    {
        $companyName        = $this->getRequest()->getPost('company-name');
        $companyRegion      = $this->getRequest()->getPost('region');
        $companyProvince    = $this->getRequest()->getPost('province');
        $companyCity        = $this->getRequest()->getPost('city');
        $companyAddress     = $this->getRequest()->getPost('address');
        $companyField       = $this->getRequest()->getPost('company-field');
        $companyScale       = $this->getRequest()->getPost('company-scale');
        $companyPhone       = $this->getRequest()->getPost('phone');

        return array(
            'companyName'       => strip_tags($companyName),
            'companyRegion'     => strip_tags($companyRegion),
            'companyProvince'   => strip_tags($companyProvince),
            'companyCity'       => strip_tags($companyCity),
            'companyAddress'    => strip_tags($companyAddress),
            'companyField'      => strip_tags($companyField),
            'companyScale'      => strip_tags($companyScale),
            'companyPhone'      => strip_tags($companyPhone),
        );
    }

    /**
     * 检查企业用户所提交的附加信息是否合法.
     * @param  Array $companyInfo - 企业用户的附加信息
     * @return 一个含有若干标志位的数组
     */
    private function verifyCompanyInfo($companyInfo)
    {
        $result = array(
            'isSuccessful'              => false,
            'isCompanyNameEmpty'        => empty($companyInfo['companyName']),
            'isCompanyNameLegal'        => $this->isCompanyNameLegal($companyInfo['companyName']),
            'isCompanyRegionEmpty'      => empty($companyInfo['companyRegion']),
            'isCompanyProvinceEmpty'    => empty($companyInfo['companyProvince']),
            'isCompanyCityEmpty'        => $this->isCityEmpty($companyInfo['companyCity'], 
                                                              $companyInfo['companyProvince']),
            'isCompanyAddressEmpty'     => empty($companyInfo['companyAddress']),
            'isCompanyAddressLegal'     => $this->isAddressLegal($companyInfo['companyAddress']),
            'isCompanyFieldEmpty'       => empty($companyInfo['companyField']),
            'isCompanyFieldLegal'       => $this->isCompanyFieldLegal($companyInfo['companyField']),
            'isCompanyScaleEmpty'       => empty($companyInfo['companyScale']),
            'isCompanyScaleLegal'       => $this->isCompanyScaleLegal($companyInfo['companyScale']),
            'isCompanyPhoneEmpty'       => empty($companyInfo['companyPhone']),
            'isCompanyPhoneLegal'       => $this->isPhoneNumberLegal($companyInfo['companyPhone']),
        );
        $result['isSuccessful']   = !$result['isCompanyNameEmpty']    &&  $result['isCompanyNameLegal'] &&
                                    !$result['isCompanyRegionEmpty']  && !$result['isCompanyProvinceEmpty'] &&
                                    !$result['isCompanyCityEmpty']    && 
                                    !$result['isCompanyAddressEmpty'] &&  $result['isCompanyAddressLegal'] &&
                                    !$result['isCompanyFieldEmpty']   &&  $result['isCompanyFieldLegal'] &&
                                    !$result['isCompanyScaleEmpty']   &&  $result['isCompanyScaleLegal'] &&
                                    !$result['isCompanyPhoneEmpty']   &&  $result['isCompanyPhoneLegal'];
        return $result;
    }

    /**
     * 检查用户(企业用户)的公司详细地址是否合法.
     * 规则: 一个合法的公司详细地址不应该超过256个字符.
     * 
     * @param  String  $address - 公司的详细地址
     * @return 用户的公司详细地址是否合法
     */
    private function isAddressLegal($address)
    {
        $MAX_LENGTH_OF_ADDRESS      = 256;
        return ( strlen($address) <= $MAX_LENGTH_OF_ADDRESS );
    }

    /**
     * 检查用户(企业用户)的公司行业是否合法.
     * 规则: 一个合法的公司行业不应该超过128个字符.
     * 
     * @param  String  $companyField - 公司所在的行业
     * @return 用户公司的行业是否合法
     */
    private function isCompanyFieldLegal($companyField)
    {
        $MAX_LENGTH_OF_FIELD        = 128;
        return ( strlen($companyField) < $MAX_LENGTH_OF_FIELD );
    }

    /**
     * 检查用户的公司规模是否合法.
     * 规则: 一个合法的公司规模应该是一个数字, 且应该是10, 100, 100, 
     * 10000和32767之间的一个值.
     * 
     * @param  String  $companyScale - 公司规模
     * @return 用户的公司规模是否合法
     */
    private function isCompanyScaleLegal($companyScale)
    {
        if ( !is_numeric($companyScale) ) {
            return false;
        }
        return true;
    }

    /**
     * 处理企业用户完善个人附加信息的请求.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @param  Array $companyInfoArray - 一个包含了企业用户附加信息的数组
     * @return 数据库操作是否成功
     */
    private function processCompanyAction($uid, $companyInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $companyTable       = $sm->get('Accounts\Model\CompanyTable');

        $companyInfo     = array(
            'uid'               => $uid,
            'company_name'      => $companyInfoArray['companyName'],
            'company_region'    => $companyInfoArray['companyRegion'],
            'company_province'  => $companyInfoArray['companyProvince'],
            'company_city'      => $companyInfoArray['companyCity'],
            'company_address'   => $companyInfoArray['companyAddress'],
            'company_field'     => $companyInfoArray['companyField'],
            'company_scale'     => $companyInfoArray['companyScale'],
            'company_phone'     => $companyInfoArray['companyPhone'],
        );

        return $companyTable->createNewCompany($companyInfo);
    }
}
