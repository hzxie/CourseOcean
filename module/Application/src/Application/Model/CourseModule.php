<?php

namespace Application\Model;

/**
 * 课程模块类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class CourseModule {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->courseModuleId           = (!empty($data['course_module_id']))           ? $data['course_module_id']         : null;
        $this->courseModuleName         = (!empty($data['course_module_name']))         ? $data['course_module_name']       : null;
        $this->teacherId                = (!empty($data['teacher_id']))                 ? $data['teacher_id']               : null;
        $this->courseModuleCycle        = (!empty($data['course_module_cycle']))        ? $data['course_module_cycle']      : null;
        $this->courseModuleStartTime    = (!empty($data['course_module_start_time']))   ? $data['course_module_start_time'] : null;
        $this->courseModuleEndTime      = (!empty($data['course_module_end_time']))     ? $data['course_module_end_time']   : null;
        $this->courseModuleBrief        = (!empty($data['course_module_brief']))        ? $data['course_module_brief']      : null;
        $this->courseModuleOutline      = (!empty($data['course_module_outline']))      ? $data['course_module_outline']    : null;
    }

    /**
     * 课程模块的唯一标识符.
     * @var int 
     */
    public $courseModuleId;

    /**
     * 课程模块的名称.
     * @var String
     */
    public $courseModuleName;

    /**
     * 讲师用户唯一标识符
     * @var int
     */
    public $teacherId;

    /**
     * 课程周期(小时数)
     * @var int
     */
    public $courseModuleCycle;

    /**
     * 课程模块开始时间.
     * @var DateTime
     */
    public $courseModuleStartTime;

    /**
     * 课程模块结束时间.
     * @var DateTime
     */
    public $courseModuleEndTime;

    /**
     * 课程模块概述.
     * @var String
     */
    public $courseModuleBrief;

    /**
     * 课程模块大纲.
     * @var String
     */
    public $courseModuleOutline;
}