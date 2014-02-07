<?php

namespace Courses\Model;

/**
 * The class is the model of the itp_lectures table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class Lecture {
	/**
	 * This method simply copies the data from the passed in array to our 
	 * entitys properties.
	 * 
	 * @param  Array $data - an array which contains data descripted in the
	 *         courses table.
	 */
	public function exchangeArray($data)
	{
		$this->lecture_id		= (!empty($data['lecture_id'])) 		? $data['lecture_id'] 		: null;
		$this->course_id		= (!empty($data['course_id'])) 			? $data['course_id'] 		: null;
		$this->course_name		= (!empty($data['course_name'])) 		? $data['course_name'] 		: null;
		$this->brief			= (!empty($data['brief'])) 				? $data['brief'] 			: null;
		$this->course_type_slug	= (!empty($data['course_type_slug'])) 	? $data['course_type_slug'] : null;
		$this->course_type_name	= (!empty($data['course_type_name'])) 	? $data['course_type_name'] : null;
		$this->real_name		= (!empty($data['real_name'])) 			? $data['real_name'] 		: null;
		$this->audience			= (!empty($data['audience'])) 			? $data['audience'] 		: null;
		$this->start_time		= (!empty($data['start_time'])) 		? $data['start_time'] 		: null;
		$this->end_time			= (!empty($data['end_time'])) 			? $data['end_time'] 		: null;
		$this->place_region		= (!empty($data['place_region'])) 		? $data['place_region'] 	: null;
		$this->place_province	= (!empty($data['place_province'])) 	? $data['place_province'] 	: null;
		$this->place_city		= (!empty($data['place_city'])) 		? $data['place_city'] 		: null;
		$this->place_address	= (!empty($data['place_address'])) 		? $data['place_address'] 	: null;
		$this->expense			= (!empty($data['expense'])) 			? $data['expense'] 			: null;
	}

	/**
	 * The unique id of the lecture.
	 * @var int
	 */
	public $lecture_id;

	/**
	 * The unique id of the course.
	 * @var int
	 */
	public $course_id;

	public $course_name;

	public $brief;

	public $course_type_slug;

	public $course_type_name;

	public $real_name;

	public $audience;

	/**
	 * The date when the lecture start.
	 * @var Date
	 */
	public $start_time;

	/**
	 * The date when the lecture end.
	 * @var Date
	 */
	public $end_time;

	/**
	 * The region where the lecture is held.
	 * @var String
	 */
	public $place_region;

	/**
	 * The province where the lecture is held.
	 * @var String
	 */
	public $place_province;

	/**
	 * The city where the lecture is held.
	 * @var String
	 */
	public $place_city;

	/**
	 * The address where the lecture is held.
	 * @var String
	 */
	public $place_address;

	/**
	 * The expense of the lecture.
	 * @var int
	 */
	public $expense;
}