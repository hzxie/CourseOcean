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
        $this->uid                  = (!empty($data['uid']))                    ? $data['uid']                  : null;
        $this->teacherName          = (!empty($data['teacher_name']))           ? $data['teacher_name']         : null;
        $this->teacherIsApproved    = (!empty($data['teacher_is_approved']))    ? $data['teacher_is_approved']  : null;
        $this->teacherBrief         = (!empty($data['teacher_brief']))          ? $data['teacher_brief']        : null;
        $this->teacherAvatar        = (!empty($data['teacher_avatar']))         ? $data['teacher_avatar']       : null;
        $this->teachingField        = (!empty($data['teaching_field']))         ? $data['teaching_field']       : null;
        $this->teacherRegion        = (!empty($data['teacher_region']))         ? $data['teacher_region']       : null;
        $this->teacherProvince      = (!empty($data['teacher_province']))       ? $data['teacher_province']     : null;
        $this->teacherCity          = (!empty($data['teacher_city']))           ? $data['teacher_city']         : null;
        $this->teacherCompany       = (!empty($data['teacher_company']))        ? $data['teacher_company']      : null;
        $this->teacherPhone         = (!empty($data['teacher_phone']))          ? $data['teacher_phone']        : null;
        $this->teacherWeibo         = (!empty($data['teacher_weibo']))          ? $data['teacher_weibo']        : null;
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
    public $teacherIsApproved;

    /**
     * 讲师的简介.
     * @var String
     */
    public $teacherBrief;

    /**
     * 讲师头像的所在路径.
     * @var String
     */
    public $teacherAvatar;
    
    /**
     * 讲师的授课领域
     * @var String
     */
    public $teachingField;

    /**
     * 讲师所在地区.
     * @var String
     */
    public $teacherRegion;

    /**
     * 讲师所在省份.
     * @var String
     */
    public $teacherProvince;

    /**
     * 讲师所在城市.
     * @var String
     */
    public $teacherCity;

    /**
     * 讲师现所在(或曾经就职)的公司.
     * @var String
     */
    public $teacherCompany;

    /**
     * 讲师的联系电话.
     * @var String
     */
    public $teacherPhone;

    /**
     * 讲师的新浪微博账号.
     * @var String
     */
    public $teacherWeibo;
}