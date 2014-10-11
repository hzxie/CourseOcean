<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 个人用户类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class PersonTable
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
     * 使用用户的唯一标识符获取个人用户对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 一个个人用户对象
     */
    public function getPersonUsingUid($uid)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($uid) {
            $select->join('itp_positions', 
                          'itp_people.person_position_id = itp_positions.position_id');
            $select->where->equalTo('itp_people.uid', $uid);
        });
        return $rowSet->current();
    }

    /**
     * 创建一个新个人用户.
     * @param  Array $person - 一个包含个人用户信息的数组
     * @return 操作是否成功完成
     */
    public function createPerson($person)
    {
        $this->tableGateway->insert($person);
        return true;
    }

    /**
     * 更新个人用户的信息.
     * @param  Array $person - 一个包含讲师信息的数组
     * @return 操作是否成功完成
     */
    public function updatePerson($person)
    {
        $this->tableGateway->update($person, array(
            'uid'   => $person['uid'],
        ));
        return true;
    }

    /**
     * 通过用户唯一标识符删除个人用户对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 操作是否成功完成
     */
    public function deletePerson($uid)
    {
        $this->tableGateway->delete(array(
            'uid'   => $uid,
        ));
        return true;
    }
}