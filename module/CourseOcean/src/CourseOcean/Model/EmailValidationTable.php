<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 电子邮件验证类类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class EmailValidationTable
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
	 * CommentTable类的构造函数.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

    /**
     * 通过电子邮件地址获取电子邮件验证凭据.
     * @param  String $email - 电子邮件地址
     * @return 一个电子邮件验证凭据对象或空引用
     */
    public function getConfidentialUsingEmail($email)
    {
        $rowSet = $this->tableGateway->select(array(
            'email'     => $email,
        ));
        return $rowSet->current();
    }

    /**
     * 通过电子邮件地址获取电子邮件验证凭据.
     * @param  String $email   - 电子邮件地址
     * @param  String $keycode - 电子邮件验证凭据
     * @return 一个电子邮件验证凭据对象或空引用
     */
    public function verifyConfidential($email, $keycode)
    {
        $rowSet = $this->tableGateway->select(array(
            'email'     => $email,
            'keycode'   => $keycode,
        ));
        return $rowSet->current();
    }

    /**
     * 创建一个电子邮件验证凭据.
     * @param  Array $emailValidation - 一个电子邮件验证凭据信息的数组
     * @return 操作是否成功完成
     */
    public function createConfidential($emailValidation)
    {
        $this->tableGateway->insert($emailValidation);
        return true;
    }

    /**
     * 通过电子邮件地址删除某个用户的验证凭据.
     * @param  String $email - 用户的电子邮件地址
     * @return 操作是否成功完成
     */
    public function deleteConfidential($email)
    {
        $this->tableGateway->delete(array(
            'email' => $email,
        ));
        return true;
    }
}