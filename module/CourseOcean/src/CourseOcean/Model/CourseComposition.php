<?php

namespace CourseOcean\Model;

/**
 * 课程组成类的Model.
 * 用于存储某个课程包含哪些课程模块.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class CourseComposition {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->courseId         = (!empty($data['course_id']))          ? $data['course_id']        : null;
        $this->courseModuleId   = (!empty($data['course_module_id']))   ? $data['course_module_id'] : null;
    }

    /**
     * 课程唯一标识符.
     * @var int
     */
    public $courseId;

    /**
     * 课程模块唯一标识符.
     * @var int
     */
    public $courseModuleId;
}