<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * 系统设置选项类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class OptionTable {
    /**
     * The Table Gateway object is intended to provide an object that 
     * represents a table in a database, and the methods of this object 
     * mirror the most common operations on a database table.
     * 
     * @var TableGatewayInterface
     */
    protected $tableGateway;

    /**
     * OptionTable类的构造函数.
     * @param TableGatewayInterface $tableGateway 
     */
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 获取所有系统设置选项的对象.
     * @return 一个ResultSet对象, 包含若干个Option对象
     */
    public function getAllOptions() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * 使用系统设置选项的键获取系统设置选项对象.
     * @param  String $optionKey - 系统设置选项的Key
     * @return 一个系统设置选项的对象
     */
    public function getOptionUsingKey($optionKey) {
        $rowSet = $this->tableGateway->select(array(
            'option_key'    => $optionKey,
        ));
        return $rowSet->current();
    }

    /**
     * 创建一个新的系统设置选项.
     * @param  Array $option - 一个包含系统设置选项信息的数组
     * @return 操作是否成功完成
     */
    public function createOption($option) {
        $this->tableGateway->insert($option);
        return true;
    }

    /**
     * 更新系统设置选项的信息.
     * @param  Array $option - 一个包含系统设置选项信息的数组
     * @return 操作是否成功完成
     */
    public function updateOption($option) {
        $this->tableGateway->update($option, array(
            'option_id'   => $option['option_id'],
        ));
        return true;
    }

    /**
     * 通过系统设置选项的唯一标识符删除系统设置选项对象.
     * @param  int $optionId - 系统设置选项的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteOption($optionId) {
        $this->tableGateway->delete(array(
            'option_id'   => $option_id,
        ));
        return true;
    }
}