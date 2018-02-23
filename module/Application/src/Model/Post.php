<?php

namespace Application\Model;

/**
 * 培训动态类的Model.
 * 
 * @author 谢浩哲 <cshzxie@gmail.com>
 */
class Post {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data) {
        $this->postId           = (!empty($data['post_id']))            ? $data['post_id']              : null;
        $this->postCategoryId   = (!empty($data['post_category_id']))   ? $data['post_category_id']     : null;
        $this->postCategorySlug = (!empty($data['post_category_slug'])) ? $data['post_category_slug']   : null;
        $this->postCategoryName = (!empty($data['post_category_name'])) ? $data['post_category_name']   : null;
        $this->postDate         = (!empty($data['post_date']))          ? $data['post_date']            : null;
        $this->postTitle        = (!empty($data['post_title']))         ? $data['post_title']           : null;
        $this->postContent      = (!empty($data['post_content']))       ? $data['post_content']         : null;
    }

    /**
     * 培训动态的唯一标识符.
     * @var int 
     */
    public $postId;

    /**
     * 培训动态分类的唯一标识符.
     * @var int
     */
    public $postCategoryId;

    /**
     * 培训动态分类的唯一英文缩写.
     * @var String
     */
    public $postCategorySlug;

    /**
     * 培训动态分类的名称.
     * @var String
     */
    public $postCategoryName;

    /**
     * 培训动态的发布日期.
     * @var DateTime
     */
    public $postDate;

    /**
     * 培训动态的标题.
     * @var String
     */
    public $postTitle;

    /**
     * 培训动态的内容.
     * @var String
     */
    public $postContent;
}