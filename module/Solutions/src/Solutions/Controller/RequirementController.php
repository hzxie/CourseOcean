<?php

namespace Solutions\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

/**
 * 处理用户需求的控制器.
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class RequirementController extends AbstractActionController
{
    /**
     * Default method to call in the controller.
     * 
     * @return a ViewModel object which contains HTML content
     */
    public function pageAction()
    {
        if ( !$this->isLogined() ) {
            $this->sendRedirect('accounts/login');
        }

        $profile = $this->getUserProfile();
        return array(
            'profile'   => $profile,
        );
    }

    /**
     * 检查用户是否已经登陆.
     * @return 用户是否已经登陆
     */
    private function isLogined()
    {
        $session    = new Container('itp_session');
        return $session->offsetGet('uid');
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
     * 获取网站的基础路径. (如localhost/itp)
     * @return 网站的基础路径
     */
    private function basePath()
    {
        $renderer   = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url        = $renderer->basePath();
        return $url;
    }

    /**
     * 获取用户的基本信息与联系信息.
     * @return 一个包含用户的基本信息与联系信息的数组
     */
    private function getUserProfile()
    {
        $userProfile            = $this->getSessionData();

        $uid                    = $userProfile['uid'];
        $userGroupSlug          = $userProfile['userGroupSlug'];

        $getExtraProfileFunc    = 'get'.$userGroupSlug.'Profile';
        $userProfile           += $this->$getExtraProfileFunc($uid);

        return $userProfile;        
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
        );
        return $sessionData;
    }

    /**
     * 获取个人用户的基本信息与联系信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @return 一个包含用户基本信息和联系信息的数组
     */
    private function getPersonProfile($uid)
    {
        $sm             = $this->getServiceLocator();
        $personTable    = $sm->get('Accounts\Model\PersonTable');
        $person         = $personTable->getPersonInfo($uid);

        return array(
            'name'      => $person->person_name,
            'phone'     => $person->person_phone,
        );
    }

    /**
     * 获取教师用户的基本信息与联系信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @return 一个包含用户基本信息和联系信息的数组
     */
    private function getTeacherProfile($uid)
    {
        $sm             = $this->getServiceLocator();
        $teacherTable   = $sm->get('Accounts\Model\TeacherTable');
        $teacher        = $teacherTable->getTeacherInfo($uid);

        return array(
            'name'      => $teacher->teacher_name,
            'phone'     => $teacher->teacher_phone,
        );
    }

    /**
     * 获取企业用户的基本信息与联系信息.
     * @param  int $uid - 用户的唯一标识符(uid)
     * @return 一个包含用户基本信息和联系信息的数组
     */
    private function getCompanyProfile($uid)
    {
        $sm             = $this->getServiceLocator();
        $companyTable   = $sm->get('Accounts\Model\CompanyTable');
        $company        = $companyTable->getCompanyInfo($uid);

        return array(
            'name'      => $company->company_name,
            'phone'     => $company->company_phone,
        );
    }

    /**
     * 在用户输入讲师时, 为用户提供搜索建议.
     * @param  String $keyword - 用户输入的关键字
     * @return 一个包含讲师信息的JSON数组
     */
    public function getTeachersAction()
    {
        $keyword        = $this->getRequest()->getQuery('keyword');

        $sm             = $this->getServiceLocator();
        $teacherTable   = $sm->get('Accounts\Model\TeacherTable');
        $resultSet      = $teacherTable->searchTeacher($keyword);
        $teachersArray  = $this->getTeachersArray($resultSet);

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($teachersArray) );
        return $response;
    }

    /**
     * 将从数据库获取的包含讲师信息的ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 包含讲师信息的ResultSet对象
     * @return 包含讲师信息的数组
     */
    private function getTeachersArray($resultSet)
    {
        $teachersArray  = array();

        foreach ( $resultSet as $teacher ) {
            array_push($teachersArray, 
                array(
                    'uid'               => $teacher->uid,
                    'teacher_avatar'    => $teacher->teacher_avatar,
                    'teacher_name'      => $teacher->teacher_name,
                )
            );
        }
        return $teachersArray;
    }

    /**
     * 处理用户提交的需求.
     * @return 包含若干标志位的JSON数组.
     */
    public function processAction()
    {
        $sessionData    = $this->getSessionData();
        $uid            = $sessionData['uid'];
        $requirement    = $this->getRequirement($uid);
        $result         = $this->verifyRequirement($requirement);

        if ( $result['isSuccessful'] ) {
            $sm                     = $this->getServiceLocator();
            $requirementTable       = $sm->get('Solutions\Model\RequirementTable');

            $result['isSuccessful'] = $requirementTable->createRequirement($requirement);
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 从HTTP请求中获取需求的相关信息, 以数组形式返回.
     * @param  int $uid - 需求提出者的用户唯一标识符(uid)
     * @return 一个包含需求必要信息的数组
     */
    private function getRequirement($uid)
    {
        $teacherUid     = $this->getRequest()->getPost('teacher-uid');
        $participants   = $this->getRequest()->getPost('participants');
        $startTime      = $this->getRequest()->getPost('start-time');
        $endTime        = $this->getRequest()->getPost('end-time');
        $place          = $this->getRequest()->getPost('place');
        $detail         = $this->getRequest()->getPost('detail');

        return array(
            'requirement_from_uid'      => $uid,
            'requirement_to_uid'        => $teacherUid,
            'requirement_participants'  => $participants,
            'requirement_start_time'    => $startTime,
            'requirement_end_time'      => $endTime,
            'requirement_place'         => strip_tags($place),
            'requirement_detail'        => strip_tags($detail),
        );
    }

    /**
     * 检查需求的相关参数是否合法.
     * @param  Array $requirement - 一个包含需求必要信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function verifyRequirement($requirement)
    {
        $result = array(
            'isSuccessful'          => false,
            'isTeacherUidLegal'     => $this->isTeacherUidLegal($requirement['requirement_to_uid']),
            'isParticipantsEmpty'   => empty($requirement['requirement_participants']),
            'isParticipantsLegal'   => is_numeric($requirement['requirement_participants']),
            'isStartTimeEmpty'      => empty($requirement['requirement_start_time']),
            'isStartTimeLegal'      => $this->isStartTimeLegal($requirement['requirement_start_time']),
            'isEndTimeEmpty'        => empty($requirement['requirement_end_time']),
            'isEndTimeLegal'        => $this->isEndTimeLegal($requirement['requirement_end_time'],
                                                             $requirement['requirement_start_time']),
            'isPlaceEmpty'          => empty($requirement['requirement_place']),
            'isPlaceLegal'          => $this->isPlaceLegal($requirement['requirement_place']),
            'isDetailEmpty'         => empty($requirement['requirement_detail']),
        );

        $result['isSuccessful'] =  $result['isTeacherUidLegal']   &&
                                  !$result['isParticipantsEmpty'] && $result['isParticipantsLegal'] &&
                                  !$result['isStartTimeEmpty']    && $result['isStartTimeLegal'] &&
                                  !$result['isEndTimeEmpty']      && $result['isEndTimeLegal'] &&
                                  !$result['isPlaceEmpty']        && $result['isPlaceLegal'] &&
                                  !$result['isDetailEmpty'];
        return $result;
    }

    /**
     * 检查所选讲师是否存在(所提交的讲师UID是否合法)
     * @param  int $teacherUid - 用户所选讲师的用户唯一标识符(uid)
     * @return 用户所选讲师是否存在
     */
    private function isTeacherUidLegal($teacherUid)
    {
        if ( $teacherUid == 0 ) {
            return true;
        }

        $sm             = $this->getServiceLocator();
        $teacherTable   = $sm->get('Accounts\Model\TeacherTable');

        return $teacherTable->isTeacherExists($teacherUid);
    }

    /**
     * 检查培训起始时间是否合法.
     * 规则: 培训的起始时间不得早于当前时间.
     * 
     * @param  String $startTime - 培训的起始时间
     * @return 培训起始时间是否合法
     */
    private function isStartTimeLegal($startTime)
    {

        $currentTimeObject  = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        $currentTime        = $currentTimeObject->format('Y-m-d H:i:s');
        return ( strtotime($currentTime) < strtotime($startTime) );
    }

    /**
     * 检查培训结束时间是否合法.
     * 规则: 培训的起始时间不得晚于结束时间.
     * 
     * @param  String $endTime   - 培训的结束时间
     * @param  String $startTime - 培训的起始时间
     * @return 培训结束时间是否合法
     */
    private function isEndTimeLegal($endTime, $startTime)
    {
        return ( strtotime($startTime) < strtotime($endTime) );
    }

    /**
     * 检查培训地点是否合法.
     * 规则: 培训地点的地址不能超过256个字符.
     * 
     * @param  String  $place - 培训地点的地址
     * @return 培训地点是否合法
     */
    private function isPlaceLegal($place)
    {
        $MAX_LENGTH_OF_PLACE = 256;
        return ( strlen($place) <= $MAX_LENGTH_OF_PLACE );
    }
}
