<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * 工作职位类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class PositionTable
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
	 * PositionTable类的构造函数.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

    /**
     * 获取所有工作职位的信息.
     * @return 一个ResultSet对象, 包含若干个Position对象.
     */
    public function getAllPositions()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * 通过工作职位的唯一英文缩写获取工作职位对象.
     * @param  String $positionSlug - 工作职位的唯一英文缩写
     * @return 一个工作职位的对象
     */
    public function getPositionUsingSlug($positionSlug)
    {
        $rowSet = $this->tableGateway->select(array(
            'position_slug'    => $positionSlug
        ));
        return $rowSet->current();
    }

    /**
     * 创建一个新的工作职位.
     * @param  Array $position - 一个包含工作职位信息的数组
     * @return 操作是否成功完成
     */
    public function createPosition($position)
    {
        $this->tableGateway->insert($position);
        return true;
    }

    /**
     * 更新工作职位对象的信息.
     * @param  Array $position - 一个包含用户信息的数组
     * @return 操作是否成功完成
     */
    public function updatePosition($position)
    {
        $this->tableGateway->update($position, array(
            'position_id'   => $position['position_id'],
        ));
        return true;
    }

    /**
     * 通过工作职位的唯一标识符删除工作职位对象.
     * @param  int $positionId - 工作职位的唯一标识符
     * @return 操作是否成功完成
     */
    public function deletePosition($positionId)
    {
        $this->tableGateway->delete(array(
            'position_id'   => $positionId,
        ));
        return true;
    }
}