<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * The deafult controller of the application.
 * It's mainly used for displaying the content of the homepage.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class IndexController extends AbstractActionController
{
    /**
     * Default method to call in the controller.
     * 
     * @return a ViewModel object which contains HTML content
     */
    public function indexAction()
    {
        $NUMBER_OF_RECORDS  = 3;
        return array(
            'lectures'  => $this->getLectures($NUMBER_OF_RECORDS),
            'news'      => $this->getNews($NUMBER_OF_RECORDS),
        );
    }

    /**
     * Get information of lectures.
     * @param  int $limit - the number of records to get
     * @return an array which contains objects of Lecture that 
     *         contains information of lectures
     */
    private function getLectures($limit)
    {
        return $this->getLatestLectures($limit);
    }

    /**
     * Get information of the latest lectures.
     * int $limit - the number of records to get
     * @return an array which contains objects of Lecture that 
     *         contains information of the latest lectures
     */
    private function getLatestLectures($limit)
    {
        $pageNumber     = 1;
        $sm             = $this->getServiceLocator();
        $lectureTable   = $sm->get('Solutions\Model\LectureTable');

        return $lectureTable->fetchAll($pageNumber, $limit);
    }

    /**
     * Get the news of the site.
     * int $limit - the number of records to get
     * @return an array which contains objects of News that contains
     *         information of the news
     */
    private function getNews($limit)
    {
        return $this->getLatestNews($limit);
    }

    /**
     * Get the latest news of the site.
     * int $limit - the number of records to get
     * @return an array which contains objects of News that contains
     *         information of the latest news
     */
    private function getLatestNews($limit)
    {
        $pageNumber     = 1;
        $sm             = $this->getServiceLocator();
        $newsTable      = $sm->get('Solutions\Model\NewsTable');

        return $newsTable->fetchAll($pageNumber, $limit);
    }
}
