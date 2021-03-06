<?php

return [
    'service_manager' => [
        'factories' => [
            'AppMail\Service\AppMailServiceInterface' => 'AppMail\Factory\AppMailServiceFactory',
        ],
    ],
    'controllers'     => [
        'factories'  => [
            'Account\Controller\Account' => 'Account\Factory\AccountControllerFactory',
            'Account\Controller\Auth'    => 'Account\Factory\AuthControllerFactory',
        ],
        'invokables' => [
            'Account\Controller\Admin' => 'Account\Controller\AdminController',
        ],
    ],
    'router'          => [
        'routes' => [
            'account' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/account[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Account\Controller\Account',
                    ],
                ],
            ],
            'admin'   => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/admin[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Account\Controller\Admin',
                    ],
                ],
            ],
            'auth'    => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/auth[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Account\Controller\Auth',
                    ],
                ],
            ],
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            'account' => __DIR__ . '/../view',
            'admin'   => __DIR__ . '/../view',
            'auth'    => __DIR__ . '/../view',
        ],
    ],
];
