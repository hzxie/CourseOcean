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
     * Get all records from the courses table by pagination.
     * @param  int $pageNumber - current number of the page
     * @param  int $limit - max number of courses in a page
     * @return an object which is an instance of ResultSet, which contains
     *         data of all courses.
     */
    public function fetchAll($pageNumber, $limit)
    {
        $offset     = ( $pageNumber - 1 ) * $limit;
        $resultSet  = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->join('itp_teacher', 
                          'itp_courses.uid = itp_teacher.uid');
            $select->order('course_id DESC');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * Get number of records in the courses table.
     * @return an integer which stands for the number of records in the course
     *         table
     */
    public function getNumberOfCourses()
    {
        return $this->tableGateway->select()->count();
    }

    /**
     * Get general information of a certain course.
     * @param  int $courseID - the unique id of the course
     * @return an object of Course which contains general information of a cerain 
     *         course
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

    /**
     * Get a list of courses which are powered by the teacher.
     * @param  int $uid - the unique id of the user
     * @return an array of objects of Course which contains general information of
     *         the teacher
     */
    public function getCourseOfTeacher($uid)
    {
        $resultSet  = $this->tableGateway->select(
            array( 'uid' => $uid )
        );
        return $resultSet;
    }
}