<?php

namespace Accounts\Model;

/**
 * 个人用户的Model.
 * 与数据库中的itp_people表关联.
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
        $this->uid                  = (!empty($data['uid']))                    ? $data['uid']                  : null;
        $this->person_name          = (!empty($data['person_name']))            ? $data['person_name']          : null;
        $this->person_region        = (!empty($data['person_region']))          ? $data['person_region']        : null;
        $this->person_province      = (!empty($data['person_province']))        ? $data['person_province']      : null;
        $this->person_city          = (!empty($data['person_city']))            ? $data['person_city']          : null;
        $this->person_position_id   = (!empty($data['person_position_id']))     ? $data['person_position_id']   : null;
        $this->person_position_name = (!empty($data['position_name']))          ? $data['position_name']        : null;
        $this->person_phone         = (!empty($data['person_phone']))           ? $data['person_phone']         : null;
    }

    /**
     * 用户的唯一标识符(uid).
     * @var int 
     */
    public $uid;

    /**
     * 用户的真实姓名.
     * @var String
     */
    public $person_name;

    /**
     * 用户所在地区.
     * @var String
     */
    public $person_region;

    /**
     * 用户所在省份.
     * @var String
     */
    public $person_province;

    /**
     * 用户所在城市.
     * @var String
     */
    public $person_city;

    /**
     * 用户工作职位的唯一标识符.
     * @var int
     */
    public $person_position_id;

    /**
     * 用户工作职位的名称.
     * @var String
     */
    public $person_position_name;

    /**
     * 用户的联系方式.
     * @var String
     */
    public $person_phone;
}