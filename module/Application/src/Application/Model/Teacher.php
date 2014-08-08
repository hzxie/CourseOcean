<?php

namespace Application\Model;

/**
 * 讲师用户的Model.
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
        $this->uid           = (!empty($data['uid']))                    ? $data['uid']                  : null;
        $this->teacherName   = (!empty($data['teacher_name']))           ? $data['teacher_name']         : null;
        $this->isApproved    = (!empty($data['teacher_is_approved']))    ? $data['teacher_is_approved']  : null;
        $this->brief         = (!empty($data['teacher_brief']))          ? $data['teacher_brief']        : null;
        $this->avatar        = (!empty($data['teacher_avatar']))         ? $data['teacher_avatar']       : null;
        $this->teachingField = (!empty($data['teaching_field']))         ? $data['teaching_field']       : null;
        $this->region        = (!empty($data['teacher_region']))         ? $data['teacher_region']       : null;
        $this->province      = (!empty($data['teacher_province']))       ? $data['teacher_province']     : null;
        $this->city          = (!empty($data['teacher_city']))           ? $data['teacher_city']         : null;
        $this->company       = (!empty($data['teacher_company']))        ? $data['teacher_company']      : null;
        $this->phone         = (!empty($data['teacher_phone']))          ? $data['teacher_phone']        : null;
        $this->weibo         = (!empty($data['teacher_weibo']))          ? $data['teacher_weibo']        : null;
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
    public $teacherName;

    /**
     * 讲师的资料是否通过审核
     * @var bool
     */
    public $isApproved;

    /**
     * 讲师的简介.
     * @var String
     */
    public $brief;

    /**
     * 讲师头像的所在路径.
     * @var String
     */
    public $avatar;
    
    /**
     * 讲师的授课领域
     * @var String
     */
    public $teachingField;

    /**
     * 讲师所在地区.
     * @var String
     */
    public $region;

    /**
     * 讲师所在省份.
     * @var String
     */
    public $province;

    /**
     * 讲师所在城市.
     * @var String
     */
    public $city;

    /**
     * 讲师现所在(或曾经就职)的公司.
     * @var String
     */
    public $company;

    /**
     * 讲师的联系电话.
     * @var String
     */
    public $phone;

    /**
     * 讲师的新浪微博账号.
     * @var String
     */
    public $weibo;
}