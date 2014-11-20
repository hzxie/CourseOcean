<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 培训动态类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class PostTable
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
     * PostTable类的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 获取某个分类中培训动态的数量.
     * @param  int $categoryId - 培训动态分类的唯一标识符
     * @return 培训动态的数量
     */
    public function getCount($categoryId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId) {
            if ( $categoryId != 0 ) {
                $select->join('itp_post_categories', 
                              'itp_posts.post_category_id = itp_post_categories.post_category_id');
                $select->where->equalTo('post_category_id', $categoryId);
            }
        });
        return $resultSet->count();
    }

    /**
     * 获取所有培训动态的信息.
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Post对象.
     */
    public function getAllPosts($offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->join('itp_post_categories', 
                          'itp_posts.post_category_id = itp_post_categories.post_category_id');
            $select->order('post_id DESC');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * 使用培训动态的唯一标识符获取培训动态对象.
     * @param  int $postId - 培训动态的唯一标识符
     * @return 一个培训动态对象
     */
    public function getPostUsingPostId($postId)
    {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($postId) {
            $select->join('itp_post_categories', 
                          'itp_posts.post_category_id = itp_post_categories.post_category_id');
            $select->where->equalTo('post_id', $postId);
        });
        return $rowSet->current();
    }

    /**
     * 使用培训动态的类别获取培训动态对象.
     * @param  int $categoryId - 培训动态类别的唯一标识符
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Post对象
     */
    public function getPostsUsingCategory($categoryId, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $offset, $limit) {
            $select->join('itp_post_categories', 
                          'itp_posts.post_category_id = itp_post_categories.post_category_id');
       
            if ( $categoryId != 0 ) {
                $select->where->equalTo('itp_posts.post_category_id', $categoryId);
            }
            $select->offset($offset);
            $select->limit($limit);
            $select->order('post_id DESC');
        });
        return $resultSet;
    }
    
    /**
     * 创建一个新培训动态.
     * @param  Array $post - 一个包含培训动态信息的数组
     * @return 操作是否成功完成
     */
    public function createPost($post)
    {
        $this->tableGateway->insert($post);
        return true;
    }

    /**
     * 更新培训动态对象的信息.
     * @param  Array $post - 一个包含培训动态信息的数组
     * @return 操作是否成功完成
     */
    public function updatePost($post)
    {
        $this->tableGateway->update($post, array(
            'post_id'   => $post['post_id'],
        ));
        return true;
    }

    /**
     * 通过培训动态唯一标识符删除培训动态对象.
     * @param  int $postId - 培训动态的唯一标识符
     * @return 操作是否成功完成
     */
    public function deletePost($postId)
    {
        $this->tableGateway->delete(array(
            'post_id'   => $postId,
        ));
        return true;
    }
}