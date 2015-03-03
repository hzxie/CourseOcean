<?php

namespace CourseOcean\Model;

/**
 * 电子邮件验证类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
        $this->email     = (!empty($data['email']))      ? $data['email']    : null;
        $this->keycode   = (!empty($data['keycode']))    ? $data['keycode']  : null;
    }

    /**
     * 电子邮件地址.
     * @var String
     */
    public $email;

    /**
     * 电子邮件验证凭据.
     * @var String
     */
    public $keycode;
}