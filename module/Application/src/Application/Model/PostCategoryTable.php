<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * 培训动态分类类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class PostCategoryTable
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
	 * PostCategoryTable类的构造函数.
	 * @param TableGateway $tableGateway 
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

    /**
     * 获取所有课程类别的信息.
     * @return 一个ResultSet对象, 包含若干个PostCategory对象.
     */
    public function getAllPostCategories()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     * 通过培训动态分类的唯一英文缩写获取培训动态分类对象.
     * @param  String $catelogySlug - 培训动态分类的唯一英文缩写
     * @return 一个培训动态分类的对象
     */
    public function getCatelogyUsingSlug($postCategorySlug)
    {
        $rowSet = $this->tableGateway->select(array(
            'post_category_slug'    => $postCategorySlug
        ));
        return $rowSet->current();
    }

    /**
     * 创建一个新的培训动态分类.
     * @param  Array $postCategory - 一个包含培训动态分类信息的数组
     * @return 操作是否成功完成
     */
    public function createPostCategory($postCategory)
    {
        $this->tableGateway->insert($postCategory);
        return true;
    }

    /**
     * 更新培训动态分类对象的信息.
     * @param  Array $postCategory - 一个包含用户信息的数组
     * @return 操作是否成功完成
     */
    public function updatePostCategory($postCategory)
    {
        $this->tableGateway->update($postCategory, array(
            'post_category_id'  => $postCategory['post_category_id'],
        ));
        return true;
    }

    /**
     * 通过培训动态分类的唯一标识符删除培训动态分类对象.
     * @param  int $postCategoryId - 用户的唯一标识符
     * @return 操作是否成功完成
     */
    public function deletePostCategory($postCategoryId)
    {
        $this->tableGateway->delete(array(
            'post_category_id'  => $postCategoryId,
        ));
        return true;
    }
}