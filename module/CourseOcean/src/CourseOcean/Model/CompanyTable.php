<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 企业用户类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class CompanyTable
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
     * CompanyTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 使用用户的唯一标识符获取企业用户对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 一个企业用户对象
     */
    public function getCompanyUsingUid($uid)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($uid) {
            $select->join('co_company_fields', 
                          'co_companies.company_field_id = co_company_fields.company_field_id');
            $select->where->equalTo('co_companies.uid', $uid);
        });
        return $rowSet->current();
    }

    /**
     * 创建一个新企业用户.
     * @param  Array $company - 一个包含企业用户信息的数组
     * @return 操作是否成功完成
     */
    public function createCompany($company)
    {
        $this->tableGateway->insert($company);
        return true;
    }

    /**
     * 更新企业用户的信息.
     * @param  Array $company - 一个包含企业信息的数组
     * @return 操作是否成功完成
     */
    public function updateCompany($company)
    {
        $this->tableGateway->update($company, array(
            'uid'   => $company['uid'],
        ));
        return true;
    }

    /**
     * 通过用户唯一标识符删除企业用户对象.
     * @param  int $uid - 用户的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteCompany($uid)
    {
        $this->tableGateway->delete(array(
            'uid'   => $uid,
        ));
        return true;
    }
}