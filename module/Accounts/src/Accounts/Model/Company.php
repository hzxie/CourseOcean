<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_company table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Company {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->uid              = (!empty($data['uid']))                ? $data['uid']              : null;
        $this->company_name     = (!empty($data['company_name']))       ? $data['company_name']     : null;
        $this->company_city     = (!empty($data['company_city']))       ? $data['company_city']     : null;
        $this->company_address  = (!empty($data['company_address']))    ? $data['company_address']  : null;
        $this->company_field    = (!empty($data['company_field']))      ? $data['company_field']    : null;
        $this->company_scale    = (!empty($data['company_scale']))      ? $data['company_scale']    : null;
        $this->company_phone    = (!empty($data['company_phone']))      ? $data['company_phone']    : null;
    }

    /**
     * The unique id of the user.
     * @var int 
     */
    public $uid;

    /**
     * The name of the company.
     * @var String
     */
    public $company_name;

    /**
     * The city in China where the company locate.
     * @var String
     */
    public $company_city;

    /**
     * The address of the company.
     * @var String
     */
    public $company_address;

    /**
     * The 
     * @var String
     */
    public $company_field;

    /**
     * The scale of the company.
     * @var String
     */
    public $company_scale; 

    /**
     * The phone number of the company.
     * @var String
     */
    public $company_phone;
}