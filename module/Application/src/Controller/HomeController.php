<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;

use Application\Controller\BaseController;
use Application\Model\CourseTable;
use Application\Model\LectureTable;
use Application\Model\PostTable;
use Application\Model\TeacherTable;

/**
 * 应用程序的Controller, 用于完成应用程序中的公共事务.
 * 
 * @author Haozhe Xie <cshzxie@gmail.com>
 */
class HomeController extends BaseController {
    /**
     * HomeController的构造函数. 
     */
    public function __construct(CourseTable $courseTable, 
        LectureTable $lectureTable, PostTable $postTable,
        TeacherTable $teacherTable) {
        $this->courseTable  = $courseTable;
        $this->lectureTable = $lectureTable;
        $this->postTable    = $postTable;
        $this->teacherTable = $teacherTable;
    }

    /**
     * 显示网站的首页.
     * @return 一个包含页面所需信息的数组
     */
    public function indexAction() {
        $courses        = $this->getRecommendCourses();
        $lectures       = $this->getRecommendLectures();
        $posts          = $this->getRecommendPosts();
        $teachers       = $this->getRecommendTeachers();

        return new ViewModel([
            'courses'   => $courses,
            'lectures'  => $lectures,
            'posts'     => $posts,
            'teachers'  => $teachers,
        ]);
    }

    /**
     * 根据用户职位定向推荐培训课程(Course).
     * @return 一个包含课程信息的数组
     */
    private function getRecommendCourses() {
        $courseTypeId   = 0;
        $offset         = 0;
        $limit          = 10;
        
        $courses        = $this->courseTable->getCoursesUsingCategory(
                            $courseTypeId, $offset, $limit);
        return $this->getResultSetArray($courses);
    }

    /**
     * 根据用户职位和所在地定向推荐培训课程(Lecture).
     * @return 一个包含培训课程信息的数组
     */
    private function getRecommendLectures() {
        $courseTypeId   = null;
        $startTime      = date('Y-m-d H:i');
        $endTime        = null;
        $region         = null;
        $province       = null; 
        $city           = null;
        $offset         = 0;
        $limit          = 10;

        $lectures       = $this->lectureTable->getLecturesUsingFilters(
                            $courseTypeId, $startTime, $endTime, $region, 
                            $province, $city, $offset, $limit);
        return $this->getResultSetArray($lectures);
    }

    /**
     * 获取近期培训动态.
     * @return 一个包含培训动态信息的数组.
     */
    private function getRecommendPosts() {
        $categoryId     = 0;
        $offset         = 0;
        $limit          = 10;
        $posts          = $this->postTable->getPostsUsingCategory($categoryId, $offset, $limit);

        return $this->getResultSetArray($posts);
    }

    /**
     * 根据用户职位/公司经营领域等定向推荐讲师.
     * @return 一个包含讲师个人信息的数组
     */
    private function getRecommendTeachers() {
        $categoryId     = 0;
        $limit          = 12;
        $totalTeachers  = $this->teacherTable->getCount($categoryId);
        
        $offset         = ($totalTeachers <= $limit ? 0 : rand(0, $totalTeachers - $limit));
        $teachers       = $this->teacherTable->getTeachersUsingCategory($categoryId, $offset, $limit);

        return $this->getResultSetArray($teachers);
    }

    /**
     * 响应用户的搜索请求.
     * @return 一个页面所需信息的数组
     */
    public function searchAction() {
        $keyword = trim($this->params()->fromQuery('keyword'));

        return new ViewModel([
            'keyword'   => $keyword,
        ]);
    }

    /**
     * 响应用户的搜索请求(搜索的核心处理函数).
     * @return 一个包含搜索结果的JSON数组
     */
    public function getSearchResultAction() {
        $keyword                    = $this->params()->fromQuery('keyword');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $NUMBER_OF_RECORDS_PER_PAGE = 10;
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_RECORDS_PER_PAGE;
        $searchResult               = $this->getSearchResult($keyword, $offset, $NUMBER_OF_RECORDS_PER_PAGE);

        $result   = [
            'isSuccessful'  => count($searchResult['teachers']) != 0 || count($searchResult['courses']) != 0,
            'searchResult'  => $searchResult,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
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
    private function getSearchResult($keyword, $offset, $limit) {
        $searchResult   = [];
        $searchResult  += [
            'teachers'  => $this->getSearchResultOfTeacher($keyword, $offset, $limit),
        ];
        $searchResult  += [
            'courses'   => $this->getSearchResultOfCourse($keyword, $offset, $limit),
        ];
        return $searchResult;
    }

    /**
     * 通过关键词搜索讲师.
     * @param  String $keyword  - 关键词
     * @param  int    $offset   - 查询结果的Offset
     * @param  int    $limit    - 查询返回的记录数
     * @return 包含搜索结果的数组
     */
    private function getSearchResultOfTeacher($keyword, $offset, $limit) {
        $keywords       = explode(' ', $keyword);
        $resultSet      = [];

        foreach ( $keywords as $keyword ) {
            if ( empty(trim($keyword)) ) {
                continue;
            }
            $teachers   = $this->teacherTable->getTeachersUsingKeyword($keyword, $offset, $limit);
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
    private function getSearchResultOfCourse($keyword, $offset, $limit) {
        $keywords       = explode(' ', $keyword);
        $resultSet      = [];

        foreach ( $keywords as $keyword ) {
            if ( empty(trim($keyword)) ) {
                continue;
            }
            $courses    = $this->courseTable->getCoursesUsingKeyword($keyword, $offset, $limit);
            $resultSet += $this->getSearchResultSetArray($courses, 'course');
        }
        return $this->getSearchResultSetArray($resultSet);
    }

    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    private function getSearchResultSetArray($resultSet, $category = null) {
        $returnArray = [];
        
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
                $returnArray   += [
                    $identifier => $rowArray,
                ];
            } else {
                array_push($returnArray, $rowArray);
            }
        }
        return $returnArray;
    }

    /**
     * The data access object of Model\Course.
     * @var CourseTable
     */
    private $courseTable;
    
    /**
     * The data access object of Model\Lecture.
     * @var LectureTable
     */
    private $lectureTable;

    /**
     * The data access object of Model\Post.
     * @var PostTable
     */
    private $postTable;

    /**
     * The data access object of Model\Teacher.
     * @var TeacherTable
     */
    private $teacherTable;
}
