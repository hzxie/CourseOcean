<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Solution\Controller\Lecture'      => 'Solution\Controller\LectureController',
            'Solution\Controller\News'         => 'Solution\Controller\NewsController',
            'Solution\Controller\Course'       => 'Solution\Controller\CourseController',
            'Solution\Controller\Teacher'      => 'Solution\Controller\TeacherController',
            'Solution\Controller\Requirement'  => 'Solution\Controller\RequirementController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'Solution' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/solution',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Solution\Controller',
                        'controller'    => 'Library',
                        'action'        => 'page',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:param]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'solution' => __DIR__ . '/../view',
        ),
    ),
);
