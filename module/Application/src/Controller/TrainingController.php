<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;

use Application\Controller\BaseController;
use Application\Model\CommentTable;
use Application\Model\CourseModuleTable;
use Application\Model\CourseTable;
use Application\Model\CourseTypeTable;
use Application\Model\LectureAttendanceTable;
use Application\Model\LectureTable;
use Application\Model\PostCategoryTable;
use Application\Model\PostTable;
use Application\Model\TeacherTable;

/**
 * 课程的Controller, 用于完成课程的相关操作.
 * 
 * @author Haozhe Xie <cshzxie@gmail.com>
 */
class TrainingController extends BaseController {
    /**
     * TrainingController的构造函数. 
     */
    public function __construct(CommentTable $commentTable, 
        CourseModuleTable $courseModuleTable, 
        CourseTable $courseTable, 
        CourseTypeTable $courseTypeTable, 
        LectureAttendanceTable $lectureAttendanceTable, 
        LectureTable $lectureTable, 
        PostCategoryTable $postCategoryTable, 
        PostTable $postTable, 
        TeacherTable $teacherTable) {
        $this->commentTable = $commentTable;
        $this->courseModuleTable = $courseModuleTable;
        $this->courseTable = $courseTable;
        $this->courseTypeTable = $courseTypeTable;
        $this->lectureAttendanceTable = $lectureAttendanceTable;
        $this->lectureTable = $lectureTable;
        $this->postCategoryTable = $postCategoryTable;
        $this->postTable = $postTable;
        $this->teacherTable = $teacherTable;
    }

    /**
     * 通过课程类型的唯一英文缩写查找课程类型的唯一标识符.
     * @param  String $catelogySlug - 课程类型的唯一英文缩写
     * @return 课程类型的唯一标识符
     */
    private function getCourseTypeId($catelogySlug) {
        $courseType = $this->courseTypeTable->getCatelogyUsingSlug($catelogySlug);

        if ( $courseType != null ) {
            return $courseType->courseTypeId;
        } 
        return 0;
    }

