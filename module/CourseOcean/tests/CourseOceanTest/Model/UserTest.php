<?php
namespace CourseOcean\Model;
namespace CourseOceanTest\Module;

use PHPUnit_Framework_TestCase;

use CourseOcean\Model\User;

/**
 * 用户Model的测试类.
 *
 * 谢浩哲 <zjhzxhz@gmail.com>
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * 测试用例: 测试User类的构造函数.
     * 测试数据: N/a.
     * 测试结果: 返回一个各项属性均为null的User对象.
     */
    public function testUserInitialState()
    {
        $user = new User();

        $this->assertNull($user->uid);
        $this->assertNull($user->username);
        $this->assertNull($user->email);
        $this->assertNull($user->password);
        $this->assertNull($user->userGroupId);
        $this->assertNull($user->userGroupSlug);
        $this->assertNull($user->userGroupName);
    }

    /**
     * 测试用例: 测试User类的exchangeArray函数.
     * 测试数据: 使用合法的数据进行测试.
     * 预期结果: 使用所提供的数据对User的各属性进行赋值.
     */
    public function testExchangeArraySetsPropertiesCorrectly()
    {
        $user   = new User();
        $data   = array(
            'uid'               => 1,
            'username'          => 'zjhzxhz',
            'email'             => 'zjhzxhz@gmail.com',
            'password'          => md5('zjhzxhz'),
            'user_group_id'     => 1,
            'user_group_slug'   => 'person',
            'user_group_name'   => '个人用户',
        );

        $user->exchangeArray($data);

        $this->assertSame($data['uid'], $user->uid);
        $this->assertSame($data['username'], $user->username);
        $this->assertSame($data['email'], $user->email);
        $this->assertSame($data['password'], $user->password);
        $this->assertSame($data['user_group_id'], $user->userGroupId);
        $this->assertSame($data['user_group_slug'], $user->userGroupSlug);
        $this->assertSame($data['user_group_name'], $user->userGroupName);
    }
}
