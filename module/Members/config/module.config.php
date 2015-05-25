<?php

return [
    'controllers'  => [
        'invokables' => [
            'Members\Controller\Members' => 'Members\Controller\MembersController',
        ],
    ],
    'router'       => [
        'routes' => [
            'members' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/members[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Members\Controller\Members',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'members' => __DIR__ . '/../view',
        ],
    ],
];
