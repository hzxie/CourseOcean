<?php

namespace Solution\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the course_meta table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class CourseMetaTable
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
     * @param  int $courseID - the unique id of the course
     * @return an array of objects of CourseMeta which contains all meta 
     *         data of the course.
     */
    public function getMetaData($courseID)
    {
        $resultSet     = $this->tableGateway->select(
            array( 
                'course_id' => $courseID,
            )
        );
        return $resultSet;
    }
}