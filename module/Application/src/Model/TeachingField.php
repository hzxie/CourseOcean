<?php

namespace Application\Model;

/**
 * 授课领域类的Model.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class TeachingField {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data) {
        $this->teacherId        = (!empty($data['teacher_id']))         ? $data['teacher_id']       : null;
        $this->courseTypeId     = (!empty($data['course_type_id']))     ? $data['course_type_id']   : null;
        $this->courseTypeSlug   = (!empty($data['course_type_slug']))   ? $data['course_type_slug'] : null;
        $this->courseTypeName   = (!empty($data['course_type_name']))   ? $data['course_type_name'] : null;
    }

    /**
     * 用户的唯一标识符.
     * @var int 
     */
    public $teacherId;

    /**
     * 课程类型(授课领域)的唯一标识符.
     * @var int
     */
    public $courseTypeId;

    /**
     * 课程类型(授课领域)的唯一英文缩写.
     * @var String
     */
    public $courseTypeSlug;

    /**
     * 课程类型(授课领域)的名称.
     * @var String
     */
    public $courseTypeName;
}