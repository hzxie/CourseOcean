<?php

namespace Accounts\Model;

/**
 * 讲师用户的Model.
 * 与数据库中的itp_teachers表关联.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class Teacher {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->uid                  = (!empty($data['uid']))                    ? $data['uid']                  : null;
        $this->teacher_name         = (!empty($data['teacher_name']))           ? $data['teacher_name']         : null;
        $this->teacher_is_approved  = (!empty($data['teacher_is_approved']))    ? $data['teacher_is_approved']  : null;
        $this->teacher_brief        = (!empty($data['teacher_brief']))          ? $data['teacher_brief']        : null;
        $this->teacher_field        = (!empty($data['teacher_field']))          ? $data['teacher_field']        : null;
        $this->teacher_avatar       = (!empty($data['teacher_avatar']))         ? $data['teacher_avatar']       : null;
        $this->teacher_region       = (!empty($data['teacher_region']))         ? $data['teacher_region']       : null;
        $this->teacher_province     = (!empty($data['teacher_province']))       ? $data['teacher_province']     : null;
        $this->teacher_city         = (!empty($data['teacher_city']))           ? $data['teacher_city']         : null;
        $this->teacher_company      = (!empty($data['teacher_company']))        ? $data['teacher_company']      : null;
        $this->teacher_phone        = (!empty($data['teacher_phone']))          ? $data['teacher_phone']        : null;
        $this->teacher_weibo        = (!empty($data['teacher_weibo']))          ? $data['teacher_weibo']        : null;
    }

    /**
     * 用户的唯一标识符(uid).
     * @var int
     */
    public $uid;

    /**
     * 讲师的真实姓名.
     * @var String
     */
    public $teacher_name;

    /**
     * 讲师的资料是否通过审核
     * @var bool
     */
    public $teacher_is_approved;

    /**
     * 讲师的简介.
     * @var String
     */
    public $teacher_brief;

    /**
     * 讲师的授课领域.
     * @var String
     */
    public $teacher_field;

    /**
     * 讲师头像的所在路径.
     * @var String
     */
    public $teacher_avatar;

    /**
     * 讲师所在地区.
     * @var String
     */
    public $teacher_region;

    /**
     * 讲师所在省份.
     * @var String
     */
    public $teacher_province;

    /**
     * 讲师所在城市.
     * @var String
     */
    public $teacher_city;

    /**
     * 讲师现所在(或曾经就职)的公司.
     * @var String
     */
    public $teacher_company;

    /**
     * 讲师的联系电话.
     * @var String
     */
    public $teacher_phone;

    /**
     * 讲师的新浪微博账号.
     * @var String
     */
    public $teacher_weibo;
}