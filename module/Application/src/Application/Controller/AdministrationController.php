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
     * 显示系统管理页面.
     * @return 一个包含页面所需信息的数组
     */
    public function dashboardAction()
    {
        return array();
    }
}