<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_person table.
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
		$this->uid 			= (!empty($data['uid'])) 		? $data['uid'] 			: null;
		$this->real_name	= (!empty($data['real_name'])) 	? $data['real_name'] 	: null;
		$this->phone 		= (!empty($data['phone'])) 		? $data['phone'] 		: null;
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
	public $real_name;

	/**
	 * The phone number of the user.
	 * @var String
	 */
	public $phone;
}