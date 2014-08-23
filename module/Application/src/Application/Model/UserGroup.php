<?php

namespace Application\Model;

/**
 * 用户组的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class UserGroup {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->userGroupId      = (!empty($data['user_group_id']))      ? $data['user_group_id']    : null;
        $this->userGroupSlug    = (!empty($data['user_group_slug']))    ? $data['user_group_slug']  : null;
        $this->userGroupName    = (!empty($data['user_group_name']))    ? $data['user_group_name']  : null;
    }

    /**
     * 用户组的唯一标识符.
     * @var int
     */
    public $userGroupId;

    /**
     * 用户组的唯一英文缩写.
     * @var String
     */
    public $userGroupSlug;

    /**
     * 用户组的名称.
     * @var String
     */
    public $userGroupName;
}