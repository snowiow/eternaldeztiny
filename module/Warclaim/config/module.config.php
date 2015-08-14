<?php

return [
    'controllers'  => [
        'invokables' => [
            'Warclaim\Controller\Warclaim' => 'Warclaim\Controller\WarclaimController',
        ],
    ],
    'router'       => [
        'routes' => [
            'warclaim' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/warclaim[/:action][/:size][/:opponent]',
                    'constraints' => [
                        'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'size'     => '[0-9]+',
                        'opponent' => '[\w\%\d_-]*',
                    ],
                    'defaults'    => [
                        'controller' => 'Warclaim\Controller\Warclaim',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'warclaim' => __DIR__ . '/../view',
        ],
    ],
];
