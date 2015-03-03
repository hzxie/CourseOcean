<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CourseOcean;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use CourseOcean\Model\User;
use CourseOcean\Model\UserTable;
use CourseOcean\Model\UserGroup;
use CourseOcean\Model\UserGroupTable;
use CourseOcean\Model\Person;
use CourseOcean\Model\PersonTable;
use CourseOcean\Model\Position;
use CourseOcean\Model\PositionTable;
use CourseOcean\Model\Teacher;
use CourseOcean\Model\TeacherTable;
use CourseOcean\Model\TeachingField;
use CourseOcean\Model\TeachingFieldTable;
use CourseOcean\Model\Company;
use CourseOcean\Model\CompanyTable;
use CourseOcean\Model\CompanyField;
use CourseOcean\Model\CompanyFieldTable;
use CourseOcean\Model\Course;
use CourseOcean\Model\CourseTable;
use CourseOcean\Model\CourseModule;
use CourseOcean\Model\CourseModuleTable;
use CourseOcean\Model\CourseComposition;
use CourseOcean\Model\CourseCompositionTable;
use CourseOcean\Model\CourseType;
use CourseOcean\Model\CourseTypeTable;
use CourseOcean\Model\Lecture;
use CourseOcean\Model\LectureTable;
use CourseOcean\Model\LectureSchedule;
use CourseOcean\Model\LectureScheduleTable;
use CourseOcean\Model\Comment;
use CourseOcean\Model\CommentTable;
use CourseOcean\Model\LectureAttendance;
use CourseOcean\Model\LectureAttendanceTable;
use CourseOcean\Model\Requirement;
use CourseOcean\Model\RequirementTable;
use CourseOcean\Model\Post;
use CourseOcean\Model\PostTable;
use CourseOcean\Model\PostCategory;
use CourseOcean\Model\PostCategoryTable;
use CourseOcean\Model\EmailValidation;
use CourseOcean\Model\EmailValidationTable;
use CourseOcean\Model\Option;
use CourseOcean\Model\OptionTable;

use CourseOcean\View\Helper\CdnHelper;


