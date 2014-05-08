<?php

namespace Solutions\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 需求表的类. 提供与数据表相关操作.
 * 与数据库中的itp_requirements相关联.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class RequirementTable
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
     * RequirementTable的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 在数据库中创建一个新的需求.
     * @param  Array $requirement - 一个包含需求的必要信息的数组
     * @return  数据库操作是否成功
     */
    public function createRequirement($requirement)
    {
        $this->tableGateway->insert($requirement);
        return true;
    }
}