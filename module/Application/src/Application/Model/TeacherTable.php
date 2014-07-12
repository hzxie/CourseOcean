<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 讲师用户类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
                $select->join('itp_teaching_field', 
                              'itp_teachers.uid = itp_teaching_field.teacher_id');
                $select->where->equalTo('course_type_id', $categoryId);
            }
            $select->where->equalTo('teacher_is_approved', true);
        });
        return $resultSet->count();
    }

    /**
     * 获取所有用户的信息.
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Teacher对象
     */
    public function getAllApprovedTeachers($offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->where->equalTo('teacher_is_approved', true);
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
            $select->where->equalTo('uid', $uid);
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
            $select->join('itp_teaching_field', 
                          'itp_teachers.uid = itp_teaching_field.teacher_id');
            $select->where->equalTo('teacher_is_approved', true);
            $select->where->equalTo('course_type_id', $categoryId);
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