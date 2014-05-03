<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_teaching_field table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class TeachingField {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->uid              = (!empty($data['uid']))                ? $data['uid']              : null;
        $this->teacher_name     = (!empty($data['teacher_name']))       ? $data['teacher_name']     : null;
        $this->teacher_company  = (!empty($data['teacher_company']))    ? $data['teacher_company']  : null;
        $this->course_type_id   = (!empty($data['course_type_id']))     ? $data['course_type_id']   : null;
        $this->course_type_slug = (!empty($data['course_type_slug']))   ? $data['course_type_slug'] : null;
        $this->course_type_name = (!empty($data['course_type_name']))   ? $data['course_type_name'] : null;
    }


    /**
     * The unique id of the teacher.
     * @var int 
     */
    public $uid;

    /**
     * The name of the teacher.
     * @var String
     */
    public $teacher_name;

    /**
     * The company where the teacher working.
     * @var String
     */
    public $teacher_company;

    /**
     * The unique id of the teaching field(course type).
     * @var int
     */
    public $course_type_id;

    /**
     * The unique slug of the teaching field(course type).
     * @var String
     */
    public $course_type_slug;

    /**
     * The display name of the teaching field(course type).
     * @var String
     */
    public $course_type_name;
}