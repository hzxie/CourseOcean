<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程会话类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class LectureTable
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
     * LectureTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 获取某个类别中课程会话的数量.
     * @param  int $categoryId - 课程会话类别的唯一标识符
     * @return 课程会话数量
     */
    public function getCountUsingFilters($categoryId, $startTime, $endTime, $region, $province, $city)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $startTime, $endTime, $region, $province, $city) {
            if ( $categoryId != 0 ) {
                $select->join('co_courses',
                              'co_lectures.course_id = co_courses.course_id');
                $select->where->equalTo('course_type_id', $categoryId);
            }
            if ( $startTime != null ) {
                $select->where->greaterThanOrEqualTo('co_lectures.lecture_start_time', $startTime);
            }
            if ( $endTime != null ) {
                $select->where->lessThanOrEqualTo('co_lectures.lecture_end_time', $endTime);
            }
            if ( $region != null ) {
                $select->where->equalTo('co_lectures.lecture_region', $region);
            }
            if ( $province != null ) {
                $select->where->equalTo('co_lectures.lecture_province', $province);
            }
            if ( $city != null ) {
                $select->where->equalTo('co_lectures.lecture_city', $city);
            }
        });
        return $resultSet->count();
    }

    /**
     * 获取某个讲师用户开设课程会话的数量.
     * @param  int $teacherId - 讲师用户的用户唯一标识符
     * @return 课程会话数量
     */
    public function getCountUsingTeacherId($teacherId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($teacherId) {
            $select->join('co_courses',
                          'co_lectures.course_id = co_courses.course_id');
            $select->where->equalTo('teacher_id', $teacherId);
        });
        return $resultSet->count();
    }

    /**
     * 获取所有课程会话的信息.
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Lecture对象
     */
    public function getAllLectures($offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->join('co_courses',
                          'co_lectures.course_id = co_courses.course_id');
            $select->join('co_course_types',
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->join('co_teachers', 
                          'co_courses.teacher_id = co_teachers.uid');
            $select->offset($offset);
            $select->limit($limit);
            $select->order('lecture_start_time DESC');
        });
        return $resultSet;
    }

    /**
     * 使用课程会话的唯一标识符获取课程会话对象.
     * @param  int $lectureId - 课程会话的唯一标识符
     * @return 一个课程会话对象
     */
    public function getLectureUsingLectureId($lectureId)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($lectureId) {
            $select->join('co_courses',
                          'co_lectures.course_id = co_courses.course_id');
            $select->join('co_course_types',
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->where->equalTo('lecture_id', $lectureId);
        });
        return $rowSet->current();
    }

    /**
     * 使用课程会话的唯一标识符获取课程会话对象.
     * @param  int $courseId - 课程的唯一标识符
     * @return 一个ResultSet对象, 包含若干个Lecture对象
     */
    public function getLecturesUsingCourseId($courseId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($courseId) {
            $select->where->equalTo('course_id', $courseId);
            $select->order('lecture_start_time DESC');
        });
        return $resultSet;
    }

    /**
     * 使用讲师用户的用户唯一标识符获取课程会话对象.
     * @param  int $teacherId - 讲师用户的用户唯一标识符 
     * @param  int $offset    - 查询结果的Offset
     * @param  int $limit     - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Lecture对象
     */
    public function getLecturesUsingTeacherId($teacherId, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($teacherId, $offset, $limit) {
            $select->columns(array(
                '*',
                'participants'    => new Expression("(SELECT COUNT(*) FROM co_lecture_attendance WHERE co_lecture_attendance.lecture_id = co_lectures.lecture_id)"),
            ));
            $select->join('co_courses',
                          'co_lectures.course_id = co_courses.course_id');
            $select->join('co_course_types',
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->where->equalTo('teacher_id', $teacherId);
            $select->offset($offset);
            $select->limit($limit);
            $select->order('lecture_start_time DESC');
        });
        return $resultSet;
    }

    /**
     * 使用课程会话的课程类别获取课程会话对象.
     * @param  int $categoryId - 课程会话类别的唯一标识符
     * @param  int $offset     - 查询结果的Offset
     * @param  int $limit      - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Lecture对象
     */
    public function getLecturesUsingFilters($categoryId, $startTime, $endTime, $region, $province, $city, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $startTime, $endTime, $region, $province, $city, $offset, $limit) {
            $select->join('co_courses',
                          'co_lectures.course_id = co_courses.course_id');
            $select->join('co_course_types',
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->join('co_teachers', 
                          'co_courses.teacher_id = co_teachers.uid');
            if ( $categoryId != 0 ) {
                $select->where->equalTo('co_courses.course_type_id', $categoryId);
            }
            if ( $startTime != null ) {
                $select->where->greaterThanOrEqualTo('co_lectures.lecture_start_time', $startTime);
            }
            if ( $endTime != null ) {
                $select->where->lessThanOrEqualTo('co_lectures.lecture_end_time', $endTime);
            }
            if ( $region != null ) {
                $select->where->equalTo('co_lectures.lecture_region', $region);
            }
            if ( $province != null ) {
                $select->where->equalTo('co_lectures.lecture_province', $province);
            }
            if ( $city != null ) {
                $select->where->equalTo('co_lectures.lecture_city', $city);
            }
            $select->offset($offset);
            $select->limit($limit);
            $select->order('lecture_start_time ASC');
        });
        return $resultSet;
    }
    
    /**
     * 创建一个新课程会话.
     * @param  Array $lecture - 一个包含课程会话信息的数组
     * @return 新创建课程的课程会话唯一标识符
     */
    public function createLecture($lecture)
    {
        $this->tableGateway->insert($lecture);
        return $this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
    }

    /**
     * 更新课程会话对象的信息.
     * @param  Array $lecture - 一个包含课程会话信息的数组
     * @return 操作是否成功完成
     */
    public function updateLecture($lecture)
    {
        $this->tableGateway->update($lecture, array(
            'lecture_id' => $lecture['lecture_id'],
        ));
        return true;
    }

    /**
     * 通过课程会话唯一标识符删除课程会话对象.
     * @param  int $lectureId - 课程会话的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteLecture($lectureId)
    {
        $this->tableGateway->delete(array(
            'lecture_id' => $lectureId,
        ));
        return true;
    }
}