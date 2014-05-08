<?php

namespace Solutions\Model;

/**
 * 用户需求的Model.
 * 与数据库中的itp_requirement表关联.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class Requirement {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->requirement_id           = (!empty($data['requirement_id']))             ? $data['requirement_id']           : null;
        $this->requirement_from_uid     = (!empty($data['requirement_from_uid']))       ? $data['requirement_from_uid']     : null;
        $this->requirement_to_uid       = (!empty($data['requirement_to_uid']))         ? $data['requirement_to_uid']       : null;
        $this->requirement_teacher_name = (!empty($data['requirement_teacher_name']))   ? $data['requirement_teacher_name'] : null;
        $this->requirement_create_time  = (!empty($data['requirement_create_time']))    ? $data['requirement_create_time']  : null;
        $this->requirement_participants = (!empty($data['requirement_participants']))   ? $data['requirement_participants'] : null;
        $this->requirement_start_time   = (!empty($data['requirement_start_time']))     ? $data['requirement_start_time']   : null;
        $this->requirement_end_time     = (!empty($data['requirement_end_time']))       ? $data['requirement_end_time']     : null;
        $this->requirement_place        = (!empty($data['requirement_place']))          ? $data['requirement_place']        : null;
        $this->requirement_detail       = (!empty($data['requirement_detail']))         ? $data['requirement_detail']       : null;
    }

    /**
     * 需求唯一标识符.
     * @var int
     */
    public $requirement_id;

    /**
     * 需求提出者的用户唯一标识符(uid).
     * @var int
     */
    public $requirement_from_uid;

    /**
     * 需求接受者的用户唯一标识符(uid).
     * @var int
     */
    public $requirement_to_uid;

    /**
     * 需求接受者的讲师姓名.
     * @var String
     */
    public $requirement_teacher_name;

    /**
     * 需求创建日期.
     * @var DateTime
     */
    public $requirement_create_time;

    /**
     * 需求参与的人数(培训人数).
     * @var int
     */
    public $requirement_participants;

    /**
     * 培训的开始时间.
     * @var DateTime
     */
    public $requirement_start_time;

    /**
     * 培训的结束时间.
     * @var DateTime
     */
    public $requirement_end_time;

    /**
     * 培训的开展地点.
     * @var String
     */
    public $requirement_place;

    /**
     * 培训的详细需求.
     * @var String
     */
    public $requirement_detail;
}