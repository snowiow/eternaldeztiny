<?php
return [
    'controllers'  => [
        'invokables' => [
            'Account\Controller\Account' => 'Account\Controller\AccountController',
        ],
    ],
    'router'       => [
        'routes' => [
            'album' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/account[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Account\Controller\Account',
                        'action'     => 'register',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'account' => __DIR__ . '/../view',
        ],
    ],
];
