<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

use Application\Controller\BaseController;
use Application\Model\CourseTable;
use Application\Model\CourseTypeTable;
use Application\Model\PostCategoryTable;
use Application\Model\PostTable;
use Application\Model\UserTable;
use Application\Model\UserGroupTable;

/**
 * 管理的Controller, 用于完成系统的监控和管理操作.
 * 
 * @author Haozhe Xie <cshzxie@gmail.com>
 */
class AdministrationController extends BaseController {
    /**
     * AdministrationController的构造函数. 
     */
    public function __construct(CourseTable $courseTable, 
        CourseTypeTable $courseTypeTable, 
        PostCategoryTable $postCategoryTable, 
        PostTable $postTable, UserTable $userTable, 
        UserGroupTable $userGroupTable) {
        $this->courseTable = $courseTable;
        $this->courseTypeTable = $courseTypeTable;
        $this->postCategoryTable = $postCategoryTable;
        $this->postTable = $postTable;
        $this->userTable = $userTable;
        $this->userGroupTable = $userGroupTable;
    }

    /**
     * 显示系统管理页面.
     * @return 一个包含页面所需信息的数组
     */
    public function indexAction() {
        if ( !$this->isAllowedToAccess(['administrator']) ) {
            return $this->redirect()->toRoute('accounts', [
                'controller'    => 'accounts',
                'action'        => 'dashboard'
            ]);
        }

        return new ViewModel([
            'profile'       => $this->getUserProfile(),
            'uncheckUsers'  => $this->getUncheckUsers(),
        ]);
    }

    /**
     * 获取用户的基本信息.
     * @return 一个包含用户基本信息的数组
     */
    private function getUserProfile() {
        $session    = new Container('co_session');
        return [
            'uid'           => $session->offsetGet('uid'),
            'username'      => $session->offsetGet('username'),
            'userGroupSlug' => $session->offsetGet('userGroupSlug'),
            'email'         => $session->offsetGet('email'),
        ];
    }

    /**
     * 获取所请求页面的内容.
     * @return 包含页面内容的HTML字符串
     */
    public function getPageContentAction() {
        $pageName = $this->params()->fromQuery('pageName');
        $pageData = $this->getPageData($pageName);
        $view     = new ViewModel($pageData);
        $view->setTerminal(true);

        $template = "/application/administration/dashboard/$pageName.phtml";
        $resolver = $this->getEvent()
                         ->getApplication()
                         ->getServiceManager()
                         ->get('Zend\View\Resolver\TemplatePathStack');
        
        if ( !$resolver->resolve($template) ) {
            return $this->notFoundAction();
        }
        $view->setTemplate($template);
        return $view;
    }

    /**
     * 加载页面所需数据.
     * @param  String $pageName - 获取页面的名称
     * @return 一个包含页面所需数据的数组
     */
    private function getPageData($pageName) {
        $pageName   = ucfirst($pageName);
        $function   = 'get'.$pageName.'PageData';

        return $this->$function();
    }

    private function getDashboardPageData() {
    }

    /**
     * 获取用户管理页面所需数据.
     * @return 一个包含用户管理页面所需数据的数组
     */
    private function getUsersPageData() {
        return [
            'totalUsers'    => $this->getTotalUsers(),
            'uncheckUsers'  => $this->getUncheckUsers(),
        ];
    }

    /**
     * 获取所有用户的数量.
     * @return 所有用户的数量
     */
    private function getTotalUsers() {
        return $this->userTable->getCountUsingFilters();
    }

    /**
     * 获取未审核的用户的数量.
     * @return 未审核的用户的数量
     */
    private function getUncheckUsers() {
        $userGroupId    = 0;
        $isInspected    = 0;

        return $this->userTable->getCountUsingFilters($userGroupId, $isInspected);
    }

    /**
     * 通过用户组的唯一标识符获取用户组的唯一英文缩写.
     * @param  String $userGroupSlug - 用户组的唯一英文缩写
     * @return 用户组的唯一标识符
     */
    private function getUserGroupId($userGroupSlug) {
        $userGroup = $this->userGroupTable->getUserGroupUsingSlug($userGroupSlug);

        if ( $userGroup != null ) {
            return $userGroup->userGroupId;
        } 
        return 0;
    }

