<?php

namespace CourseOcean\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程类型类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class CourseTypeTable
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
	 * CourseTypeTable类的构造函数.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

    /**
     * 获取所有课程类别的信息.
     * @return 一个ResultSet对象, 包含若干个CourseType对象.
     */
    public function getAllCourseTypes()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * 通过课程类型的唯一英文缩写获取课程类型对象.
     * @param  String $catelogySlug - 课程类型的唯一英文缩写
     * @return 一个课程类型的对象
     */
    public function getCatelogyUsingSlug($courseTypeSlug)
    {
        $rowSet = $this->tableGateway->select(array(
            'course_type_slug'  => $courseTypeSlug
        ));
        return $rowSet->current();
    }

    /**
     * 创建一个新的课程类型.
     * @param  Array $courseType - 一个包含课程类型信息的数组
     * @return 操作是否成功完成
     */
    public function createCourseType($courseType)
    {
        $this->tableGateway->insert($courseType);
        return true;
    }

    /**
     * 更新课程类型对象的信息.
     * @param  Array $courseType - 一个包含用户信息的数组
     * @return 操作是否成功完成
     */
    public function updateCourseType($courseType)
    {
        $this->tableGateway->update($courseType, array(
            'course_type_id'   => $courseType['course_type_id'],
        ));
        return true;
    }

    /**
     * 通过课程类型的唯一标识符删除课程类型对象.
     * @param  int $courseTypeId - 用户的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteCourseType($courseTypeId)
    {
        $this->tableGateway->delete(array(
            'course_type_id'   => $courseTypeId,
        ));
        return true;
    }
}