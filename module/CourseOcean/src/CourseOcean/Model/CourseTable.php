<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
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
     * [此方法仅供管理员使用]
     * 根据筛选条件获取某个分类下课程的数量.
     * @param  int  $categoryId    - 课程类别的唯一标识符
     * @param  bool $isPublic      - 是否为公开课(-1表示不启用此筛选项)
     * @param  bool $isUserChecked - 是否显示未审核用户的课程(-1表示不启用此筛选项)
     * @return 获取某个分类下课程的数量
     */
    public function getCountUsingFilters($categoryId, $isPublic, $isUserChecked)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $isPublic, $isUserChecked) {
            if ( $categoryId != 0 ) {
                $select->where->equalTo('course_type_id', $categoryId);
            }
            if ( $isPublic != -1 ) {
                $select->where->equalTo('course_is_public', $isPublic);
            }
            if ( $isUserChecked != -1 ) {
                $select->join('co_users', 
                              'co_users.uid = co_courses.teacher_id');
                $select->where->equalTo('is_approved', $isUserChecked);
            }
        });
        return $resultSet->count();
    }

    /**
     * 获取某个类别中公开课程的数量.
     * @param  int $categoryId - 课程类别的唯一标识符
     * @return 课程数量
     */
    public function getCountUsingCategory($categoryId = 0)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId) {
            $select->join('co_users', 
                          'co_users.uid = co_courses.teacher_id');
            
            if ( $categoryId != 0 ) {
                $select->where->equalTo('course_type_id', $categoryId);
            }
            $select->where->equalTo('is_approved', true);
            $select->where->equalTo('course_is_public', true);
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
            $select->join('co_course_types', 
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->where->equalTo('course_id', $courseId);
            $select->order('course_id DESC');
        });
        return $rowSet->current();
    }

    /**
     * [此方法仅供管理员使用]
     * 使用筛选条件获取课程对象.
     * @param  int  $categoryId    - 课程类别的唯一标识符
     * @param  bool $isPublic      - 是否为公开课(-1表示不启用此筛选项)
     * @param  bool $isUserChecked - 是否显示未审核用户的课程(-1表示不启用此筛选项)
     * @param  int $offset         - 查询结果的Offset
     * @param  int $limit          - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Course对象
     */
    public function getCoursesUsingFilters($categoryId, $isPublic, $isUserChecked, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $isPublic, $isUserChecked, $offset, $limit) {
            $select->join('co_course_types', 
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->join('co_teachers', 
                          'co_courses.teacher_id = co_teachers.uid');

            if ( $categoryId != 0 ) {
                $select->where->equalTo('co_courses.course_type_id', $categoryId);
            }
            if ( $isPublic != -1 ) {
                $select->where->equalTo('course_is_public', $isPublic);
            }
            if ( $isUserChecked != -1 ) {
                $select->join('co_users', 
                              'co_users.uid = co_courses.teacher_id');
                $select->where->equalTo('is_approved', $isUserChecked);
            }

            $select->order('course_id DESC');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * 使用课程的课程类别获取公开课程对象.
     * @param  int $categoryId - 课程类别的唯一标识符
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Course对象
     */
    public function getCoursesUsingCategory($categoryId, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $offset, $limit) {
            $select->join('co_course_types', 
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->join('co_teachers', 
                          'co_courses.teacher_id = co_teachers.uid');
            $select->join('co_users', 
                          'co_users.uid = co_courses.teacher_id');

            if ( $categoryId != 0 ) {
                $select->where->equalTo('co_courses.course_type_id', $categoryId);
            }
            $select->where->equalTo('is_approved', true);
            $select->where->equalTo('course_is_public', true);

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
            $select->join('co_course_types', 
                          'co_courses.course_type_id = co_course_types.course_type_id');
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
    public function getCoursesUsingKeyword($keyword, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($keyword, $offset, $limit) {
            $select->join('co_course_types', 
                          'co_courses.course_type_id = co_course_types.course_type_id');
            $select->join('co_teachers', 
                          'co_courses.teacher_id = co_teachers.uid');

            $select->where->OR->equalTo('co_courses.course_id', $keyword);
            $select->where->OR->like('co_courses.course_name', "%$keyword%");
            $select->where->OR->like('co_teachers.teacher_name', "%$keyword%");
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