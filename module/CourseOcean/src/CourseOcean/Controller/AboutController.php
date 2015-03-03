<?php

namespace CourseOcean\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 文档的Controller, 用于显示网站相关的文档.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class AboutController extends AbstractActionController
{
    /**
     * 加载[关于我们]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function indexAction() 
    {
        return array();
    }

    /**
     * 加载[合作伙伴]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function partnerAction() 
    {
        return array();
    }

    /**
     * 加载[诚招英才]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function joinAction() 
    {
        return array();
    }

    /**
     * 加载[帮助中心]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function supportAction() 
    {
        return array();
    }

    /**
     * 加载[意见反馈]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function feedbackAction()
    {
        return array();
    }

    /**
     * 加载[服务条款]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function termAction() 
    {
        return array();
    }

    /**
     * 加载[开放平台]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function apiAction() 
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('CourseOcean\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->getAllCourseTypes();

        return array(
            'courseTypes'   => $courseTypes,
        );
    }
}