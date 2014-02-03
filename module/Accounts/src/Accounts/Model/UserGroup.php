<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_user_groups table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
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
		$this->user_group_id	= (!empty($data['user_group_id'])) 		? $data['user_group_id'] 	: null;
		$this->user_group_slug	= (!empty($data['user_group_slug'])) 	? $data['user_group_slug'] 	: null;
		$this->user_group_name	= (!empty($data['user_group_name'])) 	? $data['user_group_name'] 	: null;
	}

	/**
	 * The unique id of the user group.
	 * @var int
	 */
	public $user_group_id;

	/**
	 * The slug name of the user group.
	 * @var String
	 */
	public $user_group_slug;

	/**
	 * The name to display of the user group.
	 * @var String
	 */
	public $user_group_name;
}