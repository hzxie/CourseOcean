<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class UserTable
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
	 * The contructor of the UserTable class.
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
     * Verify if the username and password is valid.
     * @param  String $username - the username of an account
     * @param  String $password - the encrypted password of an account
     * @return true if the username and password is valid
     */
    public function verifyAccountByUsername($username, $password)
    {
    	$rowset 	= $this->tableGateway->select(
    		array( 
    			'username'	=> $username,
    			'password'	=> $password,
    		)
    	);
    	$row 		= $rowset->current();

    	if ( !$row ) {
    		return false;
    	}
    	return true;
    }

    /**
     * Verify if the email and password is valid.
     * @param  String $email 	- the email of an account
     * @param  String $password - the encrypted password of an account
     * @return true if the email and password is valid
     */
    public function verifyAccountByEmail($email, $password)
    {
    	$rowset 	= $this->tableGateway->select(
    		array( 
    			'email'		=> $email,
    			'password'	=> $password,
    		)
    	);
    	$row 		= $rowset->current();

    	if ( !$row ) {
    		return false;
    	}
    	return true;
    }
}