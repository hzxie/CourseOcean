<?php

namespace Solutions\Model;

/**
 * 课程评论的Model.
 * 与数据库中的itp_comments表关联.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class Comment {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->comment_id           = (!empty($data['comment_id']))         ? $data['comment_id']           : null;
        $this->comment_uid          = (!empty($data['comment_uid']))        ? $data['comment_uid']          : null;
        $this->comment_username     = (!empty($data['comment_username']))   ? $data['comment_username']     : null;
        $this->comment_course_id    = (!empty($data['comment_course_id']))  ? $data['comment_course_id']    : null;
        $this->comment_time         = (!empty($data['comment_time']))       ? $data['comment_time']         : null;
        $this->comment_content      = (!empty($data['comment_content']))    ? $data['comment_content']      : null;
    }

    /**
     * 评论的唯一标识符.
     * @var int
     */
    public $comment_id;

    /**
     * 发表评论用户的用户唯一标识符(uid).
     * @var int
     */
    public $comment_uid;

    /**
     * 发表评论用户的用户名.
     * @var String
     */
    public $comment_username;

    /**
     * 所评论课程的唯一标识符.
     * @var int
     */
    public $comment_course_id;

    /**
     * 发表评论的时间.
     * @var DateTime
     */
    public $comment_time;

    /**
     * 评论的内容.
     * @var String
     */
    public $comment_content;
}