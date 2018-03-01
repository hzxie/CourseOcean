<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module {
    const VERSION = '2.0.0';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\AboutController::class => function($container) {
                    return new Controller\AboutController(
                        $container->get(Model\CourseTypeTable::class)
                    );
                },
                Controller\AccountsController::class => function($container) {
                    return new Controller\AccountsController(
                        $container->get(Model\CommentTable::class),
                        $container->get(Model\CompanyTable::class),
                        $container->get(Model\CompanyFieldTable::class),
                        $container->get(Model\CourseTable::class),
                        $container->get(Model\CourseCompositionTable::class),
                        $container->get(Model\CourseModuleTable::class),
                        $container->get(Model\CourseTypeTable::class),
                        $container->get(Model\EmailValidationTable::class),
                        $container->get(Model\LectureTable::class),
                        $container->get(Model\LectureAttendanceTable::class),
                        $container->get(Model\LectureScheduleTable::class),
                        $container->get(Model\PersonTable::class),
                        $container->get(Model\PositionTable::class),
                        $container->get(Model\RequirementTable::class),
                        $container->get(Model\TeacherTable::class),
                        $container->get(Model\TeachingFieldTable::class),
                        $container->get(Model\UserTable::class),
                        $container->get(Model\UserGroupTable::class)
                    );
                },
                Controller\AdministrationController::class => function($container) {
                    return new Controller\AdministrationController(
                        $container->get(Model\CourseTable::class), 
                        $container->get(Model\CourseTypeTable::class), 
                        $container->get(Model\PostCategoryTable::class), 
                        $container->get(Model\PostTable::class), 
                        $container->get(Model\UserTable::class), 
                        $container->get(Model\UserGroupTable::class)
                    );
                },
                Controller\HomeController::class => function($container) {
                    return new Controller\HomeController(
                        $container->get(Model\CourseTable::class),
                        $container->get(Model\LectureTable::class),
                        $container->get(Model\PostTable::class),
                        $container->get(Model\TeacherTable::class)
                    );
                },
                Controller\TrainingController::class => function($container) {
                    return new Controller\TrainingController(
                        $container->get(Model\CommentTable::class), 
                        $container->get(Model\CourseModuleTable::class), 
                        $container->get(Model\CourseTable::class), 
                        $container->get(Model\CourseTypeTable::class), 
                        $container->get(Model\LectureAttendanceTable::class), 
                        $container->get(Model\LectureTable::class), 
                        $container->get(Model\PostCategoryTable::class), 
                        $container->get(Model\PostTable::class), 
                        $container->get(Model\TeacherTable::class)
                    );
                },
            ],
        ];
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                Model\CommentTable::class => function($container) {
                    $tableGateway = $container->get(Model\CommentTableGateway::class);
                    return new Model\CommentTable($tableGateway);
                },
                Model\CommentTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Comment());
                    return new TableGateway('co_comments', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CompanyTable::class => function($container) {
                    $tableGateway = $container->get(Model\CompanyTableGateway::class);
                    return new Model\CompanyTable($tableGateway);
                },
                Model\CompanyTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Company());
                    return new TableGateway('co_companies', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CompanyFieldTable::class => function($container) {
                    $tableGateway = $container->get(Model\CompanyFieldTableGateway::class);
                    return new Model\CompanyFieldTable($tableGateway);
                },
                Model\CompanyFieldTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\CompanyField());
                    return new TableGateway('co_company_fields', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CourseTable::class => function($container) {
                    $tableGateway = $container->get(Model\CourseTableGateway::class);
                    return new Model\CourseTable($tableGateway);
                },
                Model\CourseTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Course());
                    return new TableGateway('co_courses', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CourseCompositionTable::class => function($container) {
                    $tableGateway = $container->get(Model\CourseCompositionTableGateway::class);
                    return new Model\CourseCompositionTable($tableGateway);
                },
                Model\CourseCompositionTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\CourseComposition());
                    return new TableGateway('co_course_composition', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CourseModuleTable::class => function($container) {
                    $tableGateway = $container->get(Model\CourseModuleTableGateway::class);
                    return new Model\CourseModuleTable($tableGateway);
                },
                Model\CourseModuleTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\CourseModule());
                    return new TableGateway('co_course_modules', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CourseTypeTable::class => function($container) {
                    $tableGateway = $container->get(Model\CourseTypeTableGateway::class);
                    return new Model\CourseTypeTable($tableGateway);
                },
                Model\CourseTypeTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\CourseType());
                    return new TableGateway('co_course_types', $dbAdapter, null, $resultSetPrototype);
                },
                Model\EmailValidationTable::class => function($container) {
                    $tableGateway = $container->get(Model\EmailValidationTableGateway::class);
                    return new Model\EmailValidationTable($tableGateway);
                },
                Model\EmailValidationTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\EmailValidation());
                    return new TableGateway('co_email_validation', $dbAdapter, null, $resultSetPrototype);
                },
                Model\LectureTable::class => function($container) {
                    $tableGateway = $container->get(Model\LectureTableGateway::class);
                    return new Model\LectureTable($tableGateway);
                },
                Model\LectureTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Lecture());
                    return new TableGateway('co_lectures', $dbAdapter, null, $resultSetPrototype);
                },
                Model\LectureAttendanceTable::class => function($container) {
                    $tableGateway = $container->get(Model\LectureAttendanceTableGateway::class);
                    return new Model\LectureAttendanceTable($tableGateway);
                },
                Model\LectureAttendanceTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\LectureAttendance());
                    return new TableGateway('co_lecture_attendance', $dbAdapter, null, $resultSetPrototype);
                },
                Model\LectureScheduleTable::class => function($container) {
                    $tableGateway = $container->get(Model\LectureScheduleTableGateway::class);
                    return new Model\LectureScheduleTable($tableGateway);
                },
                Model\LectureScheduleTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\LectureSchedule());
                    return new TableGateway('co_lecture_schedule', $dbAdapter, null, $resultSetPrototype);
                },
                Model\OptionTable::class => function($container) {
                    $tableGateway = $container->get(Model\OptionTableGateway::class);
                    return new Model\OptionTable($tableGateway);
                },
                Model\OptionTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Option());
                    return new TableGateway('co_options', $dbAdapter, null, $resultSetPrototype);
                },
                Model\PersonTable::class => function($container) {
                    $tableGateway = $container->get(Model\PersonTableGateway::class);
                    return new Model\PersonTable($tableGateway);
                },
                Model\PersonTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Person());
                    return new TableGateway('co_people', $dbAdapter, null, $resultSetPrototype);
                },
                Model\PositionTable::class => function($container) {
                    $tableGateway = $container->get(Model\PositionTableGateway::class);
                    return new Model\PositionTable($tableGateway);
                },
                Model\PositionTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Position());
                    return new TableGateway('co_positions', $dbAdapter, null, $resultSetPrototype);
                },
                Model\PostTable::class => function($container) {
                    $tableGateway = $container->get(Model\PostTableGateway::class);
                    return new Model\PostTable($tableGateway);
                },
                Model\PostTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Post());
                    return new TableGateway('co_posts', $dbAdapter, null, $resultSetPrototype);
                },
                Model\PostCategoryTable::class => function($container) {
                    $tableGateway = $container->get(Model\PostCategoryTableGateway::class);
                    return new Model\PostCategoryTable($tableGateway);
                },
                Model\PostCategoryTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\PostCategory());
                    return new TableGateway('co_post_categories', $dbAdapter, null, $resultSetPrototype);
                },
                Model\RequirementTable::class => function($container) {
                    $tableGateway = $container->get(Model\RequirementTableGateway::class);
                    return new Model\RequirementTable($tableGateway);
                },
                Model\RequirementTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Requirement());
                    return new TableGateway('co_requirements', $dbAdapter, null, $resultSetPrototype);
                },
                Model\TeacherTable::class => function($container) {
                    $tableGateway = $container->get(Model\TeacherTableGateway::class);
                    return new Model\TeacherTable($tableGateway);
                },
                Model\TeacherTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Teacher());
                    return new TableGateway('co_teachers', $dbAdapter, null, $resultSetPrototype);
                },
                Model\TeachingFieldTable::class => function($container) {
                    $tableGateway = $container->get(Model\TeachingFieldTableGateway::class);
                    return new Model\TeachingFieldTable($tableGateway);
                },
                Model\TeachingFieldTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\TeachingField());
                    return new TableGateway('co_teaching_fields', $dbAdapter, null, $resultSetPrototype);
                },
                Model\UserTable::class => function($container) {
                    $tableGateway = $container->get(Model\UserTableGateway::class);
                    return new Model\UserTable($tableGateway);
                },
                Model\UserTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\User());
                    return new TableGateway('co_users', $dbAdapter, null, $resultSetPrototype);
                },
                Model\UserGroupTable::class => function($container) {
                    $tableGateway = $container->get(Model\UserGroupTableGateway::class);
                    return new Model\UserGroupTable($tableGateway);
                },
                Model\UserGroupTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\UserGroup());
                    return new TableGateway('co_user_groups', $dbAdapter, null, $resultSetPrototype);
                }
            ],
        ];
    }

    /**
     * 初始化应用程序.
     * @param  MvcEvent $e - MvcEvent对象
     */
    public function onBootstrap(MvcEvent $e) {
        $serviceManager      = $e->getApplication()->getServiceManager();
        $optionTable         = $serviceManager->get('Application\Model\OptionTable');
        $viewModel           = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->options  = $this->getResultSetArray($optionTable->getAllOptions());
    }

    private function getResultSetArray($resultSet) {
        $returnArray = array();
        
        if ( $resultSet == null ) {
            return $returnArray;
        }
        foreach ( $resultSet as $rowSet ) {
            $returnArray[$rowSet->optionKey] = $rowSet->optionValue;
        }
        return $returnArray;
    }
}
