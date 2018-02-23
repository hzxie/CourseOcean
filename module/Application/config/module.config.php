<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/[:action]',
                    'defaults' => [
                        'controller' => Controller\HomeController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'about' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/about[/:action]',
                    'defaults' => [
                        'controller' => Controller\AboutController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'accounts' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/accounts[/:action]',
                    'defaults' => [
                        'controller' => Controller\AccountsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'administration' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/administration[/:action]',
                    'defaults' => [
                        'controller' => Controller\AdministrationController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'training' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/training[/:action]',
                    'defaults' => [
                        'controller' => Controller\TrainingController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
