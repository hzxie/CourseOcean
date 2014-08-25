<?php

namespace Application\Controller;

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
        return array();
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
        $keywords       = split(' ', $keyword);

        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');
        $resultSet      = array();

        foreach ( $keywords as $keyword ) {
            if ( empty(trim($keyword)) ) {
                continue;
            }
            $teachers   = $teacherTable->getTeacherUsingKeyword($keyword, $offset, $limit);
            $resultSet += $this->getResultSetArray($teachers, 'teacher');
        }
        return $this->getResultSetArray($resultSet);
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
        $keywords       = split(' ', $keyword);

        $serviceManager = $this->getServiceLocator();
        $courseTable    = $serviceManager->get('Application\Model\CourseTable');
        $resultSet      = array();

        foreach ( $keywords as $keyword ) {
            if ( empty(trim($keyword)) ) {
                continue;
            }
            $courses    = $courseTable->getCourseUsingKeyword($keyword, $offset, $limit);
            $resultSet += $this->getResultSetArray($courses, 'course');
        }
        return $this->getResultSetArray($resultSet);
    }

    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    private function getResultSetArray($resultSet, $category = null)
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