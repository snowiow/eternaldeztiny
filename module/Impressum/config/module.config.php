<?php

return [
    'controllers'  => [
        'invokables' => [
            'Impressum\Controller\Impressum' => 'Impressum\Controller\ImpressumController',
        ],
    ],
    'router'       => [
        'routes' => [
            'impressum' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/impressum[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults'    => [
                        'controller' => 'Impressum\Controller\Impressum',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'impressum' => __DIR__ . '/../view',
        ],
    ],
];
