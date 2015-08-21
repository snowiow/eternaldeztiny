<?php

return [
    'service_manager' => [
        'factories' => [
            'AppMail\Service\AppMailServiceInterface' => 'AppMail\Factory\AppMailServiceFactory',
        ],
    ],
    'controllers'     => [
        'factories' => [
            'Warclaim\Controller\Warclaim' => 'Warclaim\Factory\WarclaimControllerFactory',
        ],
    ],
    'router'          => [
        'routes' => [
            'warclaim' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/warclaim[/:action][/:id][/:size][/:opponent]',
                    'constraints' => [
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'       => '[0-9]+',
                        'size'     => '[0-9]+',
                        'opponent' => '[\w\%\d\.,_-]*',
                    ],
                    'defaults'    => [
                        'controller' => 'Warclaim\Controller\Warclaim',
                        'action'     => 'current',
                    ],
                ],
            ],
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            'warclaim' => __DIR__ . '/../view',
        ],
    ],
];
