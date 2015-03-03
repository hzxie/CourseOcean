<?php

namespace CourseOcean\Model;

/**
 * 企业用户类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
        $this->uid          = (!empty($data['uid']))                ? $data['uid']                  : null;
        $this->isApproved   = (!empty($data['is_approved']))        ? $data['is_approved']          : null;
        $this->companyName  = (!empty($data['company_name']))       ? $data['company_name']         : null;
        $this->region       = (!empty($data['company_region']))     ? $data['company_region']       : null;
        $this->province     = (!empty($data['company_province']))   ? $data['company_province']     : null;
        $this->city         = (!empty($data['company_city']))       ? $data['company_city']         : null;
        $this->address      = (!empty($data['company_address']))    ? $data['company_address']      : null;
        $this->fieldId      = (!empty($data['company_field_id']))   ? $data['company_field_id']     : null;
        $this->fieldSlug    = (!empty($data['company_field_slug'])) ? $data['company_field_slug']   : null;
        $this->fieldName    = (!empty($data['company_field_name'])) ? $data['company_field_name']   : null;
        $this->scale        = (!empty($data['company_scale']))      ? $data['company_scale']        : null;
        $this->phone        = (!empty($data['company_phone']))      ? $data['company_phone']        : null;
    }

    /**
     * 用户的唯一标识符.
     * @var int 
     */
    public $uid;

    /**
     * 企业的资料是否通过审核
     * @var bool
     */
    public $isApproved;

    /**
     * 企业用户的公司名称.
     * @var String
     */
    public $companyName;

    /**
     * 企业用户的所在地区.
     * @var String
     */
    public $region;

    /**
     * 企业用户的所在省份.
     * @var String
     */
    public $province;

    /**
     * 企业用户的所在城市.
     * @var String
     */
    public $city;

    /**
     * 企业用户的公司地址.
     * @var String
     */
    public $address;

    /**
     * 企业用户经营领域的唯一标识符.
     * @var int
     */
    public $fieldId;

    /**
     * 企业用户经营领域的唯一英文缩写.
     * @var String
     */
    public $positionSlug;

    /**
     * 企业用户经营领域的名称.
     * @var String
     */
    public $positionName;

    /**
     * 企业用户的公司规模.
     * @var int
     */
    public $scale;

    /**
     * 企业用户的联系电话.
     * @var String
     */
    public $phone;
}