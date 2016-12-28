<?php

namespace CourseOcean\Model;

/**
 * 课程会话类的Model.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class Lecture {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->lectureId        = (!empty($data['lecture_id']))             ? $data['lecture_id']           : null;
        $this->createTime       = (!empty($data['lecture_create_time']))    ? $data['lecture_create_time']  : null;
        $this->courseId         = (!empty($data['course_id']))              ? $data['course_id']            : null;
        $this->courseName       = (!empty($data['course_name']))            ? $data['course_name']          : null;
        $this->isPublic         = (!empty($data['course_is_public']))       ? $data['course_is_public']     : null;
        $this->courseTypeId     = (!empty($data['course_type_id']))         ? $data['course_type_id']       : null;
        $this->courseTypeSlug   = (!empty($data['course_type_slug']))       ? $data['course_type_slug']     : null;
        $this->courseTypeName   = (!empty($data['course_type_name']))       ? $data['course_type_name']     : null;
        $this->teacherId        = (!empty($data['teacher_id']))             ? $data['teacher_id']           : null;
        $this->teacherName      = (!empty($data['teacher_name']))           ? $data['teacher_name']         : null;
        $this->startTime        = (!empty($data['lecture_start_time']))     ? $data['lecture_start_time']   : null;
        $this->endTime          = (!empty($data['lecture_end_time']))       ? $data['lecture_end_time']     : null;
        $this->lectureRegion    = (!empty($data['lecture_region']))         ? $data['lecture_region']       : null;
        $this->lectureProvince  = (!empty($data['lecture_province']))       ? $data['lecture_province']     : null;
        $this->lectureCity      = (!empty($data['lecture_city']))           ? $data['lecture_city']         : null;
        $this->lectureAddress   = (!empty($data['lecture_address']))        ? $data['lecture_address']      : null;
        $this->minCapcity       = (!empty($data['lecture_min_capcity']))    ? $data['lecture_min_capcity']  : null;
        $this->maxCapcity       = (!empty($data['lecture_max_capcity']))    ? $data['lecture_max_capcity']  : null;
        $this->participants     = (!empty($data['participants']))           ? $data['participants']         : null;
        $this->expense          = (!empty($data['lecture_expense']))        ? $data['lecture_expense']      : null;
        $this->audience         = (!empty($data['course_audience']))        ? $data['course_audience']      : null;
        $this->brief            = (!empty($data['course_brief']))           ? $data['course_brief']         : null;
        $this->objective        = (!empty($data['course_objective']))       ? $data['course_objective']     : null;
        $this->precautions      = (!empty($data['lecture_precautions']))    ? $data['lecture_precautions']  : null;
    }

    /**
     * 课程会话的唯一标识符.
     * @var int
     */
    public $lectureId;

    /**
     * 课程会话的创建时间.
     * @var DateTime
     */
    public $createTime;

    /**
     * 课程的唯一标识符.
     * @var int 
     */
    public $courseId;

    /**
     * 课程名称.
     * @var String
     */
    public $courseName;

    /**
     * 课程是否为公开课.
     * @var String
     */
    public $isPublic;

    /**
     * 课程类别的唯一标识符.
     * @var int
     */
    public $courseTypeId;

    /**
     * 课程类别的唯一英文简写.
     * @var String
     */
    public $courseTypeSlug;

    /**
     * 课程类别的名称.
     * @var String
     */
    public $courseTypeName;

    /**
     * 讲师用户唯一标识符
     * @var int
     */
    public $teacherId;

    /**
     * 讲师姓名.
     * @var String
     */
    public $teacherName;

    /**
     * 课程会话开始时间.
     * @var DateTime
     */
    public $startTime;

    /**
     * 课程会话结束时间.
     * @var DateTime
     */
    public $endTime;

    /**
     * 课程会话的开课地区.
     * @var String
     */
    public $lectureRegion;

    /**
     * 课程会话的开课省份.
     * @var String
     */
    public $lectureProvince;

    /**
     * 课程会话的开课城市.
     * @var String
     */
    public $lectureCity;

    /**
     * 课程会话的开课地址.
     * @var String
     */
    public $lectureAddress;

    /**
     * 课程会话的最小开课人数.
     * @var int
     */
    public $minCapcity;

    /**
     * 课程会话的最大开课人数.
     * @var int
     */
    public $maxCapcity;

    /**
     * 课程会话已报名人数.
     * @var int
     */
    public $participants;

    /**
     * 参加课程的费用.
     * @var int
     */
    public $expense;

    /**
     * 课程培训对象.
     * @var String
     */
    public $audience;

    /**
     * 课程概述.
     * @var String
     */
    public $brief;

    /**
     * 课程目标.
     * @var String
     */
    public $objective;

    /**
     * 课程注意事项.
     * @var String
     */
    public $precautions;
}