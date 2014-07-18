<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程会话类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
    public function getCount($categoryId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId) {
            if ( $categoryId != 0 ) {
                $select->join('itp_courses',
                              'itp_lectures.course_id = itp_courses.course_id');
                $select->where->equalTo('course_type_id', $categoryId);
            }
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
            $select->join('itp_courses',
                          'itp_lectures.course_id = itp_courses.course_id');
            $select->join('itp_course_types',
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->join('itp_teachers', 
                          'itp_courses.teacher_id = itp_teachers.uid');
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
            $select->join('itp_courses',
                          'itp_lectures.course_id = itp_courses.course_id');
            $select->join('itp_course_types',
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->where->equalTo('lecture_id', $lectureId);
        });
        return $rowSet->current();
    }

    /**
     * 使用课程会话的唯一标识符获取课程会话对象.
     * @param  int $courseId - 课程的唯一标识符
     * @return 一个ResultSet对象, 包含若干个Lecture对象
     */
    public function getLectureUsingCourseId($courseId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($courseId) {
            $select->where->equalTo('course_id', $courseId);
            $select->order('lecture_start_time DESC');
        });
        return $resultSet;
    }

    /**
     * @todo   添加对时间的筛选
     * 使用课程会话的课程类别获取课程会话对象.
     * @param  int $categoryId - 课程会话类别的唯一标识符
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Lecture对象
     */
    public function getLecturesUsingCategory($categoryId, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $offset, $limit) {
            $select->join('itp_courses',
                          'itp_lectures.course_id = itp_courses.course_id');
            $select->join('itp_course_types',
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->join('itp_teachers', 
                          'itp_courses.teacher_id = itp_teachers.uid');
            $select->where->equalTo('itp_courses.course_type_id', $categoryId);
            $select->offset($offset);
            $select->limit($limit);
            $select->order('lecture_start_time DESC');
        });
        return $resultSet;
    }
    
    /**
     * 创建一个新课程会话.
     * @param  Array $lecture - 一个包含课程会话信息的数组
     * @return 操作是否成功完成
     */
    public function createLecture($lecture)
    {
        $this->tableGateway->insert($lecture);
        return true;
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