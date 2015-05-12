<?php

return [
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            'appmail' => __DIR__ . '/../view',
        ],
    ],
];