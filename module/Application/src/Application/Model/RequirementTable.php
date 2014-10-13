<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 需求类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
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
     * TeacherTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 使用用户的唯一标识符获取培训需求对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 一个培训需求对象
     */
    public function getRequirementUsingUid($uid)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($uid) {
            $select->where->equalTo('itp_companies.uid', $uid);
        });
        return $rowSet->current();
    }

    /**
     * 创建一个新培训需求.
     * @param  Array $requirement - 一个包含培训需求信息的数组
     * @return 操作是否成功完成
     */
    public function createRequirement($requirement)
    {
        $this->tableGateway->insert($requirement);
        return true;
    }

    /**
     * 更新培训需求的信息.
     * @param  Array $requirement - 一个包含培训需求信息的数组
     * @return 操作是否成功完成
     */
    public function updateRequirement($requirement)
    {
        $this->tableGateway->update($requirement, array(
            'requirement_id'    => $requirement['requirement_id'],
        ));
        return true;
    }

    /**
     * 通过用户唯一标识符删除培训需求对象.
     * @param  int $requirementId - 培训需求的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteRequirement($requirementId)
    {
        $this->tableGateway->delete(array(
            'requirement_id'    => $requirementId,
        ));
        return true;
    }
}