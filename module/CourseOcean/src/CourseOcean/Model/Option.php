<?php

namespace CourseOcean\Model;

/**
 * 系统设置类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class Option {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->optionId     = (!empty($data['option_id']))      ? $data['option_id']        : null;
        $this->optionKey    = (!empty($data['option_key']))     ? $data['option_key']       : null;
        $this->optionValue  = (!empty($data['option_value']))   ? $data['option_value']     : null;
    }

    /**
     * 系统设置选项的唯一标识符.
     * @var int 
     */
    public $optionId;
    
    /**
     * 系统设置选项的Key.
     * @var String
     */
    public $optionKey;

    /**
     * 系统设置选项的Value.
     * @var String
     */
    public $optionValue;
}