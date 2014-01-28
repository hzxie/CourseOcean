<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the teacher table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class TeacherTable
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

	/**
	 * Get all records from the teacher table.
	 * @return an object which is an instance of ResultSet, which contains
	 *         data of all teachers.
	 */
	public function fetchAll()
	{
		$resultSet 	= $this->tableGateway->select();
		return $resultSet;
	}

    /**
     * Get detail information of the teacher.
     * @param  int $uid - the unique id of the user
     * @return an object of Teacher which contains all information of the 
     *         teacher
     */
    public function getTeacherInfo($uid)
    {
        $rowset     = $this->tableGateway->select(
            array(
                'uid'   => $uid,
            )
        );
        return $rowset->current();
    }
}