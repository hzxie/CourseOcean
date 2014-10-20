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
     * 使用用户的唯一标识符获取培训需求的ResultSet.
     * @param  int $uid         - 用户的唯一标识符
     * @param  bool $isTeacher  - 用户是否为讲师用户
     * @param  int $offset      - 查询结果的Offset
     * @param  int $limit       - 查询返回的记录数
     * @return 一个培训需求对象
     */
    public function getRequirementUsingUid($uid, $isTeacher, $offset, $limit)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($uid, $offset, $limit) {
            $select->join('itp_courses', 
                          'itp_requirements.requirement_course_id = itp_courses.requirement_course_id');
            $select->join('itp_course_types', 
                          'itp_courses.course_type_id = itp_course_types.course_type_id');
            if ( $isTeacher ) {
                $select->where->equalTo('itp_requirements.requirement_to_uid', $uid);
            } else {
                $select->join('itp_teachers', 
                              'itp_requirements.requirement_to_uid = itp_teachers.uid');
                $select->where->equalTo('itp_requirements.requirement_from_uid', $uid);
            }
            $select->offset($offset);
            $select->limit($limit);
        });
        return $rowSet;
    }

    /**
     * 创建一个新培训需求.
     * @param  Array $requirement - 一个包含培训需求信息的数组
     * @return 新创建需求的唯一标识符
     */
    public function createRequirement($requirement)
    {
        $this->tableGateway->insert($requirement);
        return $this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
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