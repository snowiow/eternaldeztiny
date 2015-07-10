<?php

return [
    'service_manager' => [
        'factories' => [
            'AppMail\Service\AppMailServiceInterface' => 'AppMail\Factory\AppMailServiceFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            'ApplyNow\Controller\ApplyNow' => 'ApplyNow\Factory\ApplyNowControllerFactory',
        ],
    ],
    'router' => [
        'routes' => [
            'applynow' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/applynow[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'ApplyNow\Controller\ApplyNow',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'applynow' => __DIR__ . '/../view',
        ],
    ],
];
