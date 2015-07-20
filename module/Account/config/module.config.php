<?php

return [
    'service_manager' => [
        'factories' => [
            'AppMail\Service\AppMailServiceInterface' => 'AppMail\Factory\AppMailServiceFactory',
        ],
    ],
    'controllers'     => [
        'factories' => [
            'Account\Controller\Account' => 'Account\Factory\AccountControllerFactory',
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
                        'id'     => '[a-zA-Z0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Account\Controller\Account',
                    ],
                ],
            ],
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            'account' => __DIR__ . '/../view',
        ],
    ],
];
