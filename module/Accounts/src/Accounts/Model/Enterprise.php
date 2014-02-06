<?php

namespace Accounts\Model;

/**
 * The class is the model of the itp_enterprise table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Enterprise {
	/**
	 * This method simply copies the data from the passed in array to our 
	 * entitys properties.
	 * 
	 * @param  Array $data - an array which contains data descripted in the
	 *         users table.
	 */
	public function exchangeArray($data)
	{
		$this->uid 			= (!empty($data['uid'])) 			? $data['uid'] 			: null;
		$this->company_name	= (!empty($data['company_name'])) 	? $data['company_name'] : null;
		$this->logo 		= (!empty($data['logo'])) 			? $data['logo'] 		: null;
		$this->address 		= (!empty($data['address'])) 		? $data['address'] 		: null;
		$this->phone 		= (!empty($data['phone'])) 			? $data['phone'] 		: null;
	}

	/**
	 * The unique id of the user.
	 * @var int 
	 */
	public $uid;

	/**
	 * The name of the company.
	 * @var String
	 */
	public $company_name;

	/**
	 * The relative path of the logo of the company.
	 * @var String
	 */
	public $logo;

	/**
	 * The address of the company.
	 * @var String
	 */
	public $address;

	/**
	 * The phone number of the company.
	 * @var String
	 */
	public $phone;
}