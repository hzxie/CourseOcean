<?php

namespace Courses\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\BasePath;

/**
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class RecentlyController extends AbstractActionController
{
	/**
	 * Default method to call in the controller.
	 * 
	 * @return a ViewModel object which contains HTML content
	 */
	public function pageAction()
    {
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $pageNumber                 = $this->params()->fromRoute('id', 1);
        return array(
        	'lectures'	        => $this->getAllLectures($pageNumber, $NUMBER_OF_COURSES_PER_PAGE),
            'currentPageNumber' => $pageNumber,
            'numberOfPages'     => $this->getNumberOfPages($NUMBER_OF_COURSES_PER_PAGE),
            'courseTypes'       => $this->getAllCourseTypes(),
        );
    }

    /**
     * Get all lectures in the database.
     * @param  int $pageNumber - current number of the page
     * @param  int $limit - max number of lectures in a page
     * @return an array which contains all general information 
     *         of the lectures
     */
    private function getAllLectures($pageNumber, $limit)
    {
        $sm             = $this->getServiceLocator();
        $lectureTable   = $sm->get('Courses\Model\LectureTable');

        return $lectureTable->fetchAll($pageNumber, $limit);
    }

    /**
     * Get total number of pages for the lectures.
     * @param  int $limit - max number of lectures in a page
     * @return an integer which stands for the total number of pages for 
     *         the lectures
     */
    private function getNumberOfPages($limit)
    {
        $sm                 = $this->getServiceLocator();
        $lectureTable       = $sm->get('Courses\Model\LectureTable');
        $numberOfLectures   = $lectureTable->getNumberOfLectures();

        return ceil( $numberOfLectures / $limit );
    }

    /**
     * Get all information of course types in the database.
     * @return an array of objects of CourseType which contains 
     *         informtaion of course type in the database
     */
    private function getAllCourseTypes()
    {
        $sm                 = $this->getServiceLocator();
        $courseTypeTable    = $sm->get('Courses\Model\CourseTypeTable');

        return $courseTypeTable->fetchAll();
    }
}
