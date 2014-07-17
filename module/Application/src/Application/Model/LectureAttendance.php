<?php

namespace Application\Model;

/**
 * 课程会话参与类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
        $this->uid          = (!empty($data['uid']))            ? $data['uid']          : null;
        $this->lectureId    = (!empty($data['lecture_id']))     ? $data['lecture_id']   : null;
        $this->serialCode   = (!empty($data['serial_code']))    ? $data['serial_code']  : null;
        $this->totalTimes   = (!empty($data['total_times']))    ? $data['total_times']  : null;
        $this->remainTimes  = (!empty($data['remain_times']))   ? $data['remain_times'] : null;
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
}