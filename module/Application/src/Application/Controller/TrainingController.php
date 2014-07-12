<?php

namespace Application\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * 课程的Controller, 用于完成课程的相关操作.
 * 
 * @author Xie Haozhe <zjhzxhz@gmail.com>
 */
class TrainingController extends AbstractActionController
{
    /**
     * 通过课程类型的唯一英文缩写查找课程类型的唯一标识符.
     * @param  String $catelogySlug - 课程类型的唯一英文缩写
     * @return 课程类型的唯一标识符
     */
    private function getCourseTypeId($catelogySlug)
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseType         = $courseTypeTable->getCatelogyUsingId($catelogySlug);

        if ( $courseType != null ) {
            return $courseType->courseTypeId;
        } 
        return 0;
    }

    /**
     * 将ResultSet对象转换为数组.
     * @param  ResultSet $resultSet - 数据库查询返回的ResultSet对象
     * @return 一个包含查询结果的数组
     */
    private function getResultSetArray($resultSet)
    {
        $returnArray = array();
        
        foreach ( $resultSet as $rowSet ) {
            $rowArray = (array)$rowSet;
            array_push($returnArray, $rowArray);
        }
        return $returnArray;
    }

    public function lecturesAction()
    {

    }
    
    public function coursesAction()
    {

    }

    /**
     * 显示讲师团队页面.
     * @return 返回一个包含页面所需参数的数组
     */
    public function teachersAction()
    {
        $serviceManager     = $this->getServiceLocator();
        $courseTypeTable    = $serviceManager->get('Application\Model\CourseTypeTable');
        $courseTypes        = $courseTypeTable->getAllCourseTypes();

        return array(
            'courseTypes'   => $courseTypes,
        );
    }

    /**
     * 获取讲师列表.
     * @return 一个包含讲师信息的JSON数组.
     */
    public function getTeachersAction()
    {
        $NUMBER_OF_TEACHERS_PER_PAGE    = 18;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $pageNumber                     = $this->params()->fromQuery('page', 1);
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);
        $offset                         = ($pageNumber - 1) * $NUMBER_OF_TEACHERS_PER_PAGE;

        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');
        $teachers       = null;

        if ( $courseTypeSlug === 'all' ) {
            $teachers   = $teacherTable->getAllApprovedTeachers($offset, $NUMBER_OF_TEACHERS_PER_PAGE);
        } else if ( $courseTypeId != 0 ) {
            $teachers   = $teacherTable->getTeachersUsingCategory($courseTypeId, $offset, $NUMBER_OF_TEACHERS_PER_PAGE);
        }

        $result   = array(
            'isSuccessful'  => $teachers !== null && $teachers->count() !== 0,
            'teachers'      => $this->getResultSetArray($teachers),
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }
    
    /**
     * 返回讲师的页面数量.
     * @return 讲师的页面数量
     */
    public function getTeacherTotalPagesAction()
    {
        $NUMBER_OF_TEACHERS_PER_PAGE    = 18;
        $courseTypeSlug                 = $this->params()->fromQuery('category');
        $courseTypeId                   = $this->getCourseTypeId($courseTypeSlug);

        $serviceManager = $this->getServiceLocator();
        $teacherTable   = $serviceManager->get('Application\Model\TeacherTable');
        $totalPages     = ceil($teacherTable->getCount($courseTypeId) / $NUMBER_OF_TEACHERS_PER_PAGE);

        $result   = array(
            'isSuccessful'  => $totalPages != 0,
            'totalPages'    => $totalPages,
        );
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent( Json::encode($result) );
        return $response;
    }
}