<?php

return [
    'controllers'  => [
        'invokables' => [
            'News\Controller\News'         => 'News\Controller\NewsController',
            'News\Controller\NewsCategory' => 'News\Controller\NewsCategoryController',
        ],
    ],
    'router'       => [
        'routes' => [
            'news'         => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/news[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'News\Controller\News',
                        'action'     => 'index',
                    ],
                ],
            ],
            'newscategory' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/newscategory[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => 'News\Controller\NewsCategory',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'news' => __DIR__ . '/../view',
        ],
    ],
];
