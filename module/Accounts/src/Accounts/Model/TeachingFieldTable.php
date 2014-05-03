<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the teacher table.
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
	 * The contructor of the TeacherTable class.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	
	public function getTeachingField($uid)
	{
        $resultSet  = $this->tableGateway->select(function (Select $select) use ($uid) {
            $select->join('itp_course_types', 
                          'itp_teaching_field.course_type_id = itp_course_types.course_type_id');
            $select->where->equalTo('uid', $uid);
        });
        return $resultSet;
	}
	
}