<?php

namespace Application\Model;

/**
 * 工作职位的Model.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class Position {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data) {
        $this->positionId       = (!empty($data['position_id']))    ? $data['position_id']      : null;
        $this->positionSlug     = (!empty($data['position_slug']))  ? $data['position_slug']    : null;
        $this->positionName     = (!empty($data['position_name']))  ? $data['position_name']    : null;
    }

    /**
     * 工作职位的唯一标识符.
     * @var int
     */
    public $positionId;

    /**
     * 工作职位的唯一英文缩写.
     * @var String
     */
    public $positionSlug;

    /**
     * 工作职位的名称.
     * @var String
     */
    public $positionName;
}