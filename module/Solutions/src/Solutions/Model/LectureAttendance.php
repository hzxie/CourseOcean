<?php

namespace Solutions\Model;

/**
 * 讲座参与记录的Model.
 * 与数据库中的itp_lecture_attendance表关联.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
     * 讲座的唯一标识符.
     * @var int
     */
    public $lecture_id;

    /**
     * 用户唯一标识符(uid).
     * @var int
     */
    public $uid;

    /**
     * 该记录的参与人数.
     * @var int
     */
    public $participants;

    /**
     * 讲座参与序列号(入场时使用).
     * @var String
     */
    public $serial_number;

    /**
     * 该课程的名称.
     * @var String
     */
    public $course_name;

    /**
     * 该课程的类型唯一缩写(slug).
     * @var String
     */
    public $course_type_slug;

    /**
     * 该课程的开始时间.
     * @var DateTime
     */
    public $lecture_start_time;

    /**
     * 该课程的结束时间.
     * @var DateTime
     */
    public $lecture_end_time;

    /**
     * 讲师的真实姓名.
     * @var String
     */
    public $teacher_name;

    /**
     * 该课程的开课地点所在省份.
     * @var String
     */
    public $lecture_province;

    /**
     * 该课程的开课地点所在城市.
     * @var String
     */
    public $lecture_city;

    /**
     * 该课程的开课地点的地址.
     * @var String
     */
    public $lecture_address;
}