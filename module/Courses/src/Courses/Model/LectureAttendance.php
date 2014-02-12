<?php

namespace Courses\Model;

/**
 * The class is the model of the itp_lecture_attendance table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class LectureAttendance {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->lecture_id           = (!empty($data['lecture_id']))         ? $data['lecture_id']           : null;
        $this->uid                  = (!empty($data['uid']))                ? $data['uid']                  : null;
        $this->course_name          = (!empty($data['course_name']))        ? $data['course_name']          : null;
        $this->course_type_slug     = (!empty($data['course_type_slug']))   ? $data['course_type_slug']     : null;
        $this->lecture_start_time   = (!empty($data['lecture_start_time'])) ? $data['lecture_start_time']   : null;
        $this->lecture_end_time     = (!empty($data['lecture_end_time']))   ? $data['lecture_end_time']     : null;
        $this->teacher_name         = (!empty($data['teacher_name']))       ? $data['teacher_name']         : null;
        $this->lecture_province     = (!empty($data['lecture_province']))   ? $data['lecture_province']     : null;
        $this->lecture_city         = (!empty($data['lecture_city']))       ? $data['lecture_city']         : null;
        $this->lecture_address      = (!empty($data['lecture_address']))    ? $data['lecture_address']      : null;
    }

    /**
     * The unique id of the lecture.
     * @var int
     */
    public $lecture_id;

    /**
     * The unique id of the user.
     * @var int
     */
    public $uid;

    /**
     * The name of the course.
     * @var String
     */
    public $course_name;

    /**
     * The slug name of the course type.
     * @var String
     */
    public $course_type_slug;

    /**
     * The date when the lecture start.
     * @var Date
     */
    public $lecture_start_time;

    /**
     * The date when the lecture end.
     * @var Date
     */
    public $lecture_end_time;

    /**
     * The real name of the teacher.
     * @var String
     */
    public $teacher_name;

    /**
     * The province where the lecture is held.
     * @var String
     */
    public $lecture_province;

    /**
     * The city where the lecture is held.
     * @var String
     */
    public $lecture_city;

    /**
     * The address where the lecture is held.
     * @var String
     */
    public $lecture_address;
}