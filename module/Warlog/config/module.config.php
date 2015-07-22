<?php

return [
    'controllers'  => [
        'invokables' => [
            'Warlog\Controller\Warlog' => 'Warlog\Controller\WarlogController',
        ],
    ],
    'router'       => [
        'routes' => [
            'warlog' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/warlog[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Warlog\Controller\Warlog',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'warlog' => __DIR__ . '/../view',
        ],
    ],
];
