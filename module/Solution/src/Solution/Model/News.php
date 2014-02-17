<?php

namespace Solution\Model;

/**
 * The class is the model of the itp_news table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class News {
    /**
     * This method simply copies the data from the passed in array to our 
     * entitys properties.
     * 
     * @param  Array $data - an array which contains data descripted in the
     *         courses table.
     */
    public function exchangeArray($data)
    {
        $this->news_id              = (!empty($data['news_id']))            ? $data['news_id']              : null;
        $this->news_category_id     = (!empty($data['news_category_id']))   ? $data['news_category_id']     : null;
        $this->news_category_slug   = (!empty($data['news_category_slug'])) ? $data['news_category_slug']   : null;
        $this->news_category_name   = (!empty($data['news_category_name'])) ? $data['news_category_name']   : null;
        $this->publish_date         = (!empty($data['publish_date']))       ? $data['publish_date']         : null;
        $this->news_title           = (!empty($data['news_title']))         ? $data['news_title']           : null;
        $this->news_content         = (!empty($data['news_content']))       ? $data['news_content']         : null;
    }

    /**
     * The unique id of the news.
     * @var int
     */
    public $news_id;

    /**
     * The unique id of the category of the news.
     * @var int
     */
    public $news_category_id;

    /**
     * The unique slug of the category of the news.
     * @var String
     */
    public $news_category_slug;

    /**
     * The name of the category of the news.
     * @var String
     */
    public $news_category_name;

    /**
     * The date when news published.
     * @var TimeStamp
     */
    public $publish_date;

    /**
     * The title of the news.
     * @var String
     */
    public $news_title;

    /**
     * The content of the news.
     * @var String
     */
    public $news_content;

}