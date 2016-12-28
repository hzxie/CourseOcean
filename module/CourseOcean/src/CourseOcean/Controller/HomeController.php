<?php

namespace CourseOcean\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 应用程序的Controller, 用于完成应用程序中的公共事务.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class HomeController extends AbstractActionController
{
    /**
     * 显示网站的首页.
     * @return 一个包含页面所需信息的数组
     */
    public function indexAction()
    {
        $lectures       = $this->getRecommendLectures();
        $courses        = $this->getRecommendCourses();
        $teachers       = $this->getRecommendTeachers();
        $posts          = $this->getRecommendPosts();

        return array(
            'lectures'  => $lectures,
            'courses'   => $courses,
            'teachers'  => $teachers,
            'posts'     => $posts,
        );
    }

    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    private function getResultSetArray($resultSet)
    {
        $returnArray = array();
        
        if ( $resultSet == null ) {
            return $returnArray;
        }
        foreach ( $resultSet as $rowSet ) {
            $rowArray = (array)$rowSet;
            array_push($returnArray, $rowArray);
        }
        return $returnArray;
    }

    /**
     * 根据用户职位和所在地定向推荐培训课程(Lecture).
     * @return 一个包含培训课程信息的数组
     */
    private function getRecommendLectures()
    {
        $serviceManager = $this->getServiceLocator();
        $lectureTable   = $serviceManager->get('CourseOcean\Model\LectureTable');

        $courseTypeId   = null;
        $startTime      = date('Y-m-d H:i');
        $endTime        = null;
        $region         = null;
        $province       = null; 
        $city           = null;
        $offset         = 0;
        $limit          = 10;

        $lectures       = $lectureTable->getLecturesUsingFilters($courseTypeId, $startTime, $endTime, 
                                         $region, $province, $city, $offset, $limit);
        return $this->getResultSetArray($lectures);
    }

    /**
     * 根据用户职位定向推荐培训课程(Course).
     * @return 一个包含课程信息的数组
     */
    private function getRecommendCourses()
    {
        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('CourseOcean\Model\CourseTable');

        $courseTypeId   = 0;
        $offset         = 0;
        $limit          = 10;
        
        $courses        = $courseTable->getCoursesUsingCategory($courseTypeId, $offset, $limit);
        return $this->getResultSetArray($courses);
    }

    /**
     * 根据用户职位/公司经营领域等定向推荐讲师.
     * @return 一个包含讲师个人信息的数组
     */
    private function getRecommendTeachers()
    {
        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('CourseOcean\Model\TeacherTable');

        $categoryId     = 0;
        $limit          = 12;
        $totalTeachers  = $teacherTable->getCount($categoryId);
        
        $offset         = ($totalTeachers <= $limit ? 0 : rand(0, $totalTeachers - $limit));
        $teachers       = $teacherTable->getTeachersUsingCategory($categoryId, $offset, $limit);

        return $this->getResultSetArray($teachers);
    }

    /**
     * 获取近期培训动态.
     * @return 一个包含培训动态信息的数组.
     */
    private function getRecommendPosts()
    {
        $serviceManager = $this->getServiceLocator();
        $postTable      = $serviceManager->get('CourseOcean\Model\PostTable');
        
        $categoryId     = 0;
        $offset         = 0;
        $limit          = 10;
        $posts          = $postTable->getPostsUsingCategory($categoryId, $offset, $limit);

        return $this->getResultSetArray($posts);
    }

    /**
     * 响应用户的搜索请求.
     * @return 一个页面所需信息的数组
     */
    public function searchAction()
    {
        $keyword = trim($this->params()->fromQuery('keyword'));

        return array(
            'keyword'   => $keyword,
        );
    }

    /**
     * 响应用户的搜索请求(搜索的核心处理函数).
     * @return 一个包含搜索结果的JSON数组
     */
    public function getSearchResultAction()
    {
        $keyword                    = $this->params()->fromQuery('keyword');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $NUMBER_OF_RECORDS_PER_PAGE = 10;
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_RECORDS_PER_PAGE;
        $searchResult               = $this->getSearchResult($keyword, $offset, $NUMBER_OF_RECORDS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => count($searchResult['teachers']) != 0 || count($searchResult['courses']) != 0,
            'searchResult'  => $searchResult,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 通过关键词搜索课程或讲师.
     * @param  String $keyword  - 关键词
     * @param  String $category - 搜索的内容(课程/讲师/both)
     * @param  int    $offset   - 查询结果的Offset
     * @param  int    $limit    - 查询返回的记录数
     * @return 包含搜索结果的数组
     */
    private function getSearchResult($keyword, $offset, $limit)
    {
        $searchResult   = array();
        $searchResult  += array(
            'teachers'  => $this->getSearchResultOfTeacher($keyword, $offset, $limit),
        );
        $searchResult  += array(
            'courses'   => $this->getSearchResultOfCourse($keyword, $offset, $limit),
        );
        return $searchResult;
    }

    /**
     * 通过关键词搜索讲师.
     * @param  String $keyword  - 关键词
     * @param  int    $offset   - 查询结果的Offset
     * @param  int    $limit    - 查询返回的记录数
     * @return 包含搜索结果的数组
     */
    private function getSearchResultOfTeacher($keyword, $offset, $limit)
    {
        $keywords       = explode(' ', $keyword);

        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('CourseOcean\Model\TeacherTable');
        $resultSet      = array();

        foreach ( $keywords as $keyword ) {
            if ( empty(trim($keyword)) ) {
                continue;
            }
            $teachers   = $teacherTable->getTeachersUsingKeyword($keyword, $offset, $limit);
            $resultSet += $this->getSearchResultSetArray($teachers, 'teacher');
        }
        return $this->getSearchResultSetArray($resultSet);
    }

    /**
     * 通过关键词搜索课程.
     * @param  String $keyword  - 关键词
     * @param  int    $offset   - 查询结果的Offset
     * @param  int    $limit    - 查询返回的记录数
     * @return 包含搜索结果的数组
     */
    private function getSearchResultOfCourse($keyword, $offset, $limit)
    {
        $keywords       = explode(' ', $keyword);

        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('CourseOcean\Model\CourseTable');
        $resultSet      = array();

        foreach ( $keywords as $keyword ) {
            if ( empty(trim($keyword)) ) {
                continue;
            }
            $courses    = $courseTable->getCoursesUsingKeyword($keyword, $offset, $limit);
            $resultSet += $this->getSearchResultSetArray($courses, 'course');
        }
        return $this->getSearchResultSetArray($resultSet);
    }

    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    private function getSearchResultSetArray($resultSet, $category = null)
    {
        $returnArray = array();
        
        if ( $resultSet == null ) {
            return $returnArray;
        }
        foreach ( $resultSet as $rowSet ) {
            $rowArray       = (array)$rowSet;
    
            if ( $category != null ) {
                $identifier     = 0;
                if ( $category == 'teacher' ) {
                    $identifier = $rowArray['uid'];
                } else {
                    $identifier = $rowArray['courseId'];
                }
                $returnArray   += array(
                    $identifier => $rowArray,
                );
            } else {
                array_push($returnArray, $rowArray);
            }
        }
        return $returnArray;
    }
}