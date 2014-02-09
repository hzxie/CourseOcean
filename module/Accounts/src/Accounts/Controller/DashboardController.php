<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class DashboardController extends AbstractActionController
{
    /**
     * An array which contains profile of the user.
     * @var Array
     */
    private $profile;

    /**
     * Default method to call in the controller.
     * 
     * It will check if the user has logined, if not, it will 
     * send a HTTP redirect response.
     * 
     * @return a ViewModel object which contains HTML content
     */
    public function indexAction()
    {
        if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/login');
        }
        
        $this->profile  = $this->getUserData();
        $userGroupSlug  = $this->profile['userGroupSlug'];

        $view = new ViewModel(
            array( 'profile' => $this->profile )
        );
        $view->setTemplate("accounts/dashboard/$userGroupSlug.phtml");
        return $view;
    }

    /**
     * Check if the user has logined.
     * @return true if the user has logined
     */
    private function isAllowedToAccess()
    {
        $session    = new Container('itp_session');
        return $session->offsetExists('isLogined');
    }

    /**
     * Send HTTP redirect reponse.
     * @param  String $redirectPath - the pasth to redirect
     * @return an HTTP redirect reponse object
     */
    private function sendRedirect($redirectPath = '')
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * Get the profile of the user.
     * @return an array which contains user's profile
     */
    private function getUserData()
    {
        $userData           = array();
        $userData          += $this->getSessionData();

        $uid                = $userData['uid'];
        $userGroupID        = $userData['userGroupID'];
        $userGroupSlug      = ucfirst($this->getUserGroupSlug($userGroupID));

        $getProfileFunction = 'get'.$userGroupSlug.'Info';
        if ( method_exists($this, $getProfileFunction) ) {
            $userData      += $this->$getProfileFunction($uid);
        }
        return $userData;
    }

    /**
     * Get user profile from the session.
     * @return an array which contains user's profile
     */
    private function getSessionData()
    {
        $session    = new Container('itp_session');

        $sessionData = array(
            'uid'               => $session->offsetGet('uid'),
            'username'          => $session->offsetGet('username'),
            'email'             => $session->offsetGet('email'),
            'userGroupID'       => $session->offsetGet('userGroupID'),
            'lastTimeSignIn'    => $session->offsetGet('lastTimeSignIn'),
        );
        return $sessionData;
    }

    /**
     * Get the unique slug of the user group by its id.
     * @param  int $userGroupID - the unique id of the user group
     * @return the unique slug of the user group
     */
    private function getUserGroupSlug($userGroupID)
    {
        if ( $userGroupID == 0 ) {
            return 'administrator';
        }

        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupSlug($userGroupID);

        if ( $userGroup == null ) {
            return null;
        }
        return $userGroup->user_group_slug;
    }

    /**
     * Get profile of a person.
     * @param  int $uid - the unique id of the user
     * @return an array which contains profile of a person
     */
    private function getPersonInfo($uid)
    {
        $personInfo         = array(
            'userGroupSlug' => 'person',
        );

        $sm                 = $this->getServiceLocator();
        $personTable        = $sm->get('Accounts\Model\PersonTable');
        $person             = $personTable->getPersonInfo($uid);

        $personInfo     += $this->getProfileArray($person);
        return $personInfo;
    }

    /**
     * Get profile of a teacher.
     * @param  int $uid - the unique id of the user
     * @return an array which containsp profile of a teacher
     */
    private function getTeacherInfo($uid)
    {
        $teacherInfo        = array(
            'userGroupSlug' => 'teacher',
        );

        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');
        $teacher            = $teacherTable->getTeacherInfo($uid);

        $teacherInfo      += $this->getProfileArray($teacher);
        return $teacherInfo;
    }

    /**
     * Get profile of an enterprise.
     * @param  int $uid - the unique id of the user
     * @return an array which contains profile of an enterprise
     */
    private function getEnterpriseInfo($uid)
    {
        $enterpriseInfo  = array(
            'userGroupSlug' => 'enterprise',
        );

        $sm                 = $this->getServiceLocator();
        $enterpriseTable    = $sm->get('Accounts\Model\EnterpriseTable');
        $enterprise         = $enterpriseTable->getEnterpriseInfo($uid);

        $enterpriseInfo    += $this->getProfileArray($enterprise);
        return $enterpriseInfo;
    }

    /**
     * Get profile within an array.
     * @param  Object $profileInfo - an object of (Person/ Teacher/ Enterprise)
     *         which contains profile of the user
     * @return an array which contains profile of the user
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
     * Handle asynchronous changing password requests for a user.
     * @return an array which contains query result
     */
    public function changePasswordAction()
    {
        $oldPassword        = $this->getRequest()->getPost('old-password');
        $newPassword        = $this->getRequest()->getPost('new-password');
        $confirmPassword    = $this->getRequest()->getPost('password-again');
        $this->profile      = $this->getUserData();
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
     * Verify if the old password is correct.
     * @param  int    $uid - the unique id of the user
     * @param  String $password - the password of the user
     * @return true if the old password is correct
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
     * Verify if the password is legal.
     * Rule: the length of the password should no less than 6 characters,
     *       and no more than 16 characters.
     * 
     * @param  String  $password - the password of the user
     * @return true if the password is legal
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
     * Handle asynchronous changing password requests for a user.
     * @param  int    $uid - the unique id of the user
     * @param  String $password - the new password of the user
     * @return true if the query is successful
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
     * Handle asynchronous editing profile requests for a person.
     * @return an array which contains query result
     */
    public function editPersonProfileAction()
    {
        $email                  = $this->getRequest()->getPost('email');
        $phone                  = $this->getRequest()->getPost('phone');
        $this->profile          = $this->getUserData();
        $uid                    = $this->profile['uid'];
        $userGroupSlug          = $this->profile['userGroupSlug'];

        $result     = array(
            'isSuccessful'      => false,
            'isUserGroupLegal'  => $this->isUserGroupLegal($userGroupSlug, 'person'),
            'isEmailEmpty'      => empty($email),
            'isEmailLegal'      => $this->isEmailLegal($email),
            'isEmailExists'     => $this->isEmailExists($uid, $email),
            'isPhoneEmpty'      => empty($phone),
            'isPhoneLegal'      => $this->isPhoneNumberLegal($phone),
        );

        $result['isSuccessful'] = !$result['isEmailEmpty']  &&  $result['isEmailLegal'] &&
                                  !$result['isEmailExists'] && !$result['isPhoneEmpty'] && 
                                   $result['isPhoneLegal']  &&  $result['isUserGroupLegal'];
        if ( $result['isSuccessful'] ) {
            $uid                    = $this->profile['uid'];
            $result['isSuccessful'] = $this->editPersonProfile($uid, $email, $phone);
        }

        $response   = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * Handle asynchronous editing profile requests for a person.
     * @param  int $uid - the unique id of the user
     * @param  String $email - the email of the user
     * @param  String $phone - the phone number of the user
     * @return true if the query is successful
     */
    private function editPersonProfile($uid, $email, $phone)
    {
        $isEditEmailSuccessful      = $this->editUserEmail($uid, $email);
        $isEditProfileSuccessful    = $this->editParticalPersonProfile($uid, $phone);
        $isSuccessful               = $isEditEmailSuccessful && $isEditProfileSuccessful;

        if ( $isSuccessful ) {
            $this->updateSession($email);
        }
        return $isSuccessful;
    }

    /**
     * Handle asynchronous editing email requests for a user.
     * @param  int $uid - the unique id of the user
     * @param  String $email - the email of the user
     * @return true if the query is successful
     */
    private function editUserEmail($uid, $email)
    {
        $sm             = $this->getServiceLocator();
        $userTable      = $sm->get('Accounts\Model\UserTable');

        $userInfo       = array(
            'uid'       => $uid,
            'email'     => $email,
        );
        return $userTable->editProfile($userInfo);
    }

    /**
     * Handle asynchronous editing profile(without email) requests for a 
     * user.
     * @param  int $uid - the unique id of the user
     * @param  String $phone - the phone number of the user
     * @return true if the query is successful
     */
    private function editParticalPersonProfile($uid, $phone)
    {
        $sm             = $this->getServiceLocator();
        $personTable    = $sm->get('Accounts\Model\PersonTable');

        $personInfo     = array(
            'uid'       => $uid,
            'phone'     => $phone,
        );
        return $personTable->editProfile($personInfo);
    }

    /**
     * Verify if the email address is legal.
     * @param  String  $email - the email address of the user
     * @return true if the email is legal
     */
    private function isEmailLegal($email)
    {
        return (bool)preg_match('/^[A-Z0-9._%-]{4,18}@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);
    }

    /**
     * Verify if the email address has existed.
     * @param  int $uid - the unique id of the user
     * @param  String  $email - the email address of the user
     * @return true if the email has existed
     */
    private function isEmailExists($uid, $email)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');
        $user               = $userTable->isEmailExists($email);

        if ( $user == null ) {
            return false;
        } else {
            return ( $user->uid != $uid );
        }
    }

    /**
     * Verify if the phone number of the user is legal.
     * @param  String  $phone - the phone number of the user
     * @return true if the phone number of the user is legal
     */
    private function isPhoneNumberLegal($phone)
    {
        return (bool)preg_match('/^[0-9-]{7,24}$/', $phone);
    }

    /**
     * Verify if the user group is legal, avoiding illegal access.
     * @param  String  $userGroupSlug - the user group of the user
     * @param  String  $requestUserGroupSlug - the user group requsted by 
     *         the user
     * @return true if the user group is metched
     */
    private function isUserGroupLegal($userGroupSlug, $requestUserGroupSlug)
    {
        return ( $userGroupSlug == $requestUserGroupSlug );
    }

    /**
     * Update email in the session after updating profile.
     * @param String  $email - the email address of the user
     */
    private function updateSession($email)
    {
        $session    = new Container('itp_session');
        $session->offsetSet('email', $email);
    }

    /**
     * Verify if the name of the company is legal.
     * Rule: the length of the company name should no more than 
     *       64 characters.
     * 
     * @param  String  $companyName - the name of the company
     * @return true if the name of the company is legal
     */
    private function isCompanyLegal($companyName)
    {
        $MAX_LENGTH_OF_COMPANY_NAME = 64;
        return ( strlen($companyName) <= $MAX_LENGTH_OF_COMPANY_NAME );
    }

    /**
     * Verify if the field of research of the teacher is legal.
     * Rule: the length of the field should no more than 128 characters.
     * 
     * @param  String  $field - field of research of the teacher
     * @return true if the field of research of the teacher is legal
     */
    private function isFieldLegal($field)
    {
        $MAX_LENGTH_OF_FIELD        = 128;
        return ( strlen($field) <= $MAX_LENGTH_OF_FIELD );
    }
}
