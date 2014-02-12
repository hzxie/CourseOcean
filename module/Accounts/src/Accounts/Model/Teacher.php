<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_teacher table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Teacher {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->uid              = (!empty($data['uid']))                ? $data['uid']              : null;
        $this->teacher_name     = (!empty($data['teacher_name']))       ? $data['teacher_name']     : null;
        $this->teacher_brief    = (!empty($data['teacher_brief']))      ? $data['teacher_brief']    : null;
        $this->teacher_field    = (!empty($data['teacher_field']))      ? $data['teacher_field']    : null;
        $this->teacher_avatar   = (!empty($data['teacher_avatar']))     ? $data['teacher_avatar']   : null;
        $this->teacher_region   = (!empty($data['teacher_region']))     ? $data['teacher_region']   : null;
        $this->teacher_province = (!empty($data['teacher_province']))   ? $data['teacher_province'] : null;
        $this->teacher_city     = (!empty($data['teacher_city']))       ? $data['teacher_city']     : null;
        $this->teacher_company  = (!empty($data['teacher_company']))    ? $data['teacher_company']  : null;
        $this->teacher_phone    = (!empty($data['teacher_phone']))      ? $data['teacher_phone']    : null;
        $this->teacher_weibo    = (!empty($data['teacher_weibo']))      ? $data['teacher_weibo']    : null;
    }

    /**
     * The unique id of the teacher.
     * @var int
     */
    public $uid;

    /**
     * The real name of the teacher.
     * @var String
     */
    public $teacher_name;

    /**
     * A brief introduction of the teacher.
     * @var String
     */
    public $teacher_brief;

    /**
     * The field of research of the teacher.
     * @var String
     */
    public $teacher_field;

    /**
     * The relative path of the avatar of the teacher.
     * @var String
     */
    public $teacher_avatar;

    /**
     * The region in China where the teacher live.
     * @var String
     */
    public $teacher_region;

    /**
     * The province in China where the teacher live.
     * @var String
     */
    public $teacher_province;

    /**
     * The city in China where the teacher live.
     * @var String
     */
    public $teacher_city;

    /**
     * The company name of the teacher.
     * @var String
     */
    public $teacher_company;

    /**
     * The phone number of the teacher.
     * @var String
     */
    public $teacher_phone;

    /**
     * The account of the weibo of the teacher.
     * @var String
     */
    public $teacher_weibo;
}