<?php

return [
    'controllers'  => [
        'invokables' => [
            'Warstatus\Controller\Warstatus' => 'Warstatus\Controller\WarstatusController',
        ],
    ],
    'router'       => [
        'routes' => [
            'warstatus' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/warstatus[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-z-A-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Warstatus\Controller\Warstatus',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'warstatus' => __DIR__ . '/../view',
        ],
    ],
];
