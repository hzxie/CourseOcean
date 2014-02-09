<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the enterprise table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class EnterpriseTable
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
	 * The contructor of the PersonTable class.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Get all records from the users table.
	 * @return an object which is an instance of ResultSet, which contains
	 *         data of all users.
	 */
	public function fetchAll()
	{
		$resultSet 	= $this->tableGateway->select();
		return $resultSet;
	}

	/**
	 * Get profile of a user.
	 * @param  int $uid - the unique id of the user
	 * @return an Object of Person which contains information of the person
	 */
	public function getEnterpriseInfo($uid)
	{
		$rowset     	= $this->tableGateway->select(
            array( 
                'uid'  	=> $uid,
            )
        );
        return $rowset->current();
	}

    /**
     * Handle asynchronous register requests for an enterprise.
     * @return true if the query is successful
     */
    public function createNewEnterprise($enterpriseInfo)
    {
        $this->tableGateway->insert($enterpriseInfo);
        return true;
    }
}