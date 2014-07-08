<?php

namespace Solutions\Model;

/**
 * 课程模块类的Model.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Course {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->course_id            = (!empty($data['course_id']))          ? $data['course_id']            : null;
        $this->course_name          = (!empty($data['course_name']))        ? $data['course_name']          : null;
        $this->teacher_id           = (!empty($data['teacher_id']))         ? $data['teacher_id']           : null;
        $this->course_cycle         = (!empty($data['course_cycle']))       ? $data['course_cycle']         : null;
        $this->course_brief         = (!empty($data['course_brief']))       ? $data['course_brief']         : null;
        $this->course_objective     = (!empty($data['course_objective']))   ? $data['course_objective']     : null;
        $this->course_outline       = (!empty($data['course_outline']))     ? $data['course_outline']       : null;
        $this->course_start_time    = (!empty($data['course_start_time']))  ? $data['course_start_time']    : null;
        $this->course_end_time      = (!empty($data['course_end_time']))    ? $data['course_end_time']      : null;
    }

    /**
     * 课程模块的唯一标识符.
     * @var int
     */
    public $course_id;

    /**
     * 课程模块的名称.
     * @var String
     */
    public $course_name;

    /**
     * 讲师的用户唯一标识符.
     * @var int
     */
    public $teacher_id;

    /**
     * 课程模块的授课时间(分钟)
     * @var int
     */
    public $course_cycle;

    /**
     * 课程模块概述.
     * @var String
     */
    public $course_brief;

    /**
     * 课程模块的目标.
     * @var String
     */
    public $course_objective;

    /**
     * 课程模块的大纲.
     * @var String
     */
    public $course_outline;

    /**
     * 课程开始时间.
     * @var DateTime
     */
    public $course_start_time;

    /**
     * 课程结束时间.
     * @var DateTime
     */
    public $course_end_time;
}