<?php

namespace Solutions\Model;

/**
 * The class is the model of the itp_course_types table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class CourseType {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->course_type_id       = (!empty($data['course_type_id']))     ? $data['course_type_id']       : null;
        $this->course_type_slug     = (!empty($data['course_type_slug']))   ? $data['course_type_slug']     : null;
        $this->course_type_name     = (!empty($data['course_type_name']))   ? $data['course_type_name']     : null;
        $this->course_type_parent   = (!empty($data['course_type_parent'])) ? $data['course_type_parent']   : null;
    }

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
     * The name to display of the course type.
     * @var String
     */
    public $course_type_name;

    /**
     * The type id of its parent of the course type.
     * @var id
     */
    public $course_type_parent;
}