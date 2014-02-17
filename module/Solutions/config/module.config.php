<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Solutions\Controller\Lecture'      => 'Solutions\Controller\LectureController',
            'Solutions\Controller\News'         => 'Solutions\Controller\NewsController',
            'Solutions\Controller\Course'       => 'Solutions\Controller\CourseController',
            'Solutions\Controller\Teacher'      => 'Solutions\Controller\TeacherController',
            'Solutions\Controller\Requirement'  => 'Solutions\Controller\RequirementController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'Solutions' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/solutions',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Solutions\Controller',
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
            'solutions' => __DIR__ . '/../view',
        ),
    ),
);
