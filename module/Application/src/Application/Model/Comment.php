<?php

namespace Application\Model;

/**
 * 课程评价类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class Comment {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->commentId        = (!empty($data['comment_id']))         ? $data['comment_id']       : null;
        $this->lectureId        = (!empty($data['lecture_id']))         ? $data['lecture_id']       : null;
        $this->courseId         = (!empty($data['course_id']))          ? $data['course_id']        : null;
        $this->reviewerUid      = (!empty($data['reviewer_uid']))       ? $data['reviewer_uid']     : null;
        $this->reviewerUsername = (!empty($data['username']))           ? $data['username']         : null;
        $this->commentTime      = (!empty($data['comment_time']))       ? $data['comment_time']     : null;
        $this->ranking          = (!empty($data['comment_ranking']))    ? $data['comment_ranking']  : null;
        $this->detail           = (!empty($data['comment_detail']))     ? $data['comment_detail']   : null;
    }

    /**
     * 课程评价的唯一标识符.
     * @var int 
     */
    public $commentId;

    /**
     * 课程会话的唯一标识符.
     * @var int
     */
    public $lectureId;

    /**
     * 课程唯一标识符.
     * @var int
     */
    public $courseId;

    /**
     * 评论者的用户唯一标识符.
     * @var int
     */
    public $reviewerUid;

    /**
     * 评论者的用户名.
     * @var String
     */
    public $reviewerUsername;

    /**
     * 评论的发布时间.
     * @var DateTime
     */
    public $commentTime;

    /**
     * 课程评价等级(1~5的整数)
     * @var int
     */
    public $ranking;

    /**
     * 课程评价内容
     * @var String
     */
    public $detail;
}