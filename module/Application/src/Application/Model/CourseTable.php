<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
     * CourseTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 获取某个类别中课程的数量.
     * @param  int $categoryId - 课程类别的唯一标识符
     * @return 课程数量
     */
    public function getCountUsingCategory($categoryId = 0, $hideUnchecked = true)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $hideUnchecked) {
            if ( $categoryId != 0 ) {
                $select->where->equalTo('course_type_id', $categoryId);
            }
            if ( $hideUnchecked ) {
                $select->join('itp_users', 
                              'itp_users.uid = itp_courses.teacher_id');
                $select->where->equalTo('is_approved', true);
            }
        });
        return $resultSet->count();
    }

    /**
     * 获取某个讲师所开设课程的数量
     * @param  int $teacherId - 讲师的用户唯一标识符
     * @return 课程数量
     */
    public function getCountUsingTeacherId($teacherId)
    {
        $resultSet = $this->tableGateway->select(array(
            'teacher_id'    => $teacherId,
        ));
        return $resultSet->count();
    }

    /**
     * 使用课程的唯一标识符获取课程对象.
     * @param  int $courseId - 课程的唯一标识符
     * @return 一个课程对象
     */
    public function getCourseUsingCourseId($courseId)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($courseId) {
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->where->equalTo('course_id', $courseId);
            $select->order('course_id DESC');
        });
        return $rowSet->current();
    }

    /**
     * 使用课程的课程类别获取课程对象.
     * @param  int $categoryId - 课程类别的唯一标识符
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Course对象
     */
    public function getCoursesUsingCategory($categoryId, $offset, $limit, $hideUnchecked = true)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $offset, $limit, $hideUnchecked) {
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->join('itp_teachers', 
                          'itp_courses.teacher_id = itp_teachers.uid');
            if ( $categoryId != 0 ) {
                $select->where->equalTo('itp_courses.course_type_id', $categoryId);
            }
            if ( $hideUnchecked ) {
                $select->join('itp_users', 
                              'itp_users.uid = itp_courses.teacher_id');
                $select->where->equalTo('is_approved', true);
            }
            $select->order('course_id DESC');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * 使用用户的唯一标识符获取课程对象.
     * @param  int $teacherId - 讲师用户的唯一标识符
     * @return 一个ResultSet对象, 包含若干个Course对象
     */
    public function getCoursesUsingTeacherId($teacherId, $offset = 0, $limit = 0)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($teacherId, $offset, $limit) {
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->where->equalTo('teacher_id', $teacherId);
            $select->order('course_id DESC');
            $select->offset($offset);
            if ( $limit != 0 ) {
                $select->limit($limit);
            }
        });
        return $resultSet;
    }

    /**
     * 通过关键词查找课程.
     * @param  String $keyword - 关键词
     * @param  int    $offset  - 查询结果的Offset
     * @param  int    $limit   - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Course对象
     */
    public function getCourseUsingKeyword($keyword, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($keyword, $offset, $limit) {
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            $select->join('itp_teachers', 
                          'itp_courses.teacher_id = itp_teachers.uid');
            $select->where->like('itp_courses.course_name', "%$keyword%");
            $select->order('course_id DESC');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }
    
    /**
     * 创建一个新课程.
     * @param  Array $course - 一个包含课程信息的数组
     * @return 操作是否成功完成
     */
    public function createCourse($course)
    {
        $this->tableGateway->insert($course);
        return $this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
    }

    /**
     * 更新课程对象的信息.
     * @param  Array $course - 一个包含课程信息的数组
     * @return 操作是否成功完成
     */
    public function updateCourse($course)
    {
        $this->tableGateway->update($course, array(
            'course_id' => $course['course_id'],
        ));
        return true;
    }

    /**
     * 通过课程唯一标识符删除课程对象.
     * @param  int $courseId - 课程的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteCourse($courseId)
    {
        $this->tableGateway->delete(array(
            'course_id' => $courseId,
        ));
        return true;
    }
}