<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * 培训动态类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class PostTable {
    /**
     * The Table Gateway object is intended to provide an object that 
     * represents a table in a database, and the methods of this object 
     * mirror the most common operations on a database table.
     * 
     * @var TableGatewayInterface
     */
    protected $tableGateway;

    /**
     * PostTable类的构造函数.
     * @param TableGatewayInterface $tableGateway 
     */
    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * [此方法仅供管理员使用]
     * 使用筛选条件获取满足筛选条件的培训动态.
     * @param  int $categoryId - 培训动态分类的唯一标识符
     * @param  $publishTime    - 培训动态的发布时间(-1表示不启用此筛选项)
     * @return 培训动态的数量
     */
    public function getCountUsingFilters($categoryId, $publishMonth) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $publishMonth) {
            $select->join('co_post_categories', 
                          'co_posts.post_category_id = co_post_categories.post_category_id');

            if ( $categoryId != 0 ) {
                $select->where->equalTo('co_posts.post_category_id', $categoryId);
            }
            if ( $publishMonth != 'all' ) {
                $select->where->like('co_posts.post_date', "%$publishMonth%");
            }
        });
        return $resultSet->count();
    }

    /**
     * 获取某个分类中培训动态的数量.
     * @param  int $categoryId - 培训动态分类的唯一标识符
     * @return 培训动态的数量
     */
    public function getCountUsingCategory($categoryId = 0) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId) {
            if ( $categoryId != 0 ) {
                $select->join('co_post_categories', 
                              'co_posts.post_category_id = co_post_categories.post_category_id');
                $select->where->equalTo('post_category_id', $categoryId);
            }
        });
        return $resultSet->count();
    }

    /**
     * [此方法仅供管理员使用]
     * 获取所有培训动态的信息.
     * @param  int    $categoryId   - 培训动态分类的唯一标识符
     * @param  String $publishMonth - 培训动态的发布月份
     * @param  int    $offset       - 查询结果的Offset
     * @param  int    $limit        - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Post对象.
     */
    public function getPostsUsingFilters($categoryId, $publishMonth, $offset, $limit) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $publishMonth, $offset, $limit) {
            $select->join('co_post_categories', 
                          'co_posts.post_category_id = co_post_categories.post_category_id');
            
            if ( $categoryId != 0 ) {
                $select->where->equalTo('co_posts.post_category_id', $categoryId);
            }
            if ( $publishMonth != 'all' ) {
                $select->where->like('co_posts.post_date', "%$publishMonth%");
            }

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
    public function getPostUsingPostId($postId) {
        $rowSet = $this->tableGateway->select(function (Select $select) use ($postId) {
            $select->join('co_post_categories', 
                          'co_posts.post_category_id = co_post_categories.post_category_id');
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
    public function getPostsUsingCategory($categoryId, $offset, $limit) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($categoryId, $offset, $limit) {
            $select->join('co_post_categories', 
                          'co_posts.post_category_id = co_post_categories.post_category_id');
       
            if ( $categoryId != 0 ) {
                $select->where->equalTo('co_posts.post_category_id', $categoryId);
            }
            $select->offset($offset);
            $select->limit($limit);
            $select->order('post_id DESC');
        });
        return $resultSet;
    }

    /**
     * 获取培训动态发布时间的列表.
     * @return 一个ResultSet对象, 包含培训动态发布时间的信息
     */
    public function getPushlishMonths() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->columns(array(new Expression("DISTINCT(CONCAT(YEAR(post_date), ', ',MONTH(post_date))) as post_date")));
        });
        return $resultSet;
    }

    /**
     * [此方法仅供管理员使用]
     * 根据关键字筛选培训动态.
     * @param  String $keyword - 关键词
     * @param  int $offset     - 查询结果的Offset
     * @param  int $limit      - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Post对象
     */
    public function getPostsUsingKeyword($keyword, $offset, $limit) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($keyword, $offset, $limit) {
            $select->join('co_post_categories', 
                          'co_posts.post_category_id = co_post_categories.post_category_id');
            $select->where->OR->equalTo('co_posts.post_id', "$keyword");
            $select->where->OR->like('co_posts.post_title', "%$keyword%");
            $select->where->OR->like('co_posts.post_content', "%$keyword%");
        });
        return $resultSet;
    }
    
    /**
     * 创建一个新培训动态.
     * @param  Array $post - 一个包含培训动态信息的数组
     * @return 操作是否成功完成
     */
    public function createPost($post) {
        $this->tableGateway->insert($post);
        return true;
    }

    /**
     * 更新培训动态对象的信息.
     * @param  Array $post - 一个包含培训动态信息的数组
     * @return 操作是否成功完成
     */
    public function updatePost($post) {
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
    public function deletePost($postId) {
        $this->tableGateway->delete(array(
            'post_id'   => $postId,
        ));
        return true;
    }
}