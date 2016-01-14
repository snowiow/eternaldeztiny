<?php

return [
    'modules'                 => [
        'Account',
        'Application',
        'ApplyNow',
        'AppMail',
        'Members',
        'News',
        'Impressum',
        'Warlog',
        'Warclaim',
        'Media',
        'Warstatus',
    ],
    'module_listener_options' => [
        'module_paths'      => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
    ],
];
