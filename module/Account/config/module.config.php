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
                        'id'     => '[a-zA-Z]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Account\Controller\Admin',
                    ],
                ],
            ],
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            'account' => __DIR__ . '/../view',
            'admin'   => __DIR__ . '/../view',
        ],
    ],
];
