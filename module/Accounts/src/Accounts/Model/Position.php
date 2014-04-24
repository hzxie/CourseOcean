<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_positions table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Position {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->position_id      = (!empty($data['position_id']))    ? $data['position_id']      : null;
        $this->position_slug    = (!empty($data['position_slug']))  ? $data['position_slug']    : null;
        $this->position_name    = (!empty($data['position_name']))  ? $data['position_name']    : null;
    }

    /**
     * The unique id of the position.
     * @var int
     */
    public $position_id;

    /**
     * The unique slug of the position.
     * @var String
     */
    public $position_slug;

    /**
     * The display name of the position.
     * @var String
     */
    public $position_name;
}