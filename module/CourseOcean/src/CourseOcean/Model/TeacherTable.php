<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 讲师用户类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class TeacherTable
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
     * TeacherTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 获取某个授课领域中讲师的数量.
     * @param  int $categoryId - 授课领域的唯一标识符
     * @return 讲师数量
     */
    public function getCount($categoryId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId) {
            if ( $categoryId != 0 ) {
                $select->join('co_teaching_fields', 
                              'co_teachers.uid = co_teaching_fields.teacher_id');
                $select->where->equalTo('course_type_id', $categoryId);
            }
            $select->join('co_users', 
                          'co_users.uid = co_teachers.uid');
            $select->where->equalTo('is_approved', true);
        });
        return $resultSet->count();
    }

    /**
     * 获取所有讲师的信息.
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Teacher对象
     */
    public function getAllTeachers($offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->columns(array(
                'uid'               => 'uid', 
                'teacher_name'      => 'teacher_name', 
                'teacher_avatar'    => 'teacher_avatar',
                'teacher_company'   => 'teacher_company',
                'teaching_field'    => new Expression("GROUP_CONCAT(`course_type_name` SEPARATOR ', ')"),
            ));
            $select->join('co_users', 
                          'co_users.uid = co_teachers.uid');
            $select->join('co_teaching_fields', 
                          'co_teachers.uid = co_teaching_fields.teacher_id');
            $select->join('co_course_types', 
                          'co_teaching_fields.course_type_id = co_course_types.course_type_id');
            $select->order(new Expression('CONVERT(teacher_name USING GBK)'));
            $select->group('co_teachers.uid');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * 使用用户的唯一标识符获取讲师对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 一个讲师对象
     */
    public function getTeacherUsingUid($uid)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($uid) {
            $select->columns(array(
                'uid'                   => 'uid', 
                'teacher_name'          => 'teacher_name', 
                'teacher_brief'         => 'teacher_brief',
                'teacher_avatar'        => 'teacher_avatar',
                'teacher_region'        => 'teacher_region',
                'teacher_province'      => 'teacher_province',
                'teacher_city'          => 'teacher_city',
                'teacher_company'       => 'teacher_company',
                'teacher_phone'         => 'teacher_phone',
                'teacher_weibo'         => 'teacher_weibo',
                'teaching_field'        => new Expression("GROUP_CONCAT(`course_type_name` SEPARATOR ', ')"),
            ));
            $select->join('co_teaching_fields', 
                          'co_teachers.uid = co_teaching_fields.teacher_id');
            $select->join('co_course_types', 
                          'co_teaching_fields.course_type_id = co_course_types.course_type_id');
            $select->where->equalTo('co_teachers.uid', $uid);
            $select->group('co_teachers.uid');
        });
        return $rowSet->current();
    }

    /**
     * 使用讲师的授课领域获取讲师对象.
     * @param  int $categoryId - 授课领域的唯一标识符
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Teacher对象
     */
    public function getTeachersUsingCategory($categoryId, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $offset, $limit) {
            $select->columns(array(
                'uid'               => 'uid', 
                'teacher_name'      => 'teacher_name', 
                'teacher_company'   => 'teacher_company',
                'teaching_field'    => new Expression("GROUP_CONCAT(`course_type_name` SEPARATOR ', ')"),
            ));
            $select->join('co_teaching_fields', 
                          'co_teachers.uid = co_teaching_fields.teacher_id');
            $select->join('co_course_types', 
                          'co_teaching_fields.course_type_id = co_course_types.course_type_id');

            if ( $categoryId != 0 ) {
                $select->where->equalTo('co_teaching_fields.course_type_id', $categoryId);
            }
            $select->join('co_users', 
                          'co_users.uid = co_teachers.uid');
            $select->where->equalTo('is_approved', true);

            $select->order(new Expression('CONVERT(teacher_name USING GBK)'));
            $select->group('co_teachers.uid');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * 通过关键词查找讲师.
     * @param  String $keyword - 关键词
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Teacher对象
     */
    public function getTeachersUsingKeyword($keyword, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($keyword, $offset, $limit) {
            $select->columns(array(
                'uid'               => 'uid', 
                'teacher_name'      => 'teacher_name', 
                'teacher_company'   => 'teacher_company',
                'teacher_brief'     => 'teacher_brief',
                'teacher_avatar'    => 'teacher_avatar',
                'teaching_field'    => new Expression("GROUP_CONCAT(`course_type_name` SEPARATOR ', ')"),
            ));
            $select->join('co_teaching_fields', 
                          'co_teachers.uid = co_teaching_fields.teacher_id');
            $select->join('co_course_types', 
                          'co_teaching_fields.course_type_id = co_course_types.course_type_id');
            $select->where->like('co_teachers.teacher_name', "%$keyword%");
            $select->group('co_teachers.uid');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }
    
    /**
     * 创建一个新讲师.
     * @param  Array $teacher - 一个包含讲师信息的数组
     * @return 操作是否成功完成
     */
    public function createTeacher($teacher)
    {
        $this->tableGateway->insert($teacher);
        return true;
    }

    /**
     * 更新讲师对象的信息.
     * @param  Array $teacher - 一个包含讲师信息的数组
     * @return 操作是否成功完成
     */
    public function updateTeacher($teacher)
    {
        $this->tableGateway->update($teacher, array(
            'uid'   => $teacher['uid'],
        ));
        return true;
    }

    /**
     * 通过用户唯一标识符删除用户对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteTeacher($uid)
    {
        $this->tableGateway->delete(array(
            'uid'   => $uid,
        ));
        return true;
    }
}