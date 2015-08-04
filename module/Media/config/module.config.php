<?php

return [
    'controllers'  => [
        'invokables' => [
            'Media\Controller\Media' => 'Media\Controller\MediaController',
        ],
    ],
    'router'       => [
        'routes' => [
            'media' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/media[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'Media\Controller\Media',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'media' => __DIR__ . '/../view',
        ],
    ],
];
