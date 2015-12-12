<?php
/**
 * Display all errors when APPLICATION_ENV is development
 */
if ($_SERVER['APPLICATION_ENV'] == 'development') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

if (version_compare(PHP_VERSION, '7.0', '<')) {
    function validateTypeHint($code, $error)
    {
        if ($code === E_RECOVERABLE_ERROR) {
            if (strpos($error, 'int, integer given') !== false) {
                return true;
            }

            if (strpos($error, 'string, string given') !== false) {
                return true;
            }

            if (strpos($error, 'bool, boolean given') !== false) {
                return true;
            }

            if (strpos($error, 'float, double given') !== false) {
                return true;
            }

            if (strpos($error, 'object, instance of') !== false) {
                return true;
            }
        }

        return false;
    }
    set_error_handler('validateTypeHint');
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
