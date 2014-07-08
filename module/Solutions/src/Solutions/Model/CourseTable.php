<?php

namespace Solutions\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the courses table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class CourseTable
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

    
    public function getCoursesOfLecture($lectureID)
    {
        $resultSet  = $this->tableGateway->select(function (Select $select) use ($lectureID) {
            $select->join('itp_lecture_courses', 
                          'itp_courses.course_id = itp_lecture_courses.course_id');
            $select->where->equalTo('lecture_id', $lectureID);
        });
        return $resultSet;
    }
}