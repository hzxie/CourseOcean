<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the user_groups table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class UserGroupTable
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
	 * The contructor of the UserGroupTable class.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Get all records from the user_groups table.
	 * @return an object which is an instance of ResultSet, which contains
	 *         data of all user groups.
	 */
	public function fetchAll()
	{
		$resultSet 	= $this->tableGateway->select();
		return $resultSet;
	}

    public function getUserGroupID($userGroupSlug)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'user_group_slug'  => $userGroupSlug,
            )
        );
        return $rowset->current();
    }
}