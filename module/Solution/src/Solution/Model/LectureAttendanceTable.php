<?php

namespace Solution\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the lecture attendance table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class LectureAttendanceTable
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
     * Get attendance records for a certain user from the lectures attendance 
     * table by pagination.
     * @param  int $offset - current number of the records gotten
     * @param  int $limit - max number of lectures in a page
     * @return an object which is an instance of ResultSet, which contains
     *         data of all lectures.
     */
    public function getAttendaceRecordsOfUser($uid, $offset, $limit)
    {
        $resultSet  = $this->tableGateway->select(function (Select $select) use ($uid, $offset, $limit) {
            $select->join('itp_lectures',
                          'itp_lecture_attendance.lecture_id = itp_lectures.lecture_id');
            $select->join('itp_courses',
                          'itp_lectures.course_id = itp_courses.course_id');
            $select->join('itp_course_types',
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->join('itp_teachers', 
                          'itp_courses.uid = itp_teachers.uid');
            $select->order('itp_lecture_attendance.lecture_id DESC');
            $select->where("itp_lecture_attendance.uid = $uid");
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * Check if the user has attended the lecture.
     * @param  int $uid - the unique id of the user
     * @param  int $lectureID - the unique id of the lecture
     * @return true if the user has attended the lecture
     */
    public function isAttended($uid, $lectureID)
    {
        $rowset     = $this->tableGateway->select(
            array(
                'uid'           => $uid,
                'lecture_id'    => $lectureID,
            )
        );
        return $rowset->current();
    }

    /**
     * Handle asynchronous attending lecture requests for the users.
     * @param  Array $attendanceRecord - an array contains ensential data 
     *         as a lecture attendance record
     * @return true if the attendance operation is successful
     */
    public function attendLecture($attendanceRecord)
    {
        $this->tableGateway->insert($attendanceRecord);
        return true;
    }
}