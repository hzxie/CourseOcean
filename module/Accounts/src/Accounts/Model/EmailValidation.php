<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_email_validation table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class EmailValidation {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->email    = (!empty($data['email']))  ? $data['email']    : null;
        $this->guid     = (!empty($data['guid']))   ? $data['guid']     : null;
    }

    /**
     * The email address of the user.
     * @var String
     */
    public $email;

    /**
     * Activation code for resetting password or activate account.
     * @var String
     */
    public $activation_code;
}