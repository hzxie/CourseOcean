<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;

use Application\Controller\BaseController;
use Application\Model\CourseTypeTable;

/**
 * 文档的Controller, 用于显示网站相关的文档.
 * 
 * @author Haozhe Xie <cshzxie@gmail.com>
 */
class AboutController extends BaseController {
    /**
     * AboutController的构造函数. 
     */
    public function __construct(CourseTypeTable $courseTypeTable) {
        $this->courseTypeTable = $courseTypeTable;
    }

    /**
     * 加载[关于我们]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function indexAction() {
        return new ViewModel();
    }

    /**
     * 加载[合作伙伴]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function partnerAction() {
        return new ViewModel();
    }

    /**
     * 加载[诚招英才]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function joinAction() {
        return new ViewModel();
    }

    /**
     * 加载[帮助中心]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function supportAction() {
        return new ViewModel();
    }

    /**
     * 加载[意见反馈]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function feedbackAction() {
        return new ViewModel();
    }

    /**
     * 加载[服务条款]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function termAction() {
        return new ViewModel();
    }

    /**
     * 加载[开放平台]页面.
     * @return 一个包含页面所需信息的数组
     */
    public function apiAction() {
        $courseTypes        = $this->courseTypeTable->getAllCourseTypes();

        return new ViewModel([
            'courseTypes'   => $courseTypes,
        ]);
    }

    /**
     * The data access object of CourseType.
     * @var CourseTypeTable
     */
    private $courseTypeTable;
}