    /**
     * 获取课程类别的分类信息.
     * @return 一个包含课程类别分类信息的JSON数组
     */
    public function getCourseTypesAction() {
        $courseTypes = $this->courseTypeTable->getAllCourseTypes();
        $result      = [
            'isSuccessful'  => $courseTypes != null && $courseTypes->count() != 0,
            'courseTypes'   => $this->getResultSetArray($courseTypes),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 显示近期开课页面.
     * @return 一个包含页面所需参数的数组
     */
    public function lecturesAction() {
        $courseTypes = $this->courseTypeTable->getAllCourseTypes();

        return new ViewModel([
            'courseTypes'   => $courseTypes,
        ]);
    }

    /**
     * 获取近期开课的课程信息.
     * @return 一个包含近期开课课程信息的JSON数组
     */
    public function getLecturesAction() {
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $courseTypeSlug             = $this->params()->fromQuery('category');
        $startTime                  = $this->params()->fromQuery('startTime');
        $startTime                  = ( $startTime ? $startTime : date('Y-m-d H:i:s') );
        $endTime                    = $this->params()->fromQuery('endTime');
        $region                     = $this->params()->fromQuery('region');
        $province                   = $this->params()->fromQuery('province');
        $city                       = $this->params()->fromQuery('city');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $courseTypeId               = $this->getCourseTypeId($courseTypeSlug);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $lectures   = $this->lectureTable->getLecturesUsingFilters($courseTypeId, $startTime, 
                        $endTime, $region, $province, $city, $offset, $NUMBER_OF_COURSES_PER_PAGE);
        $totalPages = ceil($this->lectureTable->getCountUsingFilters($courseTypeId, $startTime, 
                        $endTime, $region, $province, $city) / $NUMBER_OF_COURSES_PER_PAGE);
        $result     = [
            'isSuccessful'  => $lectures != null && $lectures->count() != 0,
            'lectures'      => $this->getResultSetArray($lectures),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 显示课程会话的详细信息页面.
     * @return 一个包含页面所需参数的数组
     */
    public function lectureAction() {
        $lectureId  = $this->params()->fromQuery('lectureId');
        $lecture    = $this->lectureTable->getLectureUsingLectureId($lectureId);
        if ( $lecture == null ) {
            return $this->notFoundAction();
        }
        
        $teacher            = $this->teacherTable->getTeacherUsingUid($lecture->teacherId);
        $courseModules      = $this->courseModuleTable->getCourseModulesUsingLectureId($lecture->lectureId);
        $lectures           = $this->lectureTable->getLecturesUsingCourseId($lecture->courseId);
        return [
            'lecture'       => $lecture,
            'teacher'       => $teacher,
            'courseModules' => $this->getResultSetArray($courseModules),
            'lectures'      => $this->getResultSetArray($lectures),
            'attendance'    => $this->getLectureAttendance($lecture->lectureId),
        ];
    }

    /**
     * 获取课程会话的的详细信息.
     * @return 一个包含课程会话详细信息的JSON数组
     */
    public function getLectureAction() {
        $arrLecture = $this->lectureAction();
        $result     = [
            'isSuccessful'  => is_array($arrLecture),
            'lecture'       => is_array($arrLecture) ? $arrLecture['lecture'] : null,
            'teacher'       => is_array($arrLecture) ? $arrLecture['teacher'] : null,
            'courseModules' => is_array($arrLecture) ? $arrLecture['courseModules'] : null,
            'lectures'      => is_array($arrLecture) ? $arrLecture['lectures'] : null,
            'attendance'    => is_array($arrLecture) ? $arrLecture['attendance'] : null,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取课程会话报名情况.
     * @param  int $lectureId - 课程会话的唯一标识符
     * @return 一个包含课程会话报名情况的数组
     */
    private function getLectureAttendance($lectureId) {
        $attendance = [
            'hasAttended'   => false,
            'participants'  => 0,
        ];

        $uid                        = $this->getLoginUserUid();
        $attendance['participants'] = intval($this->lectureAttendanceTable->getCountUsingLectureId($lectureId));
        if ( $uid != 0 ) {
            $attendance['hasAttended'] = $this->lectureAttendanceTable->getLectureAttendanceUsingUidAndLectureId($uid, $lectureId);
        }
        return $attendance;
    }

    /**
     * 处理用户参加课程的请求.
     * @return 一个包含操作是否成功标志位的JSON数组
     */
    public function attendLectureAction() {
        $result         = [
            'isSuccessful'      => false,
        ];
        $uid            = $this->getLoginUserUid();
        $lectureId      = $this->params()->fromQuery('lectureId');
        $participants   = $this->params()->fromQuery('participants', 1);

        if ( $uid != 0 ) {
            $lecture            = $this->lectureTable->getLectureUsingLectureId($lectureId);
            $maxCapcity         = $lecture->maxCapcity;
            $lectureAttendance  = [
                'uid'           => $uid,
                'lecture_id'    => $lectureId,
                'serial_code'   => $this->getSerialCode($uid, $lectureId),
                'total_times'   => $participants,
                'remain_times'  => $participants,
            ];
            $alreadyParticipants    = $this->lectureAttendanceTable->getCountUsingLectureId($lectureId);
            if ( $alreadyParticipants + $participants <= $maxCapcity && $uid != $lecture->teacherId ) {
                $result['isSuccessful'] = $this->lectureAttendanceTable->createLectureAttendance($lectureAttendance);
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取报名序列号.
     * @param  int $uid       - 用户的唯一标识符
     * @param  int $lectureId - 课程会话的唯一标识符
     * @return 报名序列号
     */
    private function getSerialCode($uid, $lectureId) {
        return uniqid($uid + $lectureId);
    }

    /**
     * 显示课程库页面.
     * @return 一个包含页面所需参数的数组
     */
    public function coursesAction() {
        $courseTypes = $this->courseTypeTable->getAllCourseTypes();

        return new ViewModel([
            'courseTypes'   => $courseTypes,
        ]);
    }

    /**
     * 获取课程列表.
     * @return 一个包含课程信息的JSON数组
     */
    public function getCoursesAction() {
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $courseTypeSlug             = $this->params()->fromQuery('category');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $courseTypeId               = $this->getCourseTypeId($courseTypeSlug);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $courses    = $this->courseTable->getCoursesUsingCategory($courseTypeId, $offset, $NUMBER_OF_COURSES_PER_PAGE);
        $totalPages = ceil($this->courseTable->getCountUsingCategory($courseTypeId) / $NUMBER_OF_COURSES_PER_PAGE);
        $result     = [
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 显示课程的详细信息.
     * @return 一个包含页面所需参数的数组
     */
    public function courseAction() {
        $courseId   = $this->params()->fromQuery('courseId');
        $course     = $this->courseTable->getCourseUsingCourseId($courseId);

        if ( $course == null ) {
            return $this->notFoundAction();
        }
        $teacher        = $this->teacherTable->getTeacherUsingUid($course->teacherId);
        $courseModules  = $this->courseModuleTable->getCourseModulesUsingCourseId($course->courseId);
        $lectures       = $this->lectureTable->getLecturesUsingCourseId($course->courseId);
        return [
            'course'        => $course,
            'teacher'       => $teacher,
            'courseModules' => $this->getResultSetArray($courseModules),
            'lectures'      => $this->getResultSetArray($lectures),
        ];
    }

    /**
     * 获取某个课程的详细信息.
     * @return 一个包含课程详细信息的JSON数组
     */
    public function getCourseAction() {
        $arrCourse = $this->courseAction();

        $result    = [
            'isSuccessful'  => is_array($arrCourse),
            'course'        => is_array($arrCourse) ? $arrCourse['course'] : null,
            'teacher'       => is_array($arrCourse) ? $arrCourse['teacher'] : null,
            'courseModules' => is_array($arrCourse) ? $arrCourse['courseModules'] : null,
            'lectures'      => is_array($arrCourse) ? $arrCourse['lectures'] : null,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取某个课程的评论.
     * @return 一个包含课程评论信息的JSON对象
     */
    public function getCommentsAction() {
        $NUMBER_OF_COMMENTS_PER_PAGE    = 20;
        $courseId                       = $this->params()->fromQuery('courseId');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_COMMENTS_PER_PAGE;

        $comments   = $this->commentTable->getCommentUsingCourseId($courseId, $offset, $NUMBER_OF_COMMENTS_PER_PAGE);
        $totalPages = ceil($this->commentTable->getCountUsingCourseId($courseId) / $NUMBER_OF_COMMENTS_PER_PAGE);
        $result     = [
            'isSuccessful'  => $comments != null && $comments->count() != 0,
            'comments'      => $this->getResultSetArray($comments),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 显示讲师团队页面.
     * @return 一个包含页面所需参数的数组
     */
    public function teachersAction() {
        $courseTypes = $this->courseTypeTable->getAllCourseTypes();

        return new ViewModel([
            'courseTypes'   => $courseTypes,
        ]);
    }

    /**
     * 获取讲师列表.
     * @return 一个包含讲师信息的JSON数组.
     */
    public function getTeachersAction() {
        $NUMBER_OF_TEACHERS_PER_PAGE    = 20;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_TEACHERS_PER_PAGE;

        $teachers   = $this->teacherTable->getTeachersUsingCategory($courseTypeId, $offset, $NUMBER_OF_TEACHERS_PER_PAGE);
        $totalPages = ceil($this->teacherTable->getCount($courseTypeId) / $NUMBER_OF_TEACHERS_PER_PAGE);
        $result     = [
            'isSuccessful'  => $teachers != null && $teachers->count() != 0,
            'teachers'      => $this->getResultSetArray($teachers),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }
    
    /**
     * 显示讲师的详细信息.
     * @return 一个包含页面所需参数的数组
     */
    public function teacherAction() {
        $uid        = $this->params()->fromQuery('teacherId');
        $teacher    = $this->teacherTable->getTeacherUsingUid($uid);

        if ( $teacher == null ) {
            return $this->notFoundAction();
        }
        $courses    = $this->courseTable->getCoursesUsingTeacherId($uid);
        return [
            'teacher'   => $teacher,
            'courses'   => $this->getResultSetArray($courses),
        ];
    }

    /**
     * 获取某个讲师的详细信息.
     * @return 一个包含某个讲师详细信息的JSON数组.
     */
    public function getTeacherAction() {
        $arrTeacher = $this->teacherAction();
        $result     = [
            'isSuccessful'  => is_array($arrTeacher),
            'teacher'       => is_array($arrTeacher) ? $arrTeacher['teacher'] : null,
            'courses'       => is_array($arrTeacher) ? $arrTeacher['courses'] : null,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取培训动态分类的分类信息.
     * @return 一个包含培训动态分类信息的JSON数组
     */
    public function getPostCategoriesAction() {
        $postCategories = $this->postCategoryTable->getAllPostCategories();
        $result         = [
            'isSuccessful'      => $postCategories != null && $postCategories->count() != 0,
            'postCategories'    => $this->getResultSetArray($postCategories),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 通过培训动态分类的唯一英文缩写查找培训动态分类的唯一标识符.
     * @param  String $catelogySlug - 培训动态分类的唯一英文缩写
     * @return 培训动态分类的唯一标识符
     */
    private function getPostCategoryId($catelogySlug) {
        $postCategory = $this->postCategoryTable->getCatelogyUsingSlug($catelogySlug);

        if ( $postCategory != null ) {
            return $postCategory->postCategoryId;
        } 
        return 0;
    }

    /**
     * 显示培训动态的页面.
     * @return 一个包含页面所需参数的数组
     */
    public function postsAction() {
        $postCategories = $this->postCategoryTable->getAllPostCategories();
        
        return new ViewModel([
            'postCategories'    => $postCategories,
        ]);
    }

    /**
     * 获取培训动态.
     * @return 一个包含培训动态的JSON数组
     */
    public function getPostsAction() {
        $NUMBER_OF_POSTS_PER_PAGE   = 10;
        $postCategorySlug           = $this->params()->fromQuery('category');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $postCategoryId             = $this->getPostCategoryId($postCategorySlug);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_POSTS_PER_PAGE;

        $posts      = $this->postTable->getPostsUsingCategory($postCategoryId, $offset, $NUMBER_OF_POSTS_PER_PAGE);
        $totalPages = ceil($this->postTable->getCountUsingCategory($postCategoryId) / $NUMBER_OF_POSTS_PER_PAGE);
        $result     = [
            'isSuccessful'  => $posts != null && $posts->count() != 0,
            'posts'         => $this->getResultSetArray($posts),
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 显示培训动态的详细信息.
     * @return 一个包含页面所需参数的数组
     */
    public function postAction() {
        $postId = $this->params()->fromQuery('postId');
        $post   = $this->postTable->getPostUsingPostId($postId);

        if ( $post == null ) {
            return $this->notFoundAction();
        }
        $posts  = $this->postTable->getPostsUsingCategory($post->postCategoryId, 0, 10);
        
        return [
            'post'      => $post,
            'posts'     => $this->getResultSetArray($posts),
        ];
    }

    /**
     * 获取某个培训动态的详细信息.
     * @return 一个包含培训动态详细信息的JSON数组
     */
    public function getPostAction() {
        $arrPost  = $this->postAction();

        $result     = [
            'isSuccessful'  => is_array($arrPost),
            'post'          => is_array($arrPost) ? $arrPost['post'] : null,
            'posts'         => is_array($arrPost) ? $arrPost['posts'] : null,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * The data access object of Comment.
     * @var CommentTable
     */
    private $commentTable;
    
    /**
     * The data access object of CourseModule.
     * @var CourseModuleTable
     */
    private $courseModuleTable;
    
    /**
     * The data access object of Course.
     * @var CourseTable
     */
    private $courseTable;
    
    /**
     * The data access object of CourseType.
     * @var CourseTypeTable
     */
    private $courseTypeTable;
    
    /**
     * The data access object of LectureAttendance.
     * @var LectureAttendanceTable
     */
    private $lectureAttendanceTable;
    
    /**
     * The data access object of Lecture.
     * @var LectureTable
     */
    private $lectureTable;
    
    /**
     * The data access object of PostCategory.
     * @var PostCategoryTable
     */
    private $postCategoryTable;
    
    /**
     * The data access object of Post.
     * @var PostTable
     */
    private $postTable;
    
    /**
     * The data access object of Teacher.
     * @var TeacherTable
     */
    private $teacherTable;
}
