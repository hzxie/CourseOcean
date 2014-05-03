<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the teaching field table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class TeachingFieldTable
{
	/**
	 * The Table Gateway object is intended to provide an object that 
	 * represents a table in a database, and the methods of this object 
	 * mirror the most common operations on a database table.
	 * 
	 * @var TableGateway
	 */
	protected $tableGateway;

	/**
	 * The contructor of the TeacherFieldTable class.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Handle teachers' changing teaching field requests.
	 * @param  int   $uid - the unique id of the teacher
	 * @param  Array $courseTypeIdArray - an array which contains teaching
	 *         field ids of the teacher
	 * @return true if the query is successful
	 */
	public function changeTeachingField($uid, $courseTypeIdArray)
	{
		$this->removeTeachingField($uid);

		foreach ( $courseTypeIdArray as $courseTypeId ) {
			$teachingFieldInfo = array(
				'uid' 				=> $uid,
				'course_type_id'	=> $courseTypeId,
			);
			$this->addTeachingField($teachingFieldInfo);
		}
		return true;
	}

	/**
	 * [addTeachingField description]
	 * @param [type] $teachingFieldInfo [description]
	 */
	private function addTeachingField($teachingFieldInfo)
	{
		return $this->tableGateway->insert($teachingFieldInfo);
	}

	/**
	 * Remove all teaching field of a teacher before changing.
	 * @param  int   $uid - the unique id of the teacher
	 * @return true if the query is successful
	 */
	private function removeTeachingField($uid)
	{
		return $this->tableGateway->delete(
            array( 'uid' => $uid )
        );
	}
}