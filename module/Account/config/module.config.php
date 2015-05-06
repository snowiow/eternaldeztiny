<?php
return array(
     'controllers' => array(
         'invokables' => array(
             'Account\Controller\Account' => 'Account\Controller\AccountController',
         ),
     ),

    'router' => array(
        'routes' => array(
            'album' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/account[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Album',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
     'view_manager' => array(
         'template_path_stack' => array(
             'account' => __DIR__ . '/../view',
         ),
     ),
 );