    /**
     * 根据筛选条件获取用户列表.
     * @return 一个包含用户信息的JSON数组
     */
    public function getUsersAction() {
        $NUMBER_OF_USERS_PER_PAGE   = 25;
        $userGroupSlug              = $this->params()->fromQuery('userGroup');
        $isInspected                = $this->params()->fromQuery('isInspected', -1);
        $isApproved                 = $this->params()->fromQuery('isApproved', -1);
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_USERS_PER_PAGE;
        $userGroupId                = $this->getUserGroupId($userGroupSlug);

        $users  = $this->userTable->getUsersUsingFilters($userGroupId, $isInspected, 
                    $isApproved, $offset, $NUMBER_OF_USERS_PER_PAGE);
        $result = [
            'isSuccessful'  => $users != null && $users->count() != 0,
            'users'         => $this->getResultSetArray($users),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 根据筛选条件获取用户列表分页总数.
     * @return 用户列表分页总数
     */
    public function getUserTotalPagesAction() {
        $NUMBER_OF_USERS_PER_PAGE   = 25;
        $userGroupSlug              = $this->params()->fromQuery('userGroup');
        $isApproved                 = $this->params()->fromQuery('isApproved', -1);
        $userGroupId                = $this->getUserGroupId($userGroupSlug);

        $totalPages = ceil($this->userTable->getCountUsingFilters($userGroupId, $isApproved) / $NUMBER_OF_USERS_PER_PAGE);
        $result     = [
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 使用关键字搜索用户.
     * @return 一个包含用户信息的JSON数组
     */
    public function getUsersUsingKeywordAction() {
        $NUMBER_OF_USERS_PER_PAGE   = 10;
        $keyword                    = $this->params()->fromQuery('keyword');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_USERS_PER_PAGE;

        $users  = $this->userTable->getUsersUsingKeyword($keyword, $offset, $NUMBER_OF_USERS_PER_PAGE);
        $result = [
            'isSuccessful'  => $users != null && $users->count() != 0,
            'users'         => $this->getResultSetArray($users),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取课程管理页面所需数据.
     * @return 一个包含课程管理页面所需数据的数组
     */
    private function getCoursesPageData() {
        $courseTypes    = $this->courseTypeTable->getAllCourseTypes();
        $totalCourses   = $this->getTotalCourses();
        $publicCourses  = $this->getPublicCourses();

        return [
            'totalCourses'      => $totalCourses,
            'publicCourses'     => $publicCourses,
            'nonPublicCourses'  => $totalCourses - $publicCourses,
            'courseTypes'       => $courseTypes,
        ];
    }

    /**
     * 获取课程的总数量.
     * @return 课程的总数量
     */
    private function getTotalCourses() {
        $courseTypeId   = 0;
        $isPublic       = -1;
        $isUserChecked  = -1;
        $totalCourses   = $this->courseTable->getCountUsingFilters($courseTypeId, $isPublic, $isUserChecked);

        return $totalCourses;
    }

    /**
     * 获取公开课的数量.
     * @return 公开课的数量
     */
    private function getPublicCourses() {
        $courseTypeId   = 0;
        $isPublic       = 1;
        $isUserChecked  = -1;
        $publicCourses  = $this->courseTable->getCountUsingFilters($courseTypeId, $isPublic, $isUserChecked);

        return $publicCourses;
    }

    /**
     * 获取课程列表.
     * @return 一个包含课程信息的JSON数组
     */
    public function getCoursesAction() {
        $NUMBER_OF_COURSES_PER_PAGE = 25;
        $courseTypeSlug             = $this->params()->fromQuery('category');
        $isPublic                   = $this->params()->fromQuery('isPublic', -1);
        $isUserChecked              = $this->params()->fromQuery('isUserChecked', -1);
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $courseTypeId               = $this->getCourseTypeId($courseTypeSlug);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $courses    = $this->courseTable->getCoursesUsingFilters($courseTypeId, 
                        $isPublic, $isUserChecked, $offset, $NUMBER_OF_COURSES_PER_PAGE);
        $result     = [
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取课程页面数量.
     * @return 一个包含课程页面数量的JSON数组
     */
    public function getCourseTotalPagesAction() {
        $NUMBER_OF_COURSES_PER_PAGE = 25;
        $courseTypeSlug             = $this->params()->fromQuery('category');
        $isPublic                   = $this->params()->fromQuery('isPublic', -1);
        $isUserChecked              = $this->params()->fromQuery('isUserChecked', -1);
        $courseTypeId               = $this->getCourseTypeId($courseTypeSlug);

        $totalPages = ceil($this->courseTable->getCountUsingFilters($courseTypeId, 
                        $isPublic, $isUserChecked) / $NUMBER_OF_COURSES_PER_PAGE);
        $result     = [
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
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
     * 根据关键字筛选课程.
     * @return 一个包含课程页面数量的JSON数组
     */
    public function getCoursesUsingKeywordAction() {
        $NUMBER_OF_COURSES_PER_PAGE = 10;
        $keyword                    = $this->params()->fromQuery('keyword');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_COURSES_PER_PAGE;

        $courses  = $this->courseTable->getCoursesUsingKeyword($keyword, $offset, $NUMBER_OF_COURSES_PER_PAGE);
        $result   = [
            'isSuccessful'  => $courses != null && $courses->count() != 0,
            'courses'       => $this->getResultSetArray($courses),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    private function getLecturesPageData() {
    }

    private function getRequirementsPageData() {
    }

    /**
     * 获取培训动态管理页面所需数据.
     * @return 一个包含培训动态管理页面所需数据的数组
     */
    private function getPostsPageData() {
        $postCategories = $this->postCategoryTable->getAllPostCategories();
        $publishMonths  = $this->postTable->getPushlishMonths();

        return [
            'postCategories'    => $this->getResultSetArray($postCategories),
            'publishMonths'     => $publishMonths,
        ];
    }

    /**
     * 根据培训动态的筛选条件获取培训动态的信息.
     * @return 一个包含培训动态信息的JSON数组
     */
    public function getPostsAction() {
        $NUMBER_OF_POSTS_PER_PAGE   = 25;
        $postCategorySlug           = $this->params()->fromQuery('category');
        $publishMonth               = $this->params()->fromQuery('publishMonth');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $postCategoryId             = $this->getPostCategoryId($postCategorySlug);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_POSTS_PER_PAGE;

        $posts  = $this->postTable->getPostsUsingFilters($postCategoryId, 
                    $publishMonth, $offset, $NUMBER_OF_POSTS_PER_PAGE);
        $result = [
            'isSuccessful'  => $posts != null && $posts->count() != 0,
            'posts'         => $this->getResultSetArray($posts),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 获取培训动态页面数量.
     * @return 一个包含培训动态页面数量的JSON数组
     */
    public function getPostTotalPagesAction() {
        $NUMBER_OF_POSTS_PER_PAGE   = 25;
        $postCategorySlug           = $this->params()->fromQuery('category');
        $publishMonth               = $this->params()->fromQuery('publishMonth');
        $postCategoryId             = $this->getPostCategoryId($postCategorySlug);

        $totalPages = ceil($this->postTable->getCountUsingFilters(
                        $postCategoryId, $publishMonth) / $NUMBER_OF_POSTS_PER_PAGE);
        $result     = [
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
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
     * 通过关键字筛选培训动态.
     * @return 一个包含培训动态信息的JSON数组
     */
    public function getPostsUsingKeywordAction() {
        $NUMBER_OF_POSTS_PER_PAGE = 10;
        $keyword                  = $this->params()->fromQuery('keyword');
        $pageNumber               = $this->params()->fromQuery('page', 1);
        $offset                   = ($pageNumber - 1) * $NUMBER_OF_POSTS_PER_PAGE;

        $posts  = $this->postTable->getPostsUsingKeyword($keyword, 
                    $offset, $NUMBER_OF_POSTS_PER_PAGE);
        $result = [
            'isSuccessful'  => $posts != null && $posts->count() != 0,
            'posts'         => $this->getResultSetArray($posts),
        ];
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 创建新的培训动态.
     * @return 一个包含若干标志位的JSON数组
     */
    public function newPostAction() {
        $postTitle          = strip_tags($this->getRequest()->getPost('postTitle'));
        $postCategorySlug   = strip_tags($this->getRequest()->getPost('postCategory'));
        $postContent        = strip_tags($this->getRequest()->getPost('postContent'));
        $postCategoryId     = $this->getPostCategoryId($postCategorySlug);
        $post               = [
            'post_title'        => $postTitle,
            'post_category_id'  => $postCategoryId,
            'post_content'      => $postContent,
        ];
        $result = $this->isPostLegal($post);
        
        if ( $result['isSuccessful'] ) {
            $result['isSuccessful'] = $this->postTable->createPost($post);
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 编辑培训动态.
     * @return 一个包含若干标志位的JSON数组
     */
    public function editPostAction() {
        $postId             = strip_tags($this->getRequest()->getPost('postId'));
        $postTitle          = strip_tags($this->getRequest()->getPost('postTitle'));
        $postCategorySlug   = strip_tags($this->getRequest()->getPost('postCategory'));
        $postContent        = strip_tags($this->getRequest()->getPost('postContent'));
        $postCategoryId     = $this->getPostCategoryId($postCategorySlug);
        $post               = [
            'post_id'           => $postId,
            'post_title'        => $postTitle,
            'post_category_id'  => $postCategoryId,
            'post_content'      => $postContent,
        ];
        $result = $this->isPostLegal($post);
        
        if ( $result['isSuccessful'] ) {
            if ( $postId ) {
                $result['isSuccessful'] = $this->postTable->updatePost($post);
            } else {
                $result['isSuccessful'] = $this->postTable->createPost($post);
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(Json::encode($result));
        return $response;
    }

    /**
     * 检查培训动态的信息是否合法.
     * @param  Array  $post - 一个包含培训动态信息的数组
     * @return 一个包含若干标志位的数组
     */
    private function isPostLegal($post) {
        $result = [
            'isPostTitleEmpty'      => empty($post['post_title']),
            'isPostTitleLegal'      => mb_strlen($post['post_title']) <= 128,
            'isPostCategoryEmpty'   => empty($post['post_category_id']),
            'isPostCategoryLegal'   => $post['post_category_id'] != 0,
            'isPostContentEmpty'    => empty($post['post_content']),
        ];
        $result['isSuccessful'] = !$result['isPostTitleEmpty']    && $result['isPostTitleLegal']    &&
                                  !$result['isPostCategoryEmpty'] && $result['isPostCategoryLegal'] &&
                                  !$result['isPostContentEmpty'];
        return $result;
    }

    private function getSettingsPageData() {
    }
    
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
     * The data access object of User.
     * @var UserTable
     */
    private $userTable;
    
    /**
     * The data access object of UserGroup.
     * @var UserGroupTable
     */
    private $userGroupTable;
}