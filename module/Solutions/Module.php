<?php

namespace Solutions;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Solutions\Model\Course;
use Solutions\Model\CourseTable;
use Solutions\Model\CourseType;
use Solutions\Model\CourseTypeTable;
use Solutions\Model\Lecture;
use Solutions\Model\LectureTable;
use Solutions\Model\LectureAttendance;
use Solutions\Model\LectureAttendanceTable;
use Solutions\Model\Comment;
use Solutions\Model\CommentTable;
use Solutions\Model\News;
use Solutions\Model\NewsTable;
use Solutions\Model\NewsCategory;
use Solutions\Model\NewsCategoryTable;
use Solutions\Model\Requirement;
use Solutions\Model\RequirementTable;
use Accounts\Model\Person;
use Accounts\Model\PersonTable;
use Accounts\Model\Teacher;
use Accounts\Model\TeacherTable;
use Accounts\Model\Company;
use Accounts\Model\CompanyTable;

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
                'Solutions\Model\CourseTable' => function($sm) {
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
                'Solutions\Model\CourseTypeTable' => function($sm) {
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
                'Solutions\Model\LectureTable' => function($sm) {
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
                'Solutions\Model\LectureAttendanceTable' => function($sm) {
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
                'Solutions\Model\CommentTable' => function($sm) {
                    $tableGateway = $sm->get('CommentTableGateway');
                    $table = new CommentTable($tableGateway);
                    return $table;
                },
                'CommentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Comment());
                    return new TableGateway('itp_comments', $dbAdapter, null, $resultSetPrototype);
                },
                'Solutions\Model\NewsTable' => function($sm) {
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
                'Solutions\Model\NewsCategoryTable' => function($sm) {
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
                'Solutions\Model\RequirementTable' => function($sm) {
                    $tableGateway = $sm->get('RequirementTableGateway');
                    $table = new RequirementTable($tableGateway);
                    return $table;
                },
                'RequirementTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Requirement());
                    return new TableGateway('itp_requirements', $dbAdapter, null, $resultSetPrototype);
                },
                'Accounts\Model\PersonTable' => function($sm) {
                    $tableGateway = $sm->get('PersonTableGateway');
                    $table = new PersonTable($tableGateway);
                    return $table;
                },
                'PersonTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Person());
                    return new TableGateway('itp_people', $dbAdapter, null, $resultSetPrototype);
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
                'Accounts\Model\CompanyTable' => function($sm) {
                    $tableGateway = $sm->get('CompanyTableGateway');
                    $table = new CompanyTable($tableGateway);
                    return $table;
                },
                'CompanyTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Company());
                    return new TableGateway('itp_companies', $dbAdapter, null, $resultSetPrototype);
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
