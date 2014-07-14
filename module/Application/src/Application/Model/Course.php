<?php

namespace Application\Model;

/**
 * 课程类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class Course {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->courseId         = (!empty($data['course_id']))          ? $data['course_id']        : null;
        $this->courseName       = (!empty($data['course_name']))        ? $data['course_name']      : null;
        $this->courseIsPublic   = (!empty($data['course_is_public']))   ? $data['course_is_public'] : null;
        $this->courseTypeId     = (!empty($data['course_type_id']))     ? $data['course_type_id']   : null;
        $this->courseTypeSlug   = (!empty($data['course_type_slug']))   ? $data['course_type_slug'] : null;
        $this->courseTypeName   = (!empty($data['course_type_name']))   ? $data['course_type_name'] : null;
        $this->teacherId        = (!empty($data['teacher_id']))         ? $data['teacher_id']       : null;
        $this->teacherName      = (!empty($data['teacher_name']))       ? $data['teacher_name']     : null;
        $this->courseCycle      = (!empty($data['course_cycle']))       ? $data['course_cycle']     : null;
        $this->courseBrief      = (!empty($data['course_brief']))       ? $data['course_brief']     : null;
        $this->courseObjective  = (!empty($data['course_objective']))   ? $data['course_objective'] : null;
    }

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
    public $courseIsPublic;

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
     * 课程周期(天数)
     * @var int
     */
    public $courseCycle;

    /**
     * 课程概述.
     * @var String
     */
    public $courseBrief;

    /**
     * 课程目标.
     * @var String
     */
    public $courseObjective;
}