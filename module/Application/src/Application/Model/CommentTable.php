<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 课程评价类的DAO(Data Access Object).
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class CommentTable
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
     * 使用用户的唯一标识符获取该用户对所参加的课程评价的数量.
     * @param  int $uid - 用户的唯一标识符
     * @return 某个用户评价的数量
     */
    public function getCountUsingUid($uid)
    {
        $resultSet = $this->tableGateway->select(array(
            'reviewer_uid'  => $uid,
        ));
        return $resultSet->count();
    }

    /**
     * 使用课程的唯一标识符获取对某个课程评价的数量.
     * @param  int $courseId - 课程的唯一标识符
     * @return 某个课程评价的数量
     */
    public function getCountUsingCourseId($courseId)
    {
        $resultSet = $this->tableGateway->select(array(
            'course_id' => $courseId,
        ));
        return $resultSet->count();
    }

    /**
     * 使用用户的唯一标识符获取课程评论对象.
     * @param  int $uid - 用户的唯一标识符
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Comment对象
     */
	public function getCommentUsingUid($uid, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($uid, $offset, $limit) {
            $select->join('itp_courses', 
                          'itp_comments.course_id = itp_courses.course_id');
            $select->where->equalTo('reviewer_uid', $uid);
            $select->offset($offset);
            $select->limit($limit);
            $select->order('comment_time DESC');
        });
        return $resultSet;
    }

    /**
     * 使用课程的唯一标识符获取课程评论对象.
     * @param  int $courseId - 课程的唯一标识符
     * @param  int $offset - 查询结果的Offset
     * @param  int $limit  - 查询返回的记录数
     * @return 一个ResultSet对象, 包含若干个Comment对象
     */
    public function getCommentUsingCourseId($courseId, $offset, $limit)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($courseId, $offset, $limit) {
            $select->join('itp_users', 
                          'itp_comments.reviewer_uid = itp_users.uid');
            $select->where->equalTo('course_id', $courseId);
            $select->offset($offset);
            $select->limit($limit);
            $select->order('comment_time DESC');
        });
        return $resultSet;
    }

    /**
     * 使用课程的唯一标识符获取课程评价的平均分.
     * @param  int $courseId - 课程的唯一标识符
     * @return 课程评价的平均分
     */
    public function getCommentAverageRankingUsingCourseId($courseId)
    {
        $rowSet     = $this->tableGateway->select(function (Select $select) use ($courseId) {
            $select->where->equalTo('course_id', $courseId);
            $select->columns(array(new \Zend\Db\Sql\Expression('AVG(ranking) as ranking')));
        });
        $ranking    = $rowSet->current();
        if ( $ranking == null ) {
            echo 'null!';
            return 0;
        }
        return $ranking->ranking;
    }

    /**
     * 创建一个新课程评论.
     * @param  Array $comment - 一个包含课程评论信息的数组
     * @return 操作是否成功完成
     */
    public function createComment($comment)
    {
        $this->tableGateway->insert($comment);
        return true;
    }

    /**
     * 更新课程评论对象的信息.
     * @param  Array $comment - 一个包含课程评论信息的数组
     * @return 操作是否成功完成
     */
    public function updateComment($comment)
    {
        $this->tableGateway->update($comment, array(
            'commentId' => $comment['comment_id'],
        ));
        return true;
    }

    /**
     * 通过课程评论唯一标识符删除课程评论对象.
     * @param  int $commentId - 课程评论的唯一标识符
     * @return 操作是否成功完成
     */
    public function deleteComment($uid)
    {
        $this->tableGateway->delete(array(
            'commentId' => $comment['comment_id'],
        ));
        return true;
    }
}