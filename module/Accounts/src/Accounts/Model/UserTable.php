<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the users table.
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
     * @return an User object if the the username and password is valid,
     *         otherwise, return false
     */
    public function verifyAccountByUsername($username, $password)
    {
    	$rowset 	= $this->tableGateway->select(
    		array( 
    			'username'	=> $username,
    			'password'	=> $password,
    		)
    	);
    	return $this->updateTimeStamp($rowset->current());
    }

    /**
     * Verify if the email and password is valid.
     * @param  String $email 	- the email of an account
     * @param  String $password - the encrypted password of an account
     * @return an User object if the the username and password is valid,
     *         otherwise, return false
     */
    public function verifyAccountByEmail($email, $password)
    {
    	$rowset 	= $this->tableGateway->select(
    		array( 
    			'email'		=> $email,
    			'password'	=> $password,
    		)
    	);
    	return $this->updateTimeStamp($rowset->current());
    }

    /**
     * Update the time when the user last sign in in the database.
     * @param  User $user - an object of User which contains user's
     *         profile
     * @return an User object if the the username and password is valid,
     *         otherwise, return false
     */
    private function updateTimeStamp($user)
    {
        if ( !$user ) {
            return false;
        }

        $uid    = $user->uid;
        $data   = array(
            'last_time_signin'  => $this->getCurrentTimeStamp(),
        );
        $this->tableGateway->update($data, array('uid' => $uid));

        return $user;
    }

    /**
     * Get the current time stamp.
     * @return an formatted string with current time stamp.
     */
    private function getCurrentTimeStamp()
    {
        $currentTime = new \DateTime('now', new \DateTimeZone('Asia/Shanghai'));
        return $currentTime->format('Y-m-d H:i:s');
    }
}