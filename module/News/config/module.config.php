<?php

return array(
     'controllers' => array(
         'invokables' => array(
             'News\Controller\News' => 'News\Controller\NewsController',
         ),
     ),
     'router' => array(
        'routes' => array(
            'news' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/news[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'News\Controller\News',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'news' => __DIR__ . '/../view',
         ),
     ),
 );
