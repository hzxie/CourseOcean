<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_people table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Person {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->uid                  = (!empty($data['uid']))                    ? $data['uid']                  : null;
        $this->person_name          = (!empty($data['person_name']))            ? $data['person_name']          : null;
        $this->person_region        = (!empty($data['person_region']))          ? $data['person_region']        : null;
        $this->preson_province      = (!empty($data['preson_province']))        ? $data['preson_province']      : null;
        $this->person_city          = (!empty($data['person_city']))            ? $data['person_city']          : null;
        $this->person_position_id   = (!empty($data['person_position_id']))     ? $data['person_position_id']   : null;
        $this->person_work_time     = (!empty($data['person_work_time']))       ? $data['person_work_time']     : null;
        $this->person_phone         = (!empty($data['person_phone']))           ? $data['person_phone']         : null;
    }

    /**
     * The unique id of the user.
     * @var int 
     */
    public $uid;

    /**
     * The real name of the user.
     * @var String
     */
    public $person_name;

    /**
     * The region in China where the user live.
     * @var String
     */
    public $person_region;

    /**
     * The province in China where the user live.
     * @var String
     */
    public $preson_province;

    /**
     * The city in China where the user live.
     * @var String
     */
    public $person_city;

    /**
     * The unique id of positions(职位) of the user.
     * @var int
     */
    public $person_position_id;

    /**
     * The duration the user working.
     * @var String
     */
    public $person_work_time;

    /**
     * The phone number of the user.
     * @var String
     */
    public $person_phone;
}