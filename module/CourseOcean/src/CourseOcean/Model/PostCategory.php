<?php

namespace CourseOcean\Model;

/**
 * 培训动态分类类的Model.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class PostCategory {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         users table.
     */
    public function exchangeArray($data)
    {
        $this->postCategoryId       = (!empty($data['post_category_id']))   ? $data['post_category_id']     : null;
        $this->postCategorySlug     = (!empty($data['post_category_slug'])) ? $data['post_category_slug']   : null;
        $this->postCategoryName     = (!empty($data['post_category_name'])) ? $data['post_category_name']   : null;
    }

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
}