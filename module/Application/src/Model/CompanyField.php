<?php

namespace Application\Model;

/**
 * 公司领域的Model.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class CompanyField {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data) {
        $this->companyFieldId       = (!empty($data['company_field_id']))      ? $data['company_field_id']    : null;
        $this->companyFieldSlug     = (!empty($data['company_field_slug']))    ? $data['company_field_slug']  : null;
        $this->companyFieldName     = (!empty($data['company_field_name']))    ? $data['company_field_name']  : null;
    }

    /**
     * 用户组的唯一标识符.
     * @var int
     */
    public $companyFieldId;

    /**
     * 用户组的唯一英文缩写.
     * @var String
     */
    public $companyFieldSlug;

    /**
     * 用户组的名称.
     * @var String
     */
    public $companyFieldName;
}