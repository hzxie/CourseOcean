<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 用户组类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class UserGroupTable
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
     * 获取所有用户的信息.
     * @return 一个ResultSet对象, 包含若干个User对象.
     */
    public function getAllUserGroups()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * 使用用户的唯一标识符获取用户组对象.
     * @param  int $userGroupId - 用户组的唯一标识符
     * @return 一个用户对象
     */
	public function getUserGroupUsingId($userGroupId)
    {
        $rowSet = $this->tableGateway->select(array(
            'user_group_id'     => $userGroupId,
        ));
        return $rowSet->current();
    }
    
    /**
     * 使用用户的唯一标识符获取用户组对象.
     * @param  int $userGroupSlug - 用户组的唯一英文缩写
     * @return 一个用户对象
     */
    public function getUserGroupUsingSlug($userGroupSlug)
    {
        $rowSet = $this->tableGateway->select(array(
            'user_group_slug'   => $userGroupSlug,
        ));
        return $rowSet->current();
    }
}