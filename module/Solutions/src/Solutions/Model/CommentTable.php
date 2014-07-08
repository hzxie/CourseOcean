<?php

namespace Solutions\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * 评论表的类. 提供与数据表相关操作.
 * 与数据库中的itp_comments相关联.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
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
     * CommentTable的构造函数.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 获取某一课程的评论.
     * @param  int $lectureID - 课程的唯一标识符
     * @param  int $offset - 当前已经获取的评论数量
     * @param  int $limit - 每次获取的评论数量
     * @return 一个包含若干评论信息的ResultSet对象
     */
    public function getCommentsOfCourse($lectureID, $offset, $limit)
    {
        $resultSet  = $this->tableGateway->select(function (Select $select) use ($lectureID, $offset, $limit) {
            $select->join('itp_lecture_courses',
                          'itp_comments.comment_course_id = itp_lecture_courses.course_id');
            $select->order('itp_comments.comment_time DESC');
            $select->where("itp_lecture_courses.lecture_id = $lectureID");
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * 在数据库中创建一条新的评论.
     * @param  Array $comment - 一个包含了评论基本信息的数组
     * @return 数据库操作是否成功
     */
    public function createComment($comment) 
    {
        $this->tableGateway->insert($comment);
        return true;
    }
}