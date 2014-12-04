<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 管理的Controller, 用于完成系统的监控和管理操作.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class AdministrationController extends AbstractActionController
{
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
     * 显示系统管理页面.
     * @return 一个包含页面所需信息的数组
     */
    public function indexAction()
    {
    	if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/dashboard');
        }

        return array(
            'profile'           => $this->getUserProfile(),
            'uncheckUsers'      => $this->getUncheckUsers(),
        );
    }

    /**
     * 检查用户是否已经登录.
     * @return 用户是否已经登录
     */
    private function isAllowedToAccess()
    {
        $session    = new Container('itp_session');
        return $session->offsetExists('isLogined');
    }

    /**
     * HTTP重定向请求.
     * @param  String $redirectPath - 重定向的相对路径
     * @return HTTP重定向请求的对象
     */
    private function sendRedirect($redirectPath)
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * 获取网站的基础路径(如localhost/itp).
     * @return 网站的基础路径
     */
    private function basePath()
    {
        $renderer   = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url        = $renderer->basePath();
        return $url;
    }

    /**
     * 获取用户的基本信息.
     * @return 一个包含用户基本信息的数组
     */
    private function getUserProfile()
    {
        $session    = new Container('itp_session');
        return array(
            'uid'           => $session->offsetGet('uid'),
            'username'      => $session->offsetGet('username'),
            'userGroupSlug' => $session->offsetGet('userGroupSlug'),
            'email'         => $session->offsetGet('email'),
        );
    }

    /**
     * 获取所请求页面的内容.
     * @return 包含页面内容的HTML字符串
     */
    public function getPageContentAction()
    {
        $pageName = $this->params()->fromQuery('pageName');
        $pageData = $this->getPageData($pageName);
        $view     = new ViewModel($pageData);
        $view->setTerminal(true);

        $template = "application/administration/dashboard/$pageName.phtml";
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
     * [getPageData description]
     * @param  [type] $pageName [description]
     * @return [type]           [description]
     */
    private function getPageData($pageName)
    {
        $pageName   = ucfirst($pageName);
        $function   = 'get'.$pageName.'PageData';

        return $this->$function();
    }

    private function getDashboardPageData()
    {

    }

    /**
     * 获取用户管理页面所需数据.
     * @return 一个包含用户管理页面所需数据的数组
     */
    private function getUsersPageData()
    {
        return array(
            'totalUsers'        => $this->getTotalUsers(),
            'uncheckUsers'      => $this->getUncheckUsers(),
        );
    }

    private function getCoursesPageData()
    {

    }

    private function getLecturesPageData()
    {
        
    }

    private function getRequirementsPageData()
    {
        
    }

    private function getPostsPageData()
    {
        
    }

    private function getSettingsPageData()
    {
        
    }

    /**
     * 获取所有用户的数量.
     * @return 所有用户的数量
     */
    private function getTotalUsers()
    {
        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');

        return $userTable->getCount();
    }

    /**
     * 获取未审核的讲师用户的数量.
     * @return 未审核的讲师用户的数量
     */
    private function getUncheckUsers()
    {
        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');

        return $teacherTable->getUncheckedCount();
    }

    /**
     * 根据筛选条件获取用户列表.
     * @return 一个包含用户信息的JSON数组
     */
    public function getUsersAction()
    {
        $NUMBER_OF_USERS_PER_PAGE   = 10;
        $userGroupSlug              = $this->params()->fromQuery('userGroup');
        $isApproved                 = $this->params()->fromQuery('isApproved');
        $pageNumber                 = $this->params()->fromQuery('page', 1);
        $offset                     = ($pageNumber - 1) * $NUMBER_OF_USERS_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');
        $users          = $userTable->getAllUsers($offset, $NUMBER_OF_USERS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $users != null && $users->count() != 0,
            'users'         => $this->getResultSetArray($users),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }
}