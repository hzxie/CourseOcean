<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 处理用户控制面板请求的控制器.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class DashboardController extends AbstractActionController
{
    /**
     * 一个包含了用户个人信息的数组.
     * @var Array
     */
    private $profile;

    /**
     * 该控制器的默认方法, 显示用户登录页面.
     * @return 一个包含了HTML内容的ViewModel对象
     */
    public function indexAction()
    {
        if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/login');
        }
        if ( !$this->isActivated() ) {
            return $this->sendRedirect('accounts/register/email');   
        }
        
        $this->profile      = $this->getUserProfile();
        $uid                = $this->profile['uid'];
        $userGroupSlug      = lcfirst($this->profile['userGroupSlug']);
        $isProfileCompleted = $this->profile['isProfileCompleted'];

        if ( !$isProfileCompleted ) {
            return $this->sendRedirect('accounts/register/complete');
        }

        $view = new ViewModel(
            array( 
                'profile'       => $this->profile,
                'extra'         => $this->getExtraData($userGroupSlug),
            )
        );
        $view->setTemplate("accounts/dashboard/$userGroupSlug.phtml");
        return $view;
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
     * 检查用户是否已经验证了电子邮件.
     * @return 用户是否已经验证了电子邮件
     */
    private function isActivated()
    {
        $session    = new Container('itp_session');
        return $session->offsetExists('isActivated');
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
     * 获取用户的个人信息, 以数组的形式返回.
     * @return 一个包含用户个人资料的数组
     */
    private function getUserProfile()
    {
        $userData           = array('isProfileCompleted' => false);
        $userData          += $this->getSessionData();

        $uid                = $userData['uid'];
        $userGroupSlug      = $userData['userGroupSlug'];

        $getProfileFunction = 'get'.$userGroupSlug.'Info';
        if ( method_exists($this, $getProfileFunction) ) {
            $extraUserData  = $this->$getProfileFunction($uid);

            if ( $extraUserData ) {
                $userData['isProfileCompleted'] = true;
                $userData  += $extraUserData;
            }
        }
        return $userData;
    }

    /**
     * 从Session中获取用户的基本信息.
     * @return 一个包含用户基本信息的数组
     */
    private function getSessionData()
    {
        $session    = new Container('itp_session');

        $sessionData = array(
            'uid'               => $session->offsetGet('uid'),
            'username'          => $session->offsetGet('username'),
            'email'             => $session->offsetGet('email'),
            'userGroupSlug'     => $session->offsetGet('userGroupSlug'),
            'lastTimeSignIn'    => $session->offsetGet('lastTimeSignIn'),
        );
        return $sessionData;
    }

    /**
     * 获取用户的附加信息(例如参加讲座的情况等).
     * @param  String $userGroupSlug - 用户组的英文唯一简称(slug)
     * @return 一个包含用户个人信息的数组
     */
    private function getExtraData($userGroupSlug)
    {
        
    }

    /**
     * 获取个人用户的基本信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @return 一个包含了用户基本信息的数组
     */
    private function getPersonInfo($uid)
    {
        $sm                 = $this->getServiceLocator();
        $personTable        = $sm->get('Accounts\Model\PersonTable');
        $person             = $personTable->getPersonInfo($uid);

        return  $this->getProfileArray($person);
    }

    /**
     * 获取个人用户的附加信息.
     * @return 一个包含个人用户附加信息的数组
     */
    private function getExtraDataForPerson()
    {
        # Not Implemented
    }

    /**
     * 获取讲师用户的基本信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @return 一个包含讲师基本信息的数组
     */
    private function getTeacherInfo($uid)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');
        $teacher            = $teacherTable->getTeacherInfo($uid);

        $teacherInfo        = $this->getProfileArray($teacher);
        return $teacherInfo;
    }

    /**
     * 获取讲师用户的附加信息.
     * @return 一个包含讲师用户附加信息的数组
     */
    private function getExtraDataForTeacher()
    {
        # Not Implemented
    }

    /**
     * 获取企业用户的个人信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @return 一个包含企业个人信息的数组
     */
    private function getCompanyInfo($uid)
    {
        $sm                 = $this->getServiceLocator();
        $companyTable       = $sm->get('Accounts\Model\CompanyTable');
        $company            = $companyTable->getCompanyInfo($uid);

        $companyInfo        = $this->getProfileArray($company);
        return $companyInfo;
    }

    /**
     * 获取企业用户的附加信息.
     * @return 一个包含企业用户附加信息的数组
     */
    private function getExtraDataForCompany()
    {
        # Not Implemented
    }

    /**
     * 将从数据库获取的Model对象转换为数组并返回.
     * @param  Object $profileInfo - 一个包含了用户个人信息的的对象, 可能是
     *         Person/ Teacher/ Company之一的对象.
     * @return 一个包含用户个人信息的数组
     */
    private function getProfileArray($profileInfo)
    {
        $profileInfoArray   = array();

        foreach ( $profileInfo as $key => $value ) {
            $profileInfoArray[ $key ] = $value;
        }
        return $profileInfoArray;
    }

    /**
     * 处理用户修改密码的请求.
     * @return 包含若干标志位的JSON数组
     */
    public function changePasswordAction()
    {
        $oldPassword        = $this->getRequest()->getPost('old-password');
        $newPassword        = $this->getRequest()->getPost('new-password');
        $confirmPassword    = $this->getRequest()->getPost('password-again');
        $this->profile      = $this->getUserProfile();
        $uid                = $this->profile['uid'];

        $result     = array(
            'isSuccessful'              => false,
            'isOldPasswordEmpty'        => empty($oldPassword),
            'isOldPasswordCorrect'      => $this->isPasswordCorrect($uid, $oldPassword),
            'isNewPasswordEmpty'        => empty($newPassword),
            'isNewPasswordLegal'        => $this->isPasswordLegal($newPassword),
            'isConfirmPasswordEmpty'    => empty($confirmPassword),
            'isConfirmPasswordMatched'  => ( $newPassword == $confirmPassword ),
        );

        $result['isSuccessful'] = !$result['isOldPasswordEmpty']     && $result['isOldPasswordCorrect'] &&
                                  !$result['isNewPasswordEmpty']     && $result['isNewPasswordLegal']   &&
                                  !$result['isConfirmPasswordEmpty'] && $result['isConfirmPasswordMatched'];
        if ( $result['isSuccessful'] ) {
            $result['isSuccessful'] = $this->changePassword($uid, $newPassword);
        }

        $response   = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 验证用户(在修改密码时)所填写的旧密码是否正确.
     * @param  int    $uid - 用户的唯一标识符
     * @param  String $password - 用户所填写的旧密码
     * @return 用户填写的旧密码是否正确
     */
    private function isPasswordCorrect($uid, $password)
    {
        $sm             = $this->getServiceLocator();
        $userTable      = $sm->get('Accounts\Model\UserTable');

        $passport       = array(
            'uid'       => $uid,
            'password'  => md5($password),
        );
        return $userTable->isPasswordCorrect($passport);
    }

    /**
     * 验证新密码是否合法.
     * 规则: 用户的新密码必须在6~16个字符之间.
     * 
     * @param  String  $password - 用户所使用的新密码
     * @return 新密码是否合法
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
     * 修改用户的密码(在数据库中对用户密码进行修改)
     * @param  int    $uid - 用户的唯一标识符
     * @param  String $password - 用户希望使用的新密码
     * @return 数据库操作是否成功
     */
    private function changePassword($uid, $password)
    {
        $sm             = $this->getServiceLocator();
        $userTable      = $sm->get('Accounts\Model\UserTable');

        $passport       = array(
            'uid'       => $uid,
            'password'  => md5($password),
        );
        return $userTable->changePassword($passport);
    }

    /**
     * 获取用户参加讲座的记录, 以JSON数组的形式返回.
     * @return 一个包含用户参与讲座记录的JSON数组
     */
    public function getLectureAttendanceAction()
    {
        $offset                         = (int)$this->getRequest()->getQuery('offset', 0);
        $NUMBER_OF_RECORDS_PER_QUERY    = 10;

        $this->profile          = $this->getUserProfile();
        $uid                    = $this->profile['uid'];
        $sm                     = $this->getServiceLocator();
        $lectureAttendanceTable = $sm->get('Solutions\Model\LectureAttendanceTable');
        $resultSet              = $lectureAttendanceTable->
                                    getAttendaceRecordsOfUser($uid, $offset, $NUMBER_OF_RECORDS_PER_QUERY);
        $result                 = $this->getLectureAttendanceArray($resultSet);

        $response   = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * Convert ResultSet which contains attendance records of lectures object 
     * to an array.
     * @param  ResultSet $attendanceRecords - an ResultSet object which contains
     *          attendance records of lectures
     * @return an array which contains attendance records of lectures
     */
    private function getLectureAttendanceArray($attendanceRecords)
    {
        $lectureAttendanceArray = array();

        foreach ( $attendanceRecords as $attendanceRecord ) {
            $lectureAttendanceRecordArray = array();
            foreach ( $attendanceRecord as $key => $value) {
                $lectureAttendanceRecordArray[$key] = $value;
            }
            array_push($lectureAttendanceArray, $lectureAttendanceRecordArray);
        }
        return $lectureAttendanceArray;
    }
}
