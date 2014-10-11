<?php

namespace Application\Model;

/**
 * 个人用户类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class Person {
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
        $this->personName   = (!empty($data['person_name']))        ? $data['person_name']          : null;
        $this->region       = (!empty($data['person_region']))      ? $data['person_region']        : null;
        $this->province     = (!empty($data['person_province']))    ? $data['person_province']      : null;
        $this->city         = (!empty($data['person_city']))        ? $data['person_city']          : null;
        $this->company      = (!empty($data['person_company']))     ? $data['person_company']       : null;
        $this->positionId   = (!empty($data['person_position_id'])) ? $data['person_position_id']   : null;
        $this->positionSlug = (!empty($data['position_slug']))      ? $data['position_slug']        : null;
        $this->positionName = (!empty($data['position_name']))      ? $data['position_name']        : null;
        $this->phone        = (!empty($data['person_phone']))       ? $data['person_phone']         : null;
    }

    /**
     * 用户的唯一标识符.
     * @var int 
     */
    public $uid;

    /**
     * 个人用户的真实姓名.
     * @var String
     */
    public $personName;

    /**
     * 个人用户的所在地区.
     * @var String
     */
    public $region;

    /**
     * 个人用户的所在省份.
     * @var String
     */
    public $province;

    /**
     * 个人用户的所在城市.
     * @var String
     */
    public $city;

    /**
     * 个人用户的所在公司.
     * @var String
     */
    public $company;

    /**
     * 个人用户职位的唯一标识符.
     * @var int
     */
    public $positionId;

    /**
     * 个人用户职位的唯一英文缩写.
     * @var String
     */
    public $positionSlug;

    /**
     * 个人用户职位的名称.
     * @var String
     */
    public $positionName;

    /**
     * 个人用户的联系电话.
     * @var String
     */
    public $phone;
}