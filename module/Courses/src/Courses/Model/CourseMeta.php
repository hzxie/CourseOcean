<?php

namespace Courses\Model;

/**
 * The class is the model of the itp_course_meta table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class CourseMeta {
	/**
	 * This method simply copies the data from the passed in array to our 
	 * entitys properties.
	 * 
	 * @param  Array $data - an array which contains data descripted in the
	 *         courses table.
	 */
	public function exchangeArray($data)
	{
		$this->meta_id		= (!empty($data['meta_id'])) 	? $data['meta_id'] 		: null;
		$this->course_id	= (!empty($data['course_id'])) 	? $data['course_id'] 	: null;
		$this->meta_key		= (!empty($data['meta_key'])) 	? $data['meta_key'] 	: null;
		$this->meta_value	= (!empty($data['meta_value'])) ? $data['meta_value'] 	: null;
	}

	/**
	 * The unique id of the meta of course.
	 * @var int
	 */
	public $meta_id;

	/**
	 * The unique id of the course.
	 * @var int
	 */
	public $course_id;

	/**
	 * The key of the meta.
	 * @var String
	 */
	public $meta_key;

	/**
	 * The value of the meta.
	 * @var String
	 */
	public $meta_value;
}