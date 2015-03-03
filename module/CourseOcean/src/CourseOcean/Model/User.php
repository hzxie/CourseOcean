<?php

namespace CourseOcean\Model;

/**
 * 用户类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class User {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->uid                  = (!empty($data['uid']))                    ? $data['uid']                      : null;
        $this->username             = (!empty($data['username']))               ? $data['username']                 : null;
        $this->email                = (!empty($data['email']))                  ? $data['email']                    : null;
        $this->password             = (!empty($data['password']))               ? $data['password']                 : null;
        $this->isInspected          = (!empty($data['is_inspected']))           ? $data['is_inspected']             : null;
        $this->isApproved           = (!empty($data['is_approved']))            ? $data['is_approved']              : null;
        $this->isEmailValidated     = (!empty($data['is_email_validated']))     ? $data['is_email_validated']       : null;
        $this->userGroupId          = (!empty($data['user_group_id']))          ? $data['user_group_id']            : null;
        $this->userGroupSlug        = (!empty($data['user_group_slug']))        ? $data['user_group_slug']          : null;
        $this->userGroupName        = (!empty($data['user_group_name']))        ? $data['user_group_name']          : null;
    }


    /**
     * 用户的唯一标识符.
     * @var int 
     */
    public $uid;

    /**
     * 用户名.
     * @var String
     */
    public $username;

    /**
     * 电子邮件地址.
     * @var String
     */
    public $email;

    /**
     * 密码.
     * @var String
     */
    public $password;

    
    /**
     * 账号是否被审核.
     * @var Boolean
     */
    public $isInspected;

    /**
     * 账号是否已通过审核.
     * @var Boolean
     */
    public $isApproved;

    /**
     * 电子邮件地址是否验证.
     * @var Boolean
     */
    public $isEmailValidated;

    /**
     * 用户组的唯一标识符.
     * @var int
     */
    public $userGroupId;

    /**
     * 用户组的唯一英文缩写.
     * @var String
     */
    public $userGroupSlug;

    /**
     * 用户组的名称.
     * @var String
     */
    public $userGroupName;
}