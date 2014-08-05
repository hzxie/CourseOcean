<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Application\Model\User;
use Application\Model\UserTable;
use Application\Model\Teacher;
use Application\Model\TeacherTable;
use Application\Model\Course;
use Application\Model\CourseTable;
use Application\Model\CourseModule;
use Application\Model\CourseModuleTable;
use Application\Model\CourseComposition;
use Application\Model\CourseCompositionTable;
use Application\Model\CourseType;
use Application\Model\CourseTypeTable;
use Application\Model\Lecture;
use Application\Model\LectureTable;
use Application\Model\Comment;
use Application\Model\CommentTable;
use Application\Model\LectureAttendance;
use Application\Model\LectureAttendanceTable;
use Application\Model\Post;
use Application\Model\PostTable;
use Application\Model\PostCategory;
use Application\Model\PostCategoryTable;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return an array of factories that are all merged together by the 
     *         ModuleManager before passing to the ServiceManager
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\UserTable' => function($sm) {
                    $tableGateway   = $sm->get('UserTableGateway');
                    $userTable      = new UserTable($tableGateway);
                    return $userTable;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('itp_users', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\TeacherTable' => function($sm) {
                    $tableGateway   = $sm->get('TeacherTableGateway');
                    $teacherTable   = new TeacherTable($tableGateway);
                    return $teacherTable;
                },
                'TeacherTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Teacher());
                    return new TableGateway('itp_teachers', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CourseTable' => function($sm) {
                    $tableGateway       = $sm->get('CourseTableGateway');
                    $courseTable        = new CourseTable($tableGateway);
                    return $courseTable;
                },
                'CourseTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Course());
                    return new TableGateway('itp_courses', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CourseModuleTable' => function($sm) {
                    $tableGateway       = $sm->get('CourseModuleTableGateway');
                    $courseModuleTable  = new CourseModuleTable($tableGateway);
                    return $courseModuleTable;
                },
                'CourseModuleTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseModule());
                    return new TableGateway('itp_course_modules', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CourseCompositionTable' => function($sm) {
                    $tableGateway           = $sm->get('CourseCompositionTableGateway');
                    $courseCompositionTable = new CourseCompositionTable($tableGateway);
                    return $courseCompositionTable;
                },
                'CourseCompositionTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseComposition());
                    return new TableGateway('itp_course_composition', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CourseTypeTable' => function($sm) {
                    $tableGateway       = $sm->get('CourseTypeTableGateway');
                    $courseTypeTable    = new CourseTypeTable($tableGateway);
                    return $courseTypeTable;
                },
                'CourseTypeTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseType());
                    return new TableGateway('itp_course_types', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\LectureTable' => function($sm) {
                    $tableGateway       = $sm->get('LectureTableGateway');
                    $lectureable        = new LectureTable($tableGateway);
                    return $lectureable;
                },
                'LectureTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Lecture());
                    return new TableGateway('itp_lectures', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CommentTable' => function($sm) {
                    $tableGateway       = $sm->get('CommentTableGateway');
                    $commentTable       = new CommentTable($tableGateway);
                    return $commentTable;
                },
                'CommentTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Comment());
                    return new TableGateway('itp_comments', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\LectureAttendanceTable' => function($sm) {
                    $tableGateway           = $sm->get('LectureAttendanceGateway');
                    $lectureAttendanceable  = new LectureAttendanceTable($tableGateway);
                    return $lectureAttendanceable;
                },
                'LectureAttendanceGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LectureAttendance());
                    return new TableGateway('itp_lecture_attendance', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\PostTable' => function($sm) {
                    $tableGateway   = $sm->get('PostTableGateway');
                    $postTable      = new PostTable($tableGateway);
                    return $postTable;
                },
                'PostTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Post());
                    return new TableGateway('itp_posts', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\PostCategoryTable' => function($sm) {
                    $tableGateway       = $sm->get('PostCategoryTableGateway');
                    $postCategoryTable  = new PostCategoryTable($tableGateway);
                    return $postCategoryTable;
                },
                'PostCategoryTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PostCategory());
                    return new TableGateway('itp_post_categories', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
