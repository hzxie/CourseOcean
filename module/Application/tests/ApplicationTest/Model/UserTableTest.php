<?php
namespace Application\Model;
namespace ApplicationTest\Module;

use PHPUnit_Framework_TestCase;
use Zend\Db\ResultSet\ResultSet;

use Application\Model\User;
use Application\Model\UserTable;

/**
 * 用户DAO的测试类.
 *
 * 谢浩哲 <zjhzxhz@gmail.com>
 */
class UserTableTest extends PHPUnit_Framework_TestCase
{
    /**
     * 测试用例: 
     * 测试数据: 
     * 测试结果: 
     */
    public function testGetUserUsingUidExists()
    {
        $user = new User();
        $user->exchangeArray(array(
            'uid'               => 1001,
            'username'          => 'zjhzxhz',
            'email'             => 'zjhzxhz@gmail.com',
            'password'          => md5('zjhzxhz'),
            'user_group_id'     => 1,
            'user_group_slug'   => 'person',
            'user_group_name'   => '个人用户',
        ));

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new User());
        $resultSet->initialize(array($user));
        
        $mockTableGateway   = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('select'),
            array(),
            '',
            false
        );
        // $mockTableGateway->expects($this->once())
        //                  ->method('select')
        //                  ->with(array('uid' => 1001))
        //                  ->will($this->returnValue($resultSet));
        // $UserTable = new UserTable($mockTableGateway);
        // $this->assertSame($user, $UserTable->getUserUsingUid(1001));
    }
}
