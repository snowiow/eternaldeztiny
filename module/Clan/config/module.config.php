<?php

return [
    'controllers'  => [
        'invokables' => [
            'Clan\Controller\Clan' => 'Clan\Controller\ClanController',
        ],
    ],
    'router'       => [
        'routes' => [
            'clan' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/clan[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Clan\Controller\Clan',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'clan' => __DIR__ . '/../view',
        ],
    ],

];
