<?php

namespace Courses;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Courses\Model\Course;
use Courses\Model\CourseType;
use Courses\Model\CourseTypeTable;
use Courses\Model\CourseTable;
use Courses\Model\CourseMeta;
use Courses\Model\CourseMetaTable;
use Courses\Model\Lecture;
use Courses\Model\LectureTable;
use Courses\Model\LectureMeta;
use Courses\Model\LectureMetaTable;
use Accounts\Model\Teacher;
use Accounts\Model\TeacherTable;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return an array of factories that are all merged together by the 
     *         ModuleManager before passing to the ServiceManager
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Courses\Model\CourseTable' => function($sm) {
                    $tableGateway = $sm->get('CourseTableGateway');
                    $table = new CourseTable($tableGateway);
                    return $table;
                },
                'CourseTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Course());
                    return new TableGateway('itp_courses', $dbAdapter, null, $resultSetPrototype);
                },
                'Courses\Model\CourseMetaTable' => function($sm) {
                    $tableGateway = $sm->get('CourseMetaTableGateway');
                    $table = new CourseMetaTable($tableGateway);
                    return $table;
                },
                'CourseMetaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseMeta());
                    return new TableGateway('itp_course_meta', $dbAdapter, null, $resultSetPrototype);
                },
                'Courses\Model\CourseTypeTable' => function($sm) {
                    $tableGateway = $sm->get('CourseTypeTableGateway');
                    $table = new CourseTypeTable($tableGateway);
                    return $table;
                },
                'CourseTypeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseType());
                    return new TableGateway('itp_course_types', $dbAdapter, null, $resultSetPrototype);
                },
                'Courses\Model\LectureTable' => function($sm) {
                    $tableGateway = $sm->get('LectureTableGateway');
                    $table = new LectureTable($tableGateway);
                    return $table;
                },
                'LectureTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Lecture());
                    return new TableGateway('itp_lectures', $dbAdapter, null, $resultSetPrototype);
                },
                'Courses\Model\LectureMetaTable' => function($sm) {
                    $tableGateway = $sm->get('LectureMetaTableGateway');
                    $table = new LectureMetaTable($tableGateway);
                    return $table;
                },
                'LectureMetaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LectureMeta());
                    return new TableGateway('itp_lecture_meta', $dbAdapter, null, $resultSetPrototype);
                },
                'Accounts\Model\TeacherTable' => function($sm) {
                    $tableGateway = $sm->get('TeacherTableGateway');
                    $table = new TeacherTable($tableGateway);
                    return $table;
                },
                'TeacherTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Teacher());
                    return new TableGateway('itp_teacher', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
