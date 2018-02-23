<?php

namespace Application\Model;

/**
 * 课程会话计划类的Model.
 * 用于存储某个课程会话包含哪些课程模块以及具体时间.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class LectureSchedule {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data) {
        $this->lectureId                = (!empty($data['lecture_id']))                 ? $data['lecture_id']               : null;
        $this->courseModuleId           = (!empty($data['course_module_id']))           ? $data['course_module_id']         : null;
        $this->courseModuleStartTime    = (!empty($data['course_module_start_time']))   ? $data['course_module_start_time'] : null;
        $this->courseModuleEndTime      = (!empty($data['course_module_end_time']))     ? $data['course_module_end_time']   : null;
    }

    /**
     * 课程会话唯一标识符.
     * @var int
     */
    public $lectureId;

    /**
     * 课程模块唯一标识符.
     * @var int
     */
    public $courseModuleId;

    /**
     * 课程模块起始时间.
     * @var DateTime
     */
    public $courseModuleStartTime;

    /**
     * 课程模块结束时间.
     * @var DateTime
     */
    public $courseModuleEndTime;
}