class Module implements ConfigProviderInterface, 
        AutoloaderProviderInterface, ViewHelperProviderInterface {
    /**
     * 初始化应用程序.
     * @param  MvcEvent $e - MvcEvent对象
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceManager      = $e->getApplication()->getServiceManager();
        $optionTable         = $serviceManager->get('CourseOcean\Model\OptionTable');
        $viewModel           = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->options  = $this->getResultSetArray($optionTable->getAllOptions());
    }

    private function getResultSetArray($resultSet)
    {
        $returnArray = array();
        
        if ( $resultSet == null ) {
            return $returnArray;
        }
        foreach ( $resultSet as $rowSet ) {
            $returnArray[$rowSet->optionKey] = $rowSet->optionValue;
        }
        return $returnArray;
    }

    /**
     * 获取应用模块的配置.
     * @return 包含应用模块的配置信息的数组
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * 获取自动加载的配置.
     * @return 一个包含应用配置信息的数组
     */
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
     * 获取ViewHelper的配置信息.
     * @return 一个包含ViewHelper信息的数组
     */
    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                /* CDN Service */
                'cdn' => function($sm) {
                    $request   = $sm->getServiceLocator()->get('Request');
                    $serviceLocator = $sm->getServiceLocator();
                    return new CdnHelper($request, $serviceLocator);
                },
            ),
        );
    }

    /**
     * 获取服务配置信息.
     * @return  包含服务配置信息的数组
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                /* Database and Models */
                'CourseOcean\Model\UserTable' => function($sm) {
                    $tableGateway   = $sm->get('UserTableGateway');
                    $userTable      = new UserTable($tableGateway);
                    return $userTable;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('co_users', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\UserGroupTable' => function($sm) {
                    $tableGateway   = $sm->get('UserGroupTableGateway');
                    $userGroupTable = new UserGroupTable($tableGateway);
                    return $userGroupTable;
                },
                'UserGroupTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UserGroup());
                    return new TableGateway('co_user_groups', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\PersonTable' => function($sm) {
                    $tableGateway   = $sm->get('PersonTableGateway');
                    $personTable    = new PersonTable($tableGateway);
                    return $personTable;
                },
                'PersonTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Person());
                    return new TableGateway('co_people', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\PositionTable' => function($sm) {
                    $tableGateway   = $sm->get('PositionTableGateway');
                    $positionTable  = new PositionTable($tableGateway);
                    return $positionTable;
                },
                'PositionTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Position());
                    return new TableGateway('co_positions', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\TeacherTable' => function($sm) {
                    $tableGateway   = $sm->get('TeacherTableGateway');
                    $teacherTable   = new TeacherTable($tableGateway);
                    return $teacherTable;
                },
                'TeacherTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Teacher());
                    return new TableGateway('co_teachers', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\TeachingFieldTable' => function($sm) {
                    $tableGateway       = $sm->get('TeachingFieldTableGateway');
                    $teachingFieldTable = new TeachingFieldTable($tableGateway);
                    return $teachingFieldTable;
                },
                'TeachingFieldTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TeachingField());
                    return new TableGateway('co_teaching_fields', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\CourseTable' => function($sm) {
                    $tableGateway       = $sm->get('CourseTableGateway');
                    $courseTable        = new CourseTable($tableGateway);
                    return $courseTable;
                },
                'CourseOcean\Model\CompanyTable' => function($sm) {
                    $tableGateway   = $sm->get('CompanyTableGateway');
                    $companyTable   = new CompanyTable($tableGateway);
                    return $companyTable;
                },
                'CompanyTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Company());
                    return new TableGateway('co_companies', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\CompanyFieldTable' => function($sm) {
                    $tableGateway       = $sm->get('CompanyFieldTableGateway');
                    $companyFieldTable  = new CompanyFieldTable($tableGateway);
                    return $companyFieldTable;
                },
                'CompanyFieldTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CompanyField());
                    return new TableGateway('co_company_fields', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Course());
                    return new TableGateway('co_courses', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\CourseModuleTable' => function($sm) {
                    $tableGateway       = $sm->get('CourseModuleTableGateway');
                    $courseModuleTable  = new CourseModuleTable($tableGateway);
                    return $courseModuleTable;
                },
                'CourseModuleTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseModule());
                    return new TableGateway('co_course_modules', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\CourseCompositionTable' => function($sm) {
                    $tableGateway           = $sm->get('CourseCompositionTableGateway');
                    $courseCompositionTable = new CourseCompositionTable($tableGateway);
                    return $courseCompositionTable;
                },
                'CourseCompositionTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseComposition());
                    return new TableGateway('co_course_composition', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\CourseTypeTable' => function($sm) {
                    $tableGateway       = $sm->get('CourseTypeTableGateway');
                    $courseTypeTable    = new CourseTypeTable($tableGateway);
                    return $courseTypeTable;
                },
                'CourseTypeTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CourseType());
                    return new TableGateway('co_course_types', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\LectureTable' => function($sm) {
                    $tableGateway       = $sm->get('LectureTableGateway');
                    $lectureTable       = new LectureTable($tableGateway);
                    return $lectureTable;
                },
                'LectureTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Lecture());
                    return new TableGateway('co_lectures', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\LectureScheduleTable' => function($sm) {
                    $tableGateway           = $sm->get('LectureScheduleTableGateway');
                    $lectureScheduleTable   = new LectureScheduleTable($tableGateway);
                    return $lectureScheduleTable;
                },
                'LectureScheduleTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LectureSchedule());
                    return new TableGateway('co_lecture_schedule', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\CommentTable' => function($sm) {
                    $tableGateway       = $sm->get('CommentTableGateway');
                    $commentTable       = new CommentTable($tableGateway);
                    return $commentTable;
                },
                'CommentTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Comment());
                    return new TableGateway('co_comments', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\LectureAttendanceTable' => function($sm) {
                    $tableGateway           = $sm->get('LectureAttendanceGateway');
                    $lectureAttendanceable  = new LectureAttendanceTable($tableGateway);
                    return $lectureAttendanceable;
                },
                'LectureAttendanceGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new LectureAttendance());
                    return new TableGateway('co_lecture_attendance', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\RequirementTable' => function($sm) {
                    $tableGateway       = $sm->get('RequirementTableGateway');
                    $requirementTable   = new RequirementTable($tableGateway);
                    return $requirementTable;
                },
                'RequirementTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Requirement());
                    return new TableGateway('co_requirements', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\PostTable' => function($sm) {
                    $tableGateway   = $sm->get('PostTableGateway');
                    $postTable      = new PostTable($tableGateway);
                    return $postTable;
                },
                'PostTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Post());
                    return new TableGateway('co_posts', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\PostCategoryTable' => function($sm) {
                    $tableGateway       = $sm->get('PostCategoryTableGateway');
                    $postCategoryTable  = new PostCategoryTable($tableGateway);
                    return $postCategoryTable;
                },
                'PostCategoryTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PostCategory());
                    return new TableGateway('co_post_categories', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\EmailValidationTable' => function($sm) {
                    $tableGateway       = $sm->get('EmailValidationTableGateway');
                    $emailValidationTable  = new EmailValidationTable($tableGateway);
                    return $emailValidationTable;
                },
                'EmailValidationTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new EmailValidation());
                    return new TableGateway('co_email_validation', $dbAdapter, null, $resultSetPrototype);
                },
                'CourseOcean\Model\OptionTable' => function($sm) {
                    $tableGateway       = $sm->get('OptionTableGateway');
                    $optionTable        = new OptionTable($tableGateway);
                    return $optionTable;
                },
                'OptionTableGateway' => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Option());
                    return new TableGateway('co_options', $dbAdapter, null, $resultSetPrototype);
                },
                /* Mail Transport */
                'CourseOcean\Mail\Transport' => function ($sm) {
                    $config    = $sm->get('Config');
                    $transport = new Smtp();                
                    $transport->setOptions(new SmtpOptions($config['mail']));
                    return $transport;
                },
            ),
        );
    }
}
