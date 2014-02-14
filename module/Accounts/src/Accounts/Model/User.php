<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_users table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
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
        $this->uid                          = (!empty($data['uid']))                        ? $data['uid']                          : null;
        $this->username                     = (!empty($data['username']))                   ? $data['username']                     : null;
        $this->email                        = (!empty($data['email']))                      ? $data['email']                        : null;
        $this->password                     = (!empty($data['password']))                   ? $data['password']                     : null;
        $this->is_activated                 = (!empty($data['is_activated']))               ? $data['is_activated']                 : null;
        $this->user_group_id                = (!empty($data['user_group_id']))              ? $data['user_group_id']                : null;
        $this->last_time_signin             = (!empty($data['last_time_signin']))           ? $data['last_time_signin']             : null;
        $this->last_time_change_password    = (!empty($data['last_time_change_password']))  ? $data['last_time_change_password']    : null;
    }


    /**
     * The unique id of the user.
     * @var int 
     */
    public $uid;

    /**
     * The unique name of the user.
     * @var String
     */
    public $username;

    /**
     * The unique address of the user.
     * @var String
     */
    public $email;

    /**
     * The encrypted password of the user.
     * @var String
     */
    public $password;

    /**
     * The flag whether the user has verified email.
     * @var bool
     */
    public $is_activated;

    /**
     * The id of the user group which the user belongs 
     * to.
     * @var int
     */
    public $user_group_id;

    /**
     * The time when the user signed in last time.
     * @var TimeStamp
     */
    public $last_time_signin;

    /**
     * The time when the user changed password last time.
     * @var TimeStamp
     */
    public $last_time_change_password;
}