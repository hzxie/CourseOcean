<?php

namespace Accounts\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

/**
 * 处理用户登录请求的控制器.
 * 
 * @author 谢浩哲 <zjhzxhz@gmail.com>
 */
class LoginController extends AbstractActionController
{
    /**
     * 该控制器的默认方法, 显示用户登录页面.
     * @return 一个包含了HTML内容的ViewModel对象
     */
    public function indexAction()
    {
        if ( $this->isEnableAutoLogin() ) {
            return $this->sendRedirect('accounts/dashboard');
        }

        $this->destroySession();
        $param  = $this->params()->fromQuery();
        return array(
            'redirectUrl'   => $this->getRedirectUrl($param),
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
     * 获取访问登陆页面时的相关参数, 以便在登陆后跳转至登录前的页面.
     * @param  Array $param - 通过GET请求的相关参数, 包含了登录前页面的相关信息
     * @return 需要重定向到的URL
     */
    private function getRedirectUrl($param)
    {
        $basePath       = $this->basePath('');

        foreach ( $param as $key => $value ) {
            if ( $key == 'lectureId' ) {
                return $basePath.'/solutions/lecture/detail/'.$value;
            } else {
                return $basePath.'/accounts/dashboard';
            }
        }
    }

    /**
     * 获取网站的基础路径. (如localhost/itp)
     * @return 网站的基础路径
     */
    private function basePath()
    {
        $renderer   = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url        = $renderer->basePath();
        return $url;
    }

    /**
     * 处理用户的登录请求.
     * @return 包含若干标志位的JSON数组
     */
    public function processAction()
    {
        $username       = $this->getRequest()->getPost('username');
        $password       = $this->getRequest()->getPost('password');
        $allowAutoLogin = $this->getRequest()->getPost('remember_me');

        $result     = array(
            'isSuccessful'      => false,
            'isUsernameEmpty'   => empty($username),
            'isPasswordEmpty'   => empty($password),
            'isAccountValid'    => $this->verifyAccount($username, $password),
        );

        if ( $result['isAccountValid'] ) {
            $userData   = $this->parseUserData($result['isAccountValid']);
            $this->createSession($userData, $allowAutoLogin);
            $result['isSuccessful'] = true;
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }

    /**
     * 验证用户的用户名和密码是否合法.
     * @param  String $username - 用户的用户名/电子邮件地址
     * @param  String $password - 用户的密码
     * @return 用户的用户名和密码是否合法
     */
    private function verifyAccount($username, $password)
    {
        if ( empty($username) || empty($password) ) {
            return false;
        }

        $sm                 = $this->getServiceLocator();
        $userTable          = $sm->get('Accounts\Model\UserTable');

        if ( !$this->isEmailAddress($username) ) {
            return $userTable->verifyAccountByUsername($username, md5($password));
        } else {
            return $userTable->verifyAccountByEmail($username, md5($password));
        }
    }

    /**
     * 检查用户的登陆凭据是否为电子邮件地址.
     * @param  String  $username - 用户的登陆凭据
     * @return 用户的登陆凭据是否为电子邮件地址
     */
    private function isEmailAddress($username)
    {
        return strpos($username, '@');
    }

    /**
     * 获取用户的基本信息, 以便存储至Session中.
     * @param  User $user - 一个包含了用户基本信息的User对象
     * @return 一个包含用户基本信息的数组
     */
    private function parseUserData($user)
    {
        $userData = array(
            'uid'               => $user->uid,
            'username'          => $user->username,
            'email'             => $user->email,
            'isActivated'       => $user->is_activated,
            'userGroupId'       => $user->user_group_id,
            'lastTimeSignIn'    => $user->last_time_signin,
        );

        return $userData;
    }

    /**
     * 为已登录的用户创建Session.
     * @param  Array $userData - 用户的基本信息
     */
    private function createSession($userData, $allowAutoLogin)
    {
        $session    = new Container('itp_session');
        
        $session->offsetSet('isLogined', true);
        $session->offsetSet('allowAutoLogin', $allowAutoLogin);
        $session->offsetSet('uid', $userData['uid']);
        $session->offsetSet('username', $userData['username']);
        $session->offsetSet('email', $userData['email']);
        $session->offsetSet('isActivated', $userData['isActivated']);
        $session->offsetSet('userGroupSlug', $this->getUserGroupSlug($userData['userGroupId']));
        $session->offsetSet('lastTimeSignIn', $userData['lastTimeSignIn']);
    }

    /**
     * 通过用户组的唯一简写(slug)以获取用户组的唯一标识符.
     * @param  String $userGroupSlug - 用户组的唯一简写
     * @return 用户组的唯一标识符
     */
    private function getUserGroupID($userGroupSlug)
    {
        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupID($userGroupSlug);

        if ( $userGroup == null ) {
            return null;
        }
        return $userGroup->user_group_id;
    }

    /**
     * 通过用户组的唯一标识符以获取用户组的唯一简写(slug).
     * @param  int $userGroupId - 用户组的唯一标识符
     * @return 用户组的唯一简写(slug)
     */
    private function getUserGroupSlug($userGroupId)
    {
        $sm                 = $this->getServiceLocator();
        $userGroupTable     = $sm->get('Accounts\Model\UserGroupTable');
        $userGroup          = $userGroupTable->getUserGroupSlug($userGroupId);

        if ( $userGroup == null ) {
            return null;
        }
        return ucfirst($userGroup->user_group_slug);
    }


    /**
     * 处理用户的注销请求.
     * @return 一个包含了HTML内容的ViewModel对象
     */
    public function logoutAction()
    {
        $this->destroySession();

        return $this->sendRedirect();
    }

    /**
     * HTTP重定向请求.
     * @param  String $redirectPath - 重定向的相对路径
     * @return HTTP重定向请求的对象.
     */
    private function sendRedirect($redirectPath = '')
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $url = $renderer->basePath($redirectPath);
        $redirect = $this->plugin('redirect');

        return $redirect->toUrl($url);
    }

    /**
     * 销毁用户的Session.
     */
    private function destroySession()
    {
        $session    = new Container('itp_session');
        $session->getManager()->getStorage()->clear('itp_session');
    }
}
