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
     * [此方法仅供管理员使用]
     * 获取所有用户的数量.
     * @param  int $userGroupId - 用户组的唯一标识符
     * @param  int $isInspected - 用户是否已审核(-1表示不启用此筛选项)
     * @param  int $isApproved  - 用户是否已通过审核(-1表示不启用此筛选项)
     * @return 符合筛选条件用户的数量
     */
    public function getCountUsingFilters($userGroupId = 0, $isInspected = -1, $isApproved = -1)
    {
        $resultSet = $this->tableGateway->select(function(Select $select) use ($userGroupId, $isInspected, $isApproved) {
            if ( $userGroupId != 0 ) {
                $select->where->equalTo('user_group_id', $userGroupId);
            }
            if ( $isInspected != -1 ) {
                $select->where->equalTo('is_inspected', $isInspected);
            }
            if ( $isApproved != -1 ) {
                $select->where->equalTo('is_approved', $isApproved);
            }
        });
        return $resultSet->count();
    }

    /**
     * [此方法仅供管理员使用]
     * 根据筛选条件获取用户的信息.
     * @param  int $userGroupId - 用户组的唯一标识符
     * @param  int $isInspected - 用户是否已审核(-1表示不启用此筛选项)
     * @param  int $isApproved  - 用户是否已通过审核(-1表示不启用此筛选项)
     * @param  int $offset      - 查询结果的Offset
     * @param  int $limit       - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个User对象.
     */
    public function getUsersUsingFilters($userGroupId, $isInspected, $isApproved, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($userGroupId, $isInspected, $isApproved, $offset, $limit) {
            $select->join('itp_user_groups', 
                          'itp_users.user_group_id = itp_user_groups.user_group_id');
            if ( $userGroupId != 0 ) {
                $select->where->equalTo('itp_users.user_group_id', $userGroupId);
            }
            if ( $isInspected != -1 ) {
                $select->where->equalTo('is_inspected', $isInspected);
            }
            if ( $isApproved != -1 ) {
                $select->where->equalTo('is_approved', $isApproved);
            }
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
     * [此方法仅供管理员使用]
     * 根据关键字获取用户的信息.
     * @param  String $keyword - 关键字
     * @return 一个ResultSet对象, 包含若干个User对象.
     */
    public function getUserUsingKeyword($keyword, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($keyword, $offset, $limit) {
            $select->join('itp_user_groups', 
                          'itp_users.user_group_id = itp_user_groups.user_group_id');
            $select->where->OR->equalTo('itp_users.uid', "$keyword");
            $select->where->OR->like('itp_users.username', "%$keyword%");
            $select->where->OR->like('itp_users.email', "%$keyword%");
        });
        return $resultSet;
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
     * 将用户的审核状态设置为待审核.
     * @param  int $uid - 用户的唯一标识符
     * @return 操作是否成功完成
     */
    public function pendingUser($uid)
    {
        $user       = array(
            'uid'           => $uid,
            'is_inspected'  => false,
            'is_approved'   => false,
        );

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