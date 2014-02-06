<?php

namespace Courses\Model;

/**
 * The class is the model of the itp_courses table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Course {
	/**
	 * This method simply copies the data from the passed in array to our 
	 * entitys properties.
	 * 
	 * @param  Array $data - an array which contains data descripted in the
	 *         courses table.
	 */
	public function exchangeArray($data)
	{
		$this->course_id			= (!empty($data['course_id'])) 			? $data['course_id'] 		: null;
		$this->course_name 			= (!empty($data['course_name'])) 		? $data['course_name'] 		: null;
		$this->course_type_id		= (!empty($data['course_type_id'])) 	? $data['course_type_id'] 	: null;
		$this->course_type_slug 	= (!empty($data['course_type_slug'])) 	? $data['course_type_slug'] : null;
		$this->course_type_name		= (!empty($data['course_type_name'])) 	? $data['course_type_name'] : null;
		$this->uid 					= (!empty($data['uid'])) 				? $data['uid'] 				: null;
		$this->real_name 			= (!empty($data['real_name'])) 			? $data['real_name'] 		: null;
		$this->create_time 			= (!empty($data['create_time'])) 		? $data['create_time'] 		: null;
		$this->brief 				= (!empty($data['brief'])) 				? $data['brief'] 			: null;
		$this->cycle 				= (!empty($data['cycle'])) 				? $data['cycle'] 			: null;
		$this->audience 			= (!empty($data['audience'])) 			? $data['audience'] 		: null;
	}

	/**
	 * The unique id of the course.
	 * @var int
	 */
	public $course_id;

	/**
	 * The name of the course.
	 * @var String
	 */
	public $course_name;

	/**
	 * The unique id of the course type.
	 * @var int
	 */
	public $course_type_id;

	/**
	 * The slug name of the course type.
	 * @var String
	 */
	public $course_type_slug;

	/**
	 * The name of the course type.
	 * @var String
	 */
	public $course_type_name;

	/**
	 * The unique id of the teacher.
	 * @var int
	 */
	public $uid;

	/**
	 * The real name of the teacher.
	 * @var String
	 */
	public $real_name;

	/**
	 * The time when the course has been created.
	 * @var TimeStamp
	 */
	public $create_time;

	/**
	 * The brief introduction of the course.
	 * @var String
	 */
	public $brief;

	/**
	 * The time required of the course.
	 * @var int
	 */
	public $cycle;

	/**
	 * The audience who are suitable for the course.
	 * @var String
	 */
	public $audience;
}