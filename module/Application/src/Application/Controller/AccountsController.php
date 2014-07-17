<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 账户的Controller, 用于完成账户的相关操作.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class AccountsController extends AbstractActionController
{
    /**
     * 显示用户登录页面.
     * @return 返回一个包含页面所需参数的数组
     */
    public function loginAction()
    {
        if ( $this->isEnableAutoLogin() ) {
            return $this->sendRedirect('accounts/dashboard');
        }
        $this->destroySession();

        $forwardUrl = $this->params()->fromQuery('forward', '/accounts/dashboard');
        return array(
            'forwardUrl'    => $forwardUrl,
        );
    }

    /**
     * 检查用户是否已经启用了自动登录功能.
     * @return 用户是否已经启用了自动登录功能
     */
    private function isEnableAutoLogin()
    {
        $session            = new Container('itp_session');

        $isEnableAutoLogin  = $session->offsetGet('allowAutoLogin');
        return $isEnableAutoLogin;
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
     * 销毁用户的Session.
     */
    private function destroySession()
    {
        $session    = new Container('itp_session');
        $session->getManager()->getStorage()->clear('itp_session');
    }

    /**
     * 处理用户的登录请求.
     * @return 包含若干标志位的JSON数组
     */
    public function doLoginAction()
    {
        $username       = $this->getRequest()->getPost('username');
        $password       = $this->getRequest()->getPost('password');
        $allowAutoLogin = $this->getRequest()->getPost('remember_me');

        $result     = array(
            'isSuccessful'      => false,
            'isUsernameEmpty'   => empty($username),
            'isPasswordEmpty'   => empty($password),
            'isAccountValid'    => false,
        );

        if ( !$result['isUsernameEmpty'] && !$result['isPasswordEmpty'] ) {
            $user = $this->verifyAccount($username, $password);

            if ( $user != null ) {
                $result['isAccountValid']   = true;
                $result['isSuccessful']     = true;
                $this->createSession($user, $allowAutoLogin);
            }
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 验证账户有效性.
     * @param  String $username - 用户名
     * @param  String $password - 密码
     * @return 一个用户对象或空引用
     */
    private function verifyAccount($username, $password)
    {
        if ( empty($username) || empty($password) ) {
            return false;
        }

        $serviceManager = $this->getServiceLocator();
        $userTable      = $serviceManager->get('Application\Model\UserTable');
        $user           = null;

        if ( !$this->isEmailAddress($username) ) {
            $user = $userTable->getUserUsingUsername($username);
        } else {
            $user = $userTable->getUserUsingEmail($username);
        }
        if ( $user == null ) {
            return null;
        } else if ( $user->password != md5($password) ) {
            return null;
        }
        return $user;
    }

    /**
     * 验证所使用的用户名是否为电子邮件地址.
     * @param  String  $username - 用户登录所使用的用户名
     * @return 所使用的用户名是否为电子邮件地址
     */
    private function isEmailAddress($username)
    {
        return strpos($username, '@');
    }

    /**
     * 为已登录的用户创建Session.
     * @param  User    $user           - 一个用户对象
     * @param  boolean $allowAutoLogin - 是否允许自动登陆
     */
    private function createSession($user, $allowAutoLogin)
    {
        $session    = new Container('itp_session');
        
        $session->offsetSet('isLogined', true);
        $session->offsetSet('allowAutoLogin', $allowAutoLogin);
        $session->offsetSet('uid', $user->uid);
        $session->offsetSet('username', $user->username);
        $session->offsetSet('email', $user->email);
        $session->offsetSet('userGroupSlug', $user->userGroupSlug);
    }

    /**
     * 处理用户的注销请求.
     * @return 一个HTTP重定向请求
     */
    public function logoutAction()
    {
        $this->destroySession();
        return $this->sendRedirect('accounts/login');
    }

    public function registerAction()
    {
        return array();
    }

    public function doRegisterAction()
    {

    }

    public function dashboardAction()
    {
        if ( !$this->isAllowedToAccess() ) {
            return $this->sendRedirect('accounts/login');
        }

        return array();
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
}