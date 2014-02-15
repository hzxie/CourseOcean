<?php

namespace Courses\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * The table gateway of the news table.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class NewsTable
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
     * The contructor of the NewsTable class.
     * @param TableGateway $tableGateway 
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Get all records from the news table by pagination.
     * @param  int $pageNumber - current number of the page
     * @param  int $limit - max number of courses in a page
     * @return an object which is an instance of ResultSet, which contains
     *         data of all news.
     */
    public function fetchAll($pageNumber, $limit)
    {
        $offset     = ( $pageNumber - 1 ) * $limit;
        $resultSet  = $this->tableGateway->select(function (Select $select) use ($offset, $limit) {
            $select->join('itp_news_categories', 
                          'itp_news.news_category_id = itp_news_categories.news_category_id');
            $select->order('news_id DESC');
            $select->offset($offset);
            $select->limit($limit);
        });
        return $resultSet;
    }

    /**
     * Get number of records in the courses table.
     * @return an integer which stands for the number of records in the news
     *         table
     */
    public function getNumberOfNews()
    {
        return $this->tableGateway->select()->count();
    }

    /**
     * Get information of a certain post of news.
     * @param  int $newsID - the unique id of the news
     * @return an object of News which contains information of a cerain post of 
     *         news
     */
    public function getNews($newsID)
    {
        $rowset     = $this->tableGateway->select(function (Select $select) use ($courseID) {
            $select->join('itp_news_categories', 
                          'itp_news.news_category_id = itp_news_categories.news_category_id');
            $select->where->equalTo('news_id', $newsID);
        });
        return $rowset->current();
    }
}