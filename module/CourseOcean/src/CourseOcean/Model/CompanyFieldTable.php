<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 公司领域类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class CompanyFieldTable
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
	 * CompanyFieldTable类的构造函数.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

    /**
     * 获取所有公司领域的信息.
     * @return 一个ResultSet对象, 包含若干个User对象.
     */
    public function getAllCompanyFields()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * 使用公司领域的唯一标识符获取公司领域的对象.
     * @param  int $companyFieldId - 公司领域的唯一标识符
     * @return 一个公司领域对象
     */
	public function getCompanyFieldUsingId($companyFieldId)
    {
        $rowSet = $this->tableGateway->select(array(
            'company_field_id'      => $companyFieldId,
        ));
        return $rowSet->current();
    }
    
    /**
     * 使用公司领域的唯一标识符获取公司领域对象.
     * @param  int $companyFieldSlug - 公司领域的唯一英文缩写
     * @return 一个公司领域对象
     */
    public function getCompanyFieldUsingSlug($companyFieldSlug)
    {
        $rowSet = $this->tableGateway->select(array(
            'company_field_slug'    => $companyFieldSlug,
        ));
        return $rowSet->current();
    }

    /**
     * 创建一个新的公司领域.
     * @param  Array $companyField - 一个包含公司领域信息的数组
     * @return 操作是否成功完成
     */
    public function createCompanyField($companyField)
    {
        $this->tableGateway->insert($companyField);
        return true;
    }

    /**
     * 更新公司领域对象的信息.
     * @param  Array $companyField - 一个包含公司领域信息的数组
     * @return 操作是否成功完成
     */
    public function updateCompanyField($companyField)
    {
        $this->tableGateway->update($companyField, array(
            'company_field_id'  => $companyField['company_field_id'],
        ));
        return true;
    }

    /**
     * 通过公司领域的唯一标识符删除公司领域对象.
     * @param  int $companyFieldId - 公司领域的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteCompanyField($companyFieldId)
    {
        $this->tableGateway->delete(array(
            'company_field_id'  => $companyFieldId,
        ));
        return true;
    }
}