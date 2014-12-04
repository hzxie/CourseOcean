<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 用户类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class UserTable
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
	 * UserTable类的构造函数.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

    /**
     * 获取所有用户的数量.
     * @return 所有用户的数量
     */
    public function getCount()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet->count();
    }

    /**
     * 获取所有用户的信息.
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个User对象.
     */
    public function getAllUsers($offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->join('itp_user_groups', 
                          'itp_users.user_group_id = itp_user_groups.user_group_id');
            $select->join('itp_teachers', 
                          'itp_users.uid = itp_teachers.uid',
                          array('is_approved'),
                          $select::JOIN_LEFT);
            $select->offset($offset);
            $select->limit($limit);
            $select->order('uid DESC');
        });
        return $resultSet;
    }

    /**
     * 使用用户的唯一标识符获取用户对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 一个用户对象
     */
	public function getUserUsingUid($uid)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($uid) {
            $select->join('itp_user_groups', 
                          'itp_users.user_group_id = itp_user_groups.user_group_id');
            $select->where->equalTo('uid', $uid);
        });
        return $rowSet->current();
    }
    
    /**
     * 使用用户名获取用户对象.
     * @param  String $username - 用户名
     * @return 一个用户对象
     */
    public function getUserUsingUsername($username)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($username) {
            $select->join('itp_user_groups', 
                          'itp_users.user_group_id = itp_user_groups.user_group_id');
            $select->where->equalTo('username', $username);
        });
        return $rowSet->current();
    }

    /**
     * 使用电子邮件地址获取用户对象.
     * @param  String $email - 电子邮件地址
     * @return 一个用户对象
     */
    public function getUserUsingEmail($email)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($email) {
            $select->join('itp_user_groups', 
                          'itp_users.user_group_id = itp_user_groups.user_group_id');
            $select->where->equalTo('email', $email);
        });
        return $rowSet->current();
    }

    /**
     * 创建一个新用户.
     * @param  Array $user - 一个包含用户信息的数组
     * @return 操作是否成功完成
     */
    public function createUser($user)
    {
        $this->tableGateway->insert($user);
        return true;
    }

    /**
     * 更新用户对象的信息.
     * @param  Array $user - 一个包含用户信息的数组
     * @return 操作是否成功完成
     */
    public function updateUser($user)
    {
        $this->tableGateway->update($user, array(
            'uid'   => $user['uid'],
        ));
        return true;
    }

    /**
     * 通过用户唯一标识符删除用户对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteUser($uid)
    {
        $this->tableGateway->delete(array(
            'uid'   => $uid,
        ));
        return true;
    }
}