<?php

namespace Solutions\Model;

/**
 * 课程类的Model.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Lecture {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->lecture_id           = (!empty($data['lecture_id']))             ? $data['lecture_id']           : null;
        $this->lecture_name         = (!empty($data['lecture_name']))           ? $data['lecture_name']         : null;
        $this->course_type_id       = (!empty($data['course_type_id']))         ? $data['course_type_id']       : null;
        $this->course_type_slug     = (!empty($data['course_type_slug']))       ? $data['course_type_slug']     : null;
        $this->course_type_name     = (!empty($data['course_type_name']))       ? $data['course_type_name']     : null;
        $this->lecture_create_time  = (!empty($data['lecture_create_time']))    ? $data['lecture_create_time']  : null;
        $this->teacher_id           = (!empty($data['teacher_id']))             ? $data['teacher_id']           : null;
        $this->teacher_name         = (!empty($data['teacher_name']))           ? $data['teacher_name']         : null;
        $this->lecture_brief        = (!empty($data['lecture_brief']))          ? $data['lecture_brief']        : null;
        $this->lecture_start_time   = (!empty($data['lecture_start_time']))     ? $data['lecture_start_time']   : null;
        $this->lecture_end_time     = (!empty($data['lecture_end_time']))       ? $data['lecture_end_time']     : null;
        $this->lecture_region       = (!empty($data['lecture_region']))         ? $data['lecture_region']       : null;
        $this->lecture_province     = (!empty($data['lecture_province']))       ? $data['lecture_province']     : null;
        $this->lecture_city         = (!empty($data['lecture_city']))           ? $data['lecture_city']         : null;
        $this->lecture_address      = (!empty($data['lecture_address']))        ? $data['lecture_address']      : null;
        $this->lecture_expense      = (!empty($data['lecture_expense']))        ? $data['lecture_expense']      : null;
    }

    /**
     * 课程的唯一标识符.
     * @var int
     */
    public $lecture_id;

    /**
     * 课程的名称.
     * @var String
     */
    public $lecture_name;

    /**
     * 课程类型的唯一标识符.
     * @var int
     */
    public $course_type_id;

    /**
     * 课程类型的唯一英文简称.
     * @var String
     */
    public $course_type_slug;

    /**
     * 课程类型的名称.
     * @var String
     */
    public $course_type_name;

    /**
     * 课程的创建时间.
     * @var DateTime
     */
    public $lecture_create_time;

    /**
     * 讲师的用户唯一标识符.
     * @var int
     */
    public $teacher_id;

    /**
     * 讲师的真实姓名.
     * @var String
     */
    public $teacher_name;

    /**
     * 课程概述.
     * @var String
     */
    public $lecture_brief;

    /**
     * 课程的开始时间.
     * @var DateTime
     */
    public $lecture_start_time;

    /**
     * 课程的结束时间.
     * @var DateTime
     */
    public $lecture_end_time;

    /**
     * 课程的开课地区.
     * @var String
     */
    public $lecture_region;

    /**
     * 课程的开课省份.
     * @var String
     */
    public $lecture_province;

    /**
     * 课程的开课城市.
     * @var String
     */
    public $lecture_city;

    /**
     * 课程的开课详细地址.
     * @var String
     */
    public $lecture_address;

    /**
     * 课程的费用.
     * @var int
     */
    public $lecture_expense;
}