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
use Application\Model\UserGroup;
use Application\Model\UserGroupTable;
use Application\Model\Person;
use Application\Model\PersonTable;
use Application\Model\Position;
use Application\Model\PositionTable;
use Application\Model\Teacher;
use Application\Model\TeacherTable;
use Application\Model\TeachingField;
use Application\Model\TeachingFieldTable;
use Application\Model\Company;
use Application\Model\CompanyTable;
use Application\Model\CompanyField;
use Application\Model\CompanyFieldTable;
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
use Application\Model\LectureSchedule;
use Application\Model\LectureScheduleTable;
use Application\Model\Comment;
use Application\Model\CommentTable;
use Application\Model\LectureAttendance;
use Application\Model\LectureAttendanceTable;
use Application\Model\Post;
use Application\Model\PostTable;
use Application\Model\PostCategory;
use Application\Model\PostCategoryTable;
use Application\Model\EmailValidation;
use Application\Model\EmailValidationTable;


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
                'Application\Model\UserGroupTable' => function($sm) {
                    $tableGateway   = $sm->get('UserGroupTableGateway');
                    $userGroupTable = new UserGroupTable($tableGateway);
                    return $userGroupTable;
                },
                'UserGroupTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UserGroup());
                    return new TableGateway('itp_user_groups', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\PersonTable' => function($sm) {
                    $tableGateway   = $sm->get('PersonTableGateway');
                    $personTable    = new PersonTable($tableGateway);
                    return $personTable;
                },
                'PersonTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Person());
                    return new TableGateway('itp_people', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\PositionTable' => function($sm) {
                    $tableGateway   = $sm->get('PositionTableGateway');
                    $positionTable  = new PositionTable($tableGateway);
                    return $positionTable;
                },
                'PositionTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Position());
                    return new TableGateway('itp_positions', $dbAdapter, null, $resultSetPrototype);
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
                'Application\Model\TeachingFieldTable' => function($sm) {
                    $tableGateway       = $sm->get('TeachingFieldTableGateway');
                    $teachingFieldTable = new TeachingFieldTable($tableGateway);
                    return $teachingFieldTable;
                },
                'TeachingFieldTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TeachingField());
                    return new TableGateway('itp_teaching_fields', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CourseTable' => function($sm) {
                    $tableGateway       = $sm->get('CourseTableGateway');
                    $courseTable        = new CourseTable($tableGateway);
                    return $courseTable;
                },
                'Application\Model\CompanyTable' => function($sm) {
                    $tableGateway   = $sm->get('CompanyTableGateway');
                    $companyTable   = new CompanyTable($tableGateway);
                    return $companyTable;
                },
                'CompanyTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Company());
                    return new TableGateway('itp_companies', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CompanyFieldTable' => function($sm) {
                    $tableGateway       = $sm->get('CompanyFieldTableGateway');
                    $companyFieldTable  = new CompanyFieldTable($tableGateway);
                    return $companyFieldTable;
                },
                'CompanyFieldTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CompanyField());
                    return new TableGateway('itp_company_fields', $dbAdapter, null, $resultSetPrototype);
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
                    $lectureTable       = new LectureTable($tableGateway);
                    return $lectureTable;
                },
                'LectureTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Lecture());
                    return new TableGateway('itp_lectures', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\LectureScheduleTable' => function($sm) {
                    $tableGateway           = $sm->get('LectureScheduleTableGateway');
                    $lectureScheduleTable   = new LectureScheduleTable($tableGateway);
                    return $lectureScheduleTable;
                },
                'LectureScheduleTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LectureSchedule());
                    return new TableGateway('itp_lecture_schedule', $dbAdapter, null, $resultSetPrototype);
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
                'Application\Model\EmailValidationTable' => function($sm) {
                    $tableGateway       = $sm->get('EmailValidationTableGateway');
                    $emailValidationTable  = new EmailValidationTable($tableGateway);
                    return $emailValidationTable;
                },
                'EmailValidationTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmailValidation());
                    return new TableGateway('itp_email_validation', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
