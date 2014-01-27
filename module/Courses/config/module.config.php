<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Courses\Controller\Recently'     => 'Courses\Controller\RecentlyController',
            'Courses\Controller\News'         => 'Courses\Controller\NewsController',
            'Courses\Controller\Library'      => 'Courses\Controller\LibraryController',
            'Courses\Controller\Teacher'      => 'Courses\Controller\TeacherController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'courses' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/courses',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Courses\Controller',
                        'controller'    => 'Library',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
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
            'courses' => __DIR__ . '/../view',
        ),
    ),
);