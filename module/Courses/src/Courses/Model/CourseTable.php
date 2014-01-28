<?php

namespace Courses\Model;

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

    /**
     * Get all records from the courses table.
     * @return an object which is an instance of ResultSet, which contains
     *         data of all courses.
     */
    public function fetchAll()
    {
        $resultSet  = $this->tableGateway->select(function (Select $select) {
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->join('itp_teacher', 
                          'itp_courses.uid = itp_teacher.uid');
            $select->order('course_id DESC');
        });
        return $resultSet;
    }

    /**
     * Get general information of a certain course.
     * @param  int $courseID - the unique id of the course
     * @return an array which contains general information of a cerain course
     */
    public function getGeneralInfo($courseID)
    {
        $rowset     = $this->tableGateway->select(function (Select $select) use ($courseID) {
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->where->equalTo('course_id', $courseID);
        });
        return $rowset->current();
    }
}