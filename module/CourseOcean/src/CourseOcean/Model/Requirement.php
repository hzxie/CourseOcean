<?php

namespace CourseOcean\Model;

/**
 * 培训需求类的Model.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class Requirement {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->requirementId    = (!empty($data['requirement_id']))             ? $data['requirement_id']               : null;
        $this->isAccepted       = (!empty($data['requirement_is_accepted']))    ? $data['requirement_is_accepted']      : null;
        $this->fromUid          = (!empty($data['requirement_from_uid']))       ? $data['requirement_from_uid']         : null;
        $this->toUid            = (!empty($data['requirement_to_uid']))         ? $data['requirement_to_uid']           : null;
        $this->courseId         = (!empty($data['requirement_course_id']))      ? $data['requirement_course_id']        : null;
        $this->courseName       = (!empty($data['course_name']))                ? $data['course_name']                  : null;
        $this->courseTypeSlug   = (!empty($data['course_type_slug']))           ? $data['course_type_slug']             : null;
        $this->companyName      = (!empty($data['company_name']))               ? $data['company_name']                 : null;
        $this->teacherName      = (!empty($data['teacher_name']))               ? $data['teacher_name']                 : null;
        $this->createTime       = (!empty($data['requirement_create_time']))    ? $data['requirement_create_time']      : null;
        $this->participants     = (!empty($data['requirement_participants']))   ? $data['requirement_participants']     : null;
        $this->startTime        = (!empty($data['requirement_start_time']))     ? $data['requirement_start_time']       : null;
        $this->endTime          = (!empty($data['requirement_end_time']))       ? $data['requirement_end_time']         : null;
        $this->region           = (!empty($data['requirement_region']))         ? $data['requirement_region']           : null;
        $this->province         = (!empty($data['requirement_province']))       ? $data['requirement_province']         : null;
        $this->city             = (!empty($data['requirement_city']))           ? $data['requirement_city']             : null;
        $this->address          = (!empty($data['requirement_address']))        ? $data['requirement_address']          : null;
        $this->expense          = (!empty($data['requirement_expense']))        ? $data['requirement_expense']          : null;
        $this->detail           = (!empty($data['requirement_detail']))         ? $data['requirement_detail']           : null;
    }

    /**
     * 培训需求的唯一标识符.
     * @var int 
     */
    public $requirementId;

    /**
     * 需求是否已被受理.
     * @var boolean
     */
    public $isAccepted;

    /**
     * 需求提出者的用户唯一标识符.
     * @var int
     */
    public $fromUid;

    /**
     * 需求接受者的用户唯一标识符.
     * @var int
     */
    public $toUid;

    /**
     * 课程的唯一标识符.
     * @var int
     */
    public $courseId;

    /**
     * 课程的名称.
     * @var String
     */
    public $courseName;

    /**
     * 课程分类的唯一英文缩写.
     * @var String
     */
    public $courseTypeSlug;

    /**
     * 讲师的姓名.
     * @var String
     */
    public $teacherName;

    /**
     * 需求的创建时间.
     * @var DateTime
     */
    public $createTime;

    /**
     * 根据需求所开设课程的参与人数.
     * @var int
     */
    public $participants;

    /**
     * 根据需求所开设课程的起始时间.
     * @var DateTime
     */
    public $startTime;

    /**
     * 根据需求所开设课程的结束时间.
     * @var DateTime
     */
    public $endTime;

    /**
     * 根据需求所开设课程的地区.
     * @var String
     */
    public $region;

    /**
     * 根据需求所开设课程的省份.
     * @var String
     */
    public $province;

    /**
     * 根据需求所开设课程的城市.
     * @var String
     */
    public $city;

    /**
     * 根据需求所开设课程的地址.
     * @var String
     */
    public $address;

    /**
     * 根据需求所开设课程的费用.
     * @var String
     */
    public $expense;

    /**
     * 需求的细节描述信息.
     * @var String
     */
    public $detail;
}