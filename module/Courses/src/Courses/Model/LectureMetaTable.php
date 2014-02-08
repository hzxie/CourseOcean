<?php

namespace Courses\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the lecture_meta table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class LectureMetaTable
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
     * Get meta data of a certain course.
     * @param  int $lectureID - the unique id of the lecture
     * @return an array of objects of LectureMeta which contains all meta 
     *         data of the course.
     */
    public function getMetaData($lectureID)
    {
        $resultSet     = $this->tableGateway->select(
            array( 
                'lecture_id' => $lectureID,
            )
        );
        return $resultSet;
    }
}