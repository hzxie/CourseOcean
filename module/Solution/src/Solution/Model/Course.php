<?php

namespace Solution\Model;

/**
 * The class is the model of the itp_courses table.
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
        $this->course_type_id       = (!empty($data['course_type_id']))     ? $data['course_type_id']       : null;
        $this->course_type_slug     = (!empty($data['course_type_slug']))   ? $data['course_type_slug']     : null;
        $this->course_type_name     = (!empty($data['course_type_name']))   ? $data['course_type_name']     : null;
        $this->uid                  = (!empty($data['uid']))                ? $data['uid']                  : null;
        $this->teacher_name         = (!empty($data['teacher_name']))       ? $data['teacher_name']         : null;
        $this->course_create_time   = (!empty($data['course_create_time'])) ? $data['course_create_time']   : null;
        $this->course_brief         = (!empty($data['course_brief']))       ? $data['course_brief']         : null;
        $this->course_cycle         = (!empty($data['course_cycle']))       ? $data['course_cycle']         : null;
        $this->course_audience      = (!empty($data['course_audience']))    ? $data['course_audience']      : null;
    }

    /**
     * The unique id of the course.
     * @var int
     */
    public $course_id;

    /**
     * The name of the course.
     * @var String
     */
    public $course_name;

    /**
     * The unique id of the course type.
     * @var int
     */
    public $course_type_id;

    /**
     * The slug name of the course type.
     * @var String
     */
    public $course_type_slug;

    /**
     * The name of the course type.
     * @var String
     */
    public $course_type_name;

    /**
     * The unique id of the teacher.
     * @var int
     */
    public $uid;

    /**
     * The real name of the teacher.
     * @var String
     */
    public $teacher_name;

    /**
     * The time when the course has been created.
     * @var TimeStamp
     */
    public $course_create_time;

    /**
     * The brief introduction of the course.
     * @var String
     */
    public $course_brief;

    /**
     * The time required of the course.
     * @var int
     */
    public $course_cycle;

    /**
     * The audience who are suitable for the course.
     * @var String
     */
    public $course_audience;
}