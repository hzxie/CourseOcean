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
		$this->uid			= (!empty($data['uid'])) 			? $data['uid'] 			: null;
		$this->real_name	= (!empty($data['real_name'])) 		? $data['real_name'] 	: null;
		$this->introduction	= (!empty($data['introduction'])) 	? $data['introduction'] : null;
		$this->avatar		= (!empty($data['avatar'])) 		? $data['avatar'] 		: null;
		$this->company		= (!empty($data['company'])) 		? $data['company'] 		: null;
		$this->phone		= (!empty($data['phone'])) 			? $data['phone'] 		: null;
		$this->wechat		= (!empty($data['wechat'])) 		? $data['wechat'] 		: null;
		$this->weibo		= (!empty($data['weibo'])) 			? $data['weibo'] 		: null;
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
	 * A brief introduction of the user.
	 * @var String
	 */
	public $introduction;

	/**
	 * The relative path of the avatar of the user.
	 * @var String
	 */
	public $avatar;

	/**
	 * The company name of the user.
	 * @var String
	 */
	public $company;

	/**
	 * The phone number of the user.
	 * @var String
	 */
	public $phone;

	/**
	 * The account of the wechat of the user.
	 * @var String
	 */
	public $wechat;

	/**
	 * The account of the weibo of the user.
	 * @var String
	 */
	public $weibo;
}