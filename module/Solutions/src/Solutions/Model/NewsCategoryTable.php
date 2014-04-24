<?php

namespace Solutions\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the news categories table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class NewsCategoryTable
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
     * Get all records from the course_types table.
     * @return an object which is an instance of ResultSet, which contains
     *         data of all course types.
     */
    public function fetchAll()
    {
        $resultSet  = $this->tableGateway->select();
        return $resultSet;
    }
}