<?php

namespace Solution;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Solution\Model\Course;
use Solution\Model\CourseType;
use Solution\Model\CourseTypeTable;
use Solution\Model\CourseTable;
use Solution\Model\CourseMeta;
use Solution\Model\CourseMetaTable;
use Solution\Model\Lecture;
use Solution\Model\LectureTable;
use Solution\Model\LectureMeta;
use Solution\Model\LectureMetaTable;
use Solution\Model\LectureAttendance;
use Solution\Model\LectureAttendanceTable;
use Solution\Model\News;
use Solution\Model\NewsTable;
use Solution\Model\NewsCategory;
use Solution\Model\NewsCategoryTable;
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
                'Solution\Model\CourseTable' => function($sm) {
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
                'Solution\Model\CourseMetaTable' => function($sm) {
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
                'Solution\Model\CourseTypeTable' => function($sm) {
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
                'Solution\Model\LectureTable' => function($sm) {
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
                'Solution\Model\LectureMetaTable' => function($sm) {
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
                'Solution\Model\LectureAttendanceTable' => function($sm) {
                    $tableGateway = $sm->get('LectureAttendanceTableGateway');
                    $table = new LectureAttendanceTable($tableGateway);
                    return $table;
                },
                'LectureAttendanceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LectureAttendance());
                    return new TableGateway('itp_lecture_attendance', $dbAdapter, null, $resultSetPrototype);
                },
                'Solution\Model\NewsTable' => function($sm) {
                    $tableGateway = $sm->get('NewsTableGateway');
                    $table = new NewsTable($tableGateway);
                    return $table;
                },
                'NewsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new News());
                    return new TableGateway('itp_news', $dbAdapter, null, $resultSetPrototype);
                },
                'Solution\Model\NewsCategoryTable' => function($sm) {
                    $tableGateway = $sm->get('NewsCategoryTableGateway');
                    $table = new NewsCategoryTable($tableGateway);
                    return $table;
                },
                'NewsCategoryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new NewsCategory());
                    return new TableGateway('itp_news_categories', $dbAdapter, null, $resultSetPrototype);
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
                    return new TableGateway('itp_teachers', $dbAdapter, null, $resultSetPrototype);
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
