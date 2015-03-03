<?php

namespace CourseOceanTest\Model;

use PHPUnit_Framework_TestCase;
use Zend\Db\ResultSet\ResultSet;

use CourseOcean\Model\User;
use CourseOcean\Model\UserTable;
use CourseOceanTest\ServiceManagerGrabber;

/**
 * 用户DAO的测试类.
 *
 * 谢浩哲 <zjhzxhz@gmail.com>
 */
class UserTableTest extends PHPUnit_Framework_TestCase
{
    /**
     * 自动注入的ServiceManager对象.
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * 待测试的UserTable对象.
     * @var UserTable
     */
    protected  $userTable;

    /**
     * 准备测试环境.
     */
    public function setUp() {
        $serviceManagerGrabber  = new ServiceManagerGrabber();
        $this->serviceManager   = $serviceManagerGrabber->getServiceManager();
        $this->userTable        = $this->serviceManager->get('CourseOcean\Model\UserTable');
    }

    /**
     * 测试用例: 测试getUserUsingUid()方法
     * 测试数据: 使用数据库中存在的用户
     * 测试结果: 返回预期的User对象
     */
    public function testGetUserUsingUidExists()
    {
        $expectedUser = new User();
        $expectedUser->exchangeArray(array(
            'uid'               => 1001,
            'username'          => 'zjhzxhz',
            'email'             => 'zjhzxhz@gmail.com',
            'password'          => md5('zjhzxhz'),
            'is_inspected'      => true,
            'is_approved'       => true,
            'is_email_validated'=> false,
            'user_group_id'     => 1,
            'user_group_slug'   => 'person',
            'user_group_name'   => '个人',
        ));

        $actualUser     = $this->userTable->getUserUsingUid(1001);
     
        $this->assertEquals($expectedUser, $actualUser);
    }

    /**
     * 测试用例: 测试getUserUsingUid()方法
     * 测试数据: 使用数据库中不存在的用户
     * 测试结果: 返回空引用
     */
    public function testGetUserUsingUidNotExists()
    {
        $expectedUser   = NULL;
        $actualUser     = $this->userTable->getUserUsingUid(0);
     
        $this->assertEquals($expectedUser, $actualUser);
    }
}
