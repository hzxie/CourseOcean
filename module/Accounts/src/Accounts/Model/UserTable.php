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

    /**
     * Verify if the username has existed.
     * @param  String  $username - the username of the user
     * @return true if the username has existed
     */
    public function isUsernameExists($username)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'username'  => $username,
            )
        );
        return ( $rowset->current() != null );
    }

    /**
     * Verify if the email address has existed.
     * @param  String  $email - the email address of the user
     * @return an Object of User if the email exists
     */
    public function isEmailExists($email)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'email'     => $email,
            )
        );
        return $rowset->current();
    }

    /**
     * Get the unique id of the user by his username
     * @param  String  $username - the username of the user
     * @return the unique id of the user
     */
    public function getUidByUsername($username)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'username'  => $username,
            )
        );
        return $rowset->current();
    }

    /**
     * Handle asynchronous register requests for the users.
     * @return the unique id of the user
     */
    public function createNewUser($basicInfo)
    {
        $this->tableGateway->insert($basicInfo);
        $userData   = $this->getUidByUsername($basicInfo['username']);

        return $userData->uid;
    }

    /**
     * Update activation status of the account in database.
     * @param  String $email - the email of the user
     * @param  bool $isActivated - a flag that infers if the account has 
     *         been activated
     */
    public function updateAccountActivated($email, $isActivated)
    {
        return $this->tableGateway->update(
            array(
                'is_activated'  => $isActivated,
            ),
            array(
                'email'         => $email,
            )
        );
    }

    /**
     * Handle asynchronous editing email requests for a user.
     * @param  Array $userInfo
     * @return true if the query is successful
     */
    public function editProfile($userInfo)
    {
        $this->tableGateway->update($userInfo, array(
            'uid'   => $userInfo['uid'],
        ));
        return true;
    }

    /**
     * Verify if the old password is correct.
     * @param  Array $passport - an array which contains the uid and current
     *         password of the user
     * @return true if the old password is correct
     */
    public function isPasswordCorrect($passport)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'uid'       => $passport['uid'],
                'password'  => $passport['password'],
            )
        );
        return ( $rowset->current() != null );
    }

    /**
     * Handle asynchronous changing password requests for a user.
     * @param  Array $passport - an array which contains the uid and new 
     *         password of the user
     * @return true if the query is successful
     */
    public function changePassword($passport)
    {
        $passport  += array(
            'last_time_change_password' => $this->getCurrentTimeStamp(),
        );
        
        $this->tableGateway->update($passport, array(
            'uid'   => $passport['uid'],
        ));
        return true;
    }
}