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
class PositionTable
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
     *         data of all work positions.
     */
    public function fetchAll()
    {
        $resultSet  = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * Get the unique id of the work position by its slug.
     * @param  String $positionSlug - the unique slug of the work position
     * @return the unique id of the work position
     */
    public function getPositionID($positionSlug)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'position_slug'  => $positionSlug,
            )
        );
        return $rowset->current();
    }

    /**
     * Get the unique slug of the work position by its id.
     * @param  int $positionID - the unique id of the work position
     * @return the unique slug of the work position
     */
    public function getUserGroupSlug($positionID)
    {
        $rowset     = $this->tableGateway->select(
            array( 
                'position_id'  => $positionID,
            )
        );
        return $rowset->current();
    }
}