<?php

namespace Accounts\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the email validation table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class EmailValidationTable
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
     * Verify if the email and guid is valid.
     * @param  String $email - the email of the user
     * @param  String $guid - the code that the user received
     * @return true if the combination of email and guid is valid
     */
    public function validateEmail($email, $guid)
    {
        $rowset         = $this->tableGateway->select(
            array( 
                'email' => $email,
                'guid'  => $guid,
            )
        );
        if ( $rowset->current() ) {
            $this->deleteRecord($email);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the email has exists in this table.
     * @param  String $email - the email of the user
     * @return true if the email has exists in this table
     */
    public function containsEmail($email)
    {
        $rowset         = $this->tableGateway->select(
            array( 
                'email'             => $email,
            )
        );
        return $rowset->current();
    }

    /**
     * Create a record in this table.
     * @param  EmailValidation $record - an object of EmailValidation 
     *         which contains a combination of email and guid
     */
    public function createRecord($record)
    {
        $email  = $record['email'];

        if ( $this->containsEmail($email) ) {
            $this->deleteRecord($email);
        }

        return $this->tableGateway->insert($record);
    }

    /**
     * Delete the record after the combination of email and guid has 
     * been verified.
     * @param String $email - the email of the user
     */
    public function deleteRecord($email)
    {
        return $this->tableGateway->delete(
            array( 'email' => $email )
        );
    }
}