<?php

namespace CourseOcean\Model;

/**
 * 课程会话参与类的Model.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class LectureAttendance {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->uid                  = (!empty($data['uid']))                ? $data['uid']                  : null;
        $this->lectureId            = (!empty($data['lecture_id']))         ? $data['lecture_id']           : null;
        $this->courseName           = (!empty($data['course_name']))        ? $data['course_name']          : null;
        $this->courseTypeSlug       = (!empty($data['course_type_slug']))   ? $data['course_type_slug']     : null;
        $this->lectureStartTime     = (!empty($data['lecture_start_time'])) ? $data['lecture_start_time']   : null;
        $this->lectureEndTime       = (!empty($data['lecture_end_time']))   ? $data['lecture_end_time']     : null;
        $this->lectureTeacherId     = (!empty($data['teacher_id']))         ? $data['teacher_id']           : null;
        $this->lectureTeacherName   = (!empty($data['teacher_name']))       ? $data['teacher_name']         : null;
        $this->lectureProvince      = (!empty($data['lecture_province']))   ? $data['lecture_province']     : null;
        $this->lectureCity          = (!empty($data['lecture_city']))       ? $data['lecture_city']         : null;
        $this->lectureAddress       = (!empty($data['lecture_address']))    ? $data['lecture_address']      : null;
        $this->serialCode           = (!empty($data['serial_code']))        ? $data['serial_code']          : null;
        $this->totalTimes           = (!empty($data['total_times']))        ? $data['total_times']          : null;
        $this->remainTimes          = (!empty($data['remain_times']))       ? $data['remain_times']         : null;
        $this->commentRanking       = (!empty($data['comment_ranking']))    ? $data['comment_ranking']      : null;
        $this->commentDetail        = (!empty($data['comment_detail']))     ? $data['comment_detail']       : null;
    }

    /**
     * 用户的唯一标识符.
     * @var int 
     */
    public $uid;

    /**
     * 课程会话唯一标识符.
     * @var int
     */
    public $lectureId;

    /**
     * 课程的名称.
     * @var String
     */
    public $courseName;

    /**
     * 课程类型的唯一英文缩写.
     * @var String
     */
    public $courseTypeSlug;

    /**
     * 课程会话的开始时间.
     * @var DateTime
     */
    public $lectureStartTime;

    /**
     * 课程会话的结束时间.
     * @var DateTime
     */
    public $lectureEndTime;

    /**
     * 课程讲师的用户唯一标识符
     * @var int
     */
    public $lectureTeacherId;

    /**
     * 课程讲师的姓名.
     * @var String
     */
    public $lectureTeacherName;

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
     * 报名验证序列号.
     * @var String
     */
    public $serialCode;

    /**
     * 报名时申请的使用次数(参与人数)
     * @var int
     */
    public $totalTimes;

    /**
     * 剩余使用次数.
     * @var int
     */
    public $remainTimes;

    /**
     * 对课程的评价等级.
     * @var int
     */
    public $commentRanking;

    /**
     * 对课程的评价.
     * @var String
     */
    public $commentDetail;
}