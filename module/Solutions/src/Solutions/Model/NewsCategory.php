<?php

namespace Solutions\Model;

/**
 * The class is the model of the itp_news_categories table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class NewsCategory {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->news_category_id     = (!empty($data['news_category_id']))   ? $data['news_category_id']     : null;
        $this->news_category_slug   = (!empty($data['news_category_slug'])) ? $data['news_category_slug']   : null;
        $this->news_category_name   = (!empty($data['news_category_name'])) ? $data['news_category_name']   : null;
    }

    /**
     * The unique id of the catelogy of the news.
     * @var int
     */
    public $news_category_id;

    /**
     * The unique slug of the catelogy of the news.
     * @var String
     */
    public $news_category_slug;

    /**
     * The name of the catelogy of the news.
     * @var String
     */
    public $news_category_name;
}