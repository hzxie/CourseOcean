<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Handle requests on the register page.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class RegisterController extends AbstractActionController
{
    /**
     * Default method to call in the controller.
     * @return a ViewModel object which contains HTML content
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Handle asynchronous register requests for the users.
     * @return a HTTP response object which contains JSON data
     *         infers whether the register is successful
     */
    public function processAction()
    {
        $basicInfoArray             = $this->getBasicInfoArray();
        $result                     = $this->verifyBasicInfo($basicInfoArray);
        
        $userGroupSlug              = $basicInfoArray['userGroupSlug'];
        $getExtraInfoFunction       = 'get'.    $userGroupSlug.'InfoArray';
        $verifyExtraInfoFunction    = 'verify'. $userGroupSlug.'Info';
        $processExtraInfoFunction   = 'process'.$userGroupSlug.'Action';

        $extraInfoArray             = $this->$getExtraInfoFunction();
        $result                    += $this->$verifyExtraInfoFunction($extraInfoArray);

        $result['isSuccessful']     = false;
        if ( $result['isBasicSuccessful'] && $result['isDetailSuccessful'] ) {
            $uid                    = $this->processBasicAction($basicInfoArray);
            $result['isSuccessful'] = $this->$processExtraInfoFunction($uid, $extraInfoArray);
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * Create a record in the users table.
     * @param  Array $basicInfoArray - an array which contains basic 
     *         information of the user
     * @return the unique id of the user
     */
    private function processBasicAction($basicInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        $basicInfo          = array(
            'username'      => $basicInfoArray['username'],
            'password'      => md5($basicInfoArray['password']),
            'email'         => $basicInfoArray['email'],
            'user_group_id' => $basicInfoArray['userGroupID'],
        );
        
        return $userTable->createNewUser($basicInfo);
    }

    /**
     * Get basic information within an array.
     * @return an array which contains basic information of the user
     */
    private function getBasicInfoArray()
    {
        $username               = $this->getRequest()->getPost('username');
        $email                  = $this->getRequest()->getPost('email');
        $password               = $this->getRequest()->getPost('password');
        $confirmPassword        = $this->getRequest()->getPost('password-again');
        $userGroupSlug          = $this->getRequest()->getPost('user-group');

        return array(
            'username'          => $username,
            'email'             => $email,
            'password'          => $password,
            'confirmPassword'   => $confirmPassword,
            'userGroupSlug'     => ucfirst($userGroupSlug),
            'userGroupID'       => $this->getUserGroupID($userGroupSlug),
        );
    }

    /**
     * Verify if the basic information of the user is legal.
     * @param  Array $basicInfo - an array which contains basic information 
     *         of the user
     * @return an array which contains query result
     */
    private function verifyBasicInfo($basicInfo)
    {
        $result = array(
            'isBasicSuccessful'         => false,
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
        );
        $result['isBasicSuccessful']    = !$result['isUsernameEmpty']        &&  $result['isUsernameLegal'] &&
                                          !$result['isUsernameExists']       && !$result['isEmailEmpty']    && 
                                           $result['isEmailLegal']           && !$result['isEmailExists']   &&
                                          !$result['isPasswordEmpty']        &&  $result['isPasswordLegal'] &&
                                          !$result['isConfirmPasswordEmpty'] &&  $result['isConfirmPasswordMatched'];
        return $result;
    }

    /**
     * Verify if the username is legal.
     * Rule: the username should start with a character, and the length of
     *       it should no less than 6 characters and no more than 16 characters.
     *       It should be conbined with characters, numbers and underlines.
     * 
     * @param  String  $username - the username of the user
     * @return true if the username is legal
     */
    private function isUsernameLegal($username)
    {
        return (bool)preg_match('/^[a-z][0-9a-z_]{5,15}$/i', $username);
    }

    /**
     * Verify if the username has existed.
     * @param  String  $username - the username of the user
     * @return true if the username has existed
     */
    private function isUsernameExists($username)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        return $userTable->isUsernameExists($username);
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
     * @param  String  $email - the email address of the user
     * @return true if the email has existed
     */
    private function isEmailExists($email)
    {
        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        return $userTable->isEmailExists($email);
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
     * Get the unique id of the user group by its slug.
     * @param  String $userGroupSlug - the unique slug of the user group
     * @return the unique id of the user group
     */
    private function getUserGroupID($userGroupSlug)
    {
        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroupData      = $userGroupTable->getUserGroupID($userGroupSlug);

        return $userGroupData->user_group_id;
    }

    /**
     * Handle asynchronous register requests for a person.
     * @param  int $uid - the unique id of the user
     * @param  Array $personInfoArray - an array which contains essential 
     *         information of a person
     * @return true if the query is successful
     */
    private function processPersonAction($uid, $personInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $personTable        = $sm->get('Accounts\Model\PersonTable');

        $personInfo         = array(
            'uid'           => $uid,
            'real_name'     => $personInfoArray['realName'],
            'phone'         => $personInfoArray['phone'],
        );

        return $personTable->createNewPerson($personInfo);
    }

    /**
     * Get essential information of a person within an array.
     * @return an array which contains essential information of a person
     */
    private function getPersonInfoArray()
    {
        $realName           = $this->getRequest()->getPost('real-name');
        $phone              = $this->getRequest()->getPost('phone');

        return array(
            'realName'      => $realName,
            'phone'         => $phone,
        );
    }

    /**
     * Verify if the information of the person is legal.
     * @param  Array $personInfo - an array which contains information of the 
     *         person
     * @return an array which contains query result
     */
    private function verifyPersonInfo($personInfo)
    {
        $result = array(
            'isDetailSuccessful'        => false,
            'isRealNameEmpty'           => empty($personInfo['realName']),
            'isRealNameLegal'           => $this->isRealNameLegal($personInfo['realName']),
            'isPhoneNumberEmpty'        => empty($personInfo['phone']),
            'isPhoneNumberLegal'        => $this->isPhoneNumberLegal($personInfo['phone']),
        );
        $result['isDetailSuccessful']   = !$result['isRealNameEmpty']    && $result['isRealNameLegal'] &&
                                          !$result['isPhoneNumberEmpty'] && $result['isPhoneNumberLegal'];
        return $result;
    }

    /**
     * Verify if the real name of the user is legal.
     * Rule: the max length of the real name should less than 32 characters.
     * 
     * @param  String  $realName - the real name of the user
     * @return true if the real name of the user is legal
     */
    private function isRealNameLegal($realName)
    {
        $MAX_LENGTH_OF_REAL_NAME    = 32;
        return ( strlen($realName) <= $MAX_LENGTH_OF_REAL_NAME );
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
     * Handle asynchronous register requests for a teacher.
     * @param  int $uid - the unique id of the user
     * @param  Array $teacherInfoArray - an array which contains essential 
     *         information of a teacher
     * @return true if the query is successful
     */
    private function processTeacherAction($uid, $teacherInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $teacherTable       = $sm->get('Accounts\Model\TeacherTable');

        $teacherInfo        = array(
            'uid'           => $uid,
            'real_name'     => $teacherInfoArray['realName'],
            'company'       => $teacherInfoArray['company'],
            'field'         => str_replace('；', ';', $teacherInfoArray['field']),
            'phone'         => $teacherInfoArray['phone'],
        );

        return $teacherTable->createNewTeacher($teacherInfo);
    }

    /**
     * Get essential information of a teacher within an array.
     * @return an array which contains essential information of a teacher
     */
    private function getTeacherInfoArray()
    {
        $realName           = $this->getRequest()->getPost('real-name');
        $company            = $this->getRequest()->getPost('company');
        $field              = $this->getRequest()->getPost('field');
        $phone              = $this->getRequest()->getPost('phone');

        return array(
            'realName'      => $realName,
            'company'       => $company,
            'field'         => $field,
            'phone'         => $phone,
        );
    }

    /**
     * Verify if the information of the teacher is legal.
     * @param  Array $teacherInfo - an array which contains information of the 
     *         teacher
     * @return an array which contains query result
     */
    private function verifyTeacherInfo($teacherInfo)
    {
        $result = array(
            'isDetailSuccessful'        => false,
            'isRealNameEmpty'           => empty($teacherInfo['realName']),
            'isRealNameLegal'           => $this->isRealNameLegal($teacherInfo['realName']),
            'isCompanyEmpty'            => empty($teacherInfo['company']),
            'isCompanyLegal'            => $this->isCompanyLegal($teacherInfo['company']),
            'isFieldEmpty'              => empty($teacherInfo['field']),
            'isFieldLegal'              => $this->isFieldLegal($teacherInfo['field']),
            'isPhoneNumberEmpty'        => empty($teacherInfo['phone']),
            'isPhoneNumberLegal'        => $this->isPhoneNumberLegal($teacherInfo['phone']),
        );
        $result['isDetailSuccessful']   = !$result['isRealNameEmpty']    && $result['isRealNameLegal'] &&
                                          !$result['isCompanyEmpty']     && $result['isCompanyLegal'] &&
                                          !$result['isFieldEmpty']       && $result['isFieldLegal'] &&
                                          !$result['isPhoneNumberEmpty'] && $result['isPhoneNumberLegal'];
        return $result;
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

    /**
     * Handle asynchronous register requests for a teacher.
     * @param  int $uid - the unique id of the user
     * @param  Array $teacherInfoArray - an array which contains essential 
     *         information of a teacher
     * @return true if the query is successful
     */
    private function processEnterpriseAction($uid, $enterpriseInfoArray)
    {
        $sm                 = $this->getServiceLocator();
        $enterpriseTable    = $sm->get('Accounts\Model\EnterpriseTable');

        $enterpriseInfo     = array(
            'uid'           => $uid,
            'company_name'  => $enterpriseInfoArray['companyName'],
            'address'       => $enterpriseInfoArray['address'],
            'phone'         => $enterpriseInfoArray['phone'],
        );

        return $enterpriseTable->createNewEnterprise($enterpriseInfo);
    }

    /**
     * Get essential information of an enterprise within an array.
     * @return an array which contains essential information of an enterprise
     */
    private function getEnterpriseInfoArray()
    {
        $companyName        = $this->getRequest()->getPost('company-name');
        $address            = $this->getRequest()->getPost('address');
        $phone              = $this->getRequest()->getPost('phone');

        return array(
            'companyName'   => $companyName,
            'address'       => $address,
            'phone'         => $phone,
        );
    }

    /**
     * Verify if the information of the enterprise is legal.
     * @param  Array $teacherInfo - an array which contains information of the 
     *         enterprise
     * @return an array which contains query result
     */
    private function verifyEnterpriseInfo($enterpriseInfo)
    {
        $result = array(
            'isDetailSuccessful'        => false,
            'isCompanyEmpty'            => empty($enterpriseInfo['companyName']),
            'isCompanyLegal'            => $this->isCompanyLegal($enterpriseInfo['companyName']),
            'isAddressEmpty'            => empty($enterpriseInfo['address']),
            'isAddressLegal'            => $this->isAddressLegal($enterpriseInfo['address']),
            'isPhoneNumberEmpty'        => empty($enterpriseInfo['phone']),
            'isPhoneNumberLegal'        => $this->isPhoneNumberLegal($enterpriseInfo['phone']),
        );
        $result['isDetailSuccessful']   = !$result['isCompanyEmpty']     && $result['isCompanyLegal'] &&
                                          !$result['isAddressEmpty']     && $result['isAddressLegal'] &&
                                          !$result['isPhoneNumberEmpty'] && $result['isPhoneNumberLegal'];
        return $result;
    }

    /**
     * Verify if the address of the company is legal.
     * Rule: the length of the address should no more than 256
     *       characters.
     * 
     * @param  String  $address - the address of the company
     * @return true if the address of the company is legal
     */
    private function isAddressLegal($address)
    {
        $MAX_LENGTH_OF_ADDRESS      = 256;
        return ( strlen($address) < $MAX_LENGTH_OF_ADDRESS );
    }
}
