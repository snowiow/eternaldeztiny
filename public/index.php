<?php
/**
 * Display all errors when APPLICATION_ENV is development
 */
if ($_SERVER['APPLICATION_ENV'] == 'development') {
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}

if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    define('BASIC_TYPE_HINT_REGEX', '#^Argument \d+ passed to .+? must be an instance of ([a-z]+[\w_\\\]*), ([a-z]+) given#i');

    function basic_type_hint($err_lvl, $err_msg) {
        if ($err_lvl == E_RECOVERABLE_ERROR) {
            if (preg_match(BASIC_TYPE_HINT_REGEX, $err_msg, $matches)) {

                if (strpos($matches[1], '\\') !== false) {
                    $arr = explode('\\', $matches[1]);
                    $matches[1] = array_pop($arr);
                }

                if ($matches[1] === $matches[2]) {
                    return true;
                }

                switch ($matches[1]) {
                    case 'int':
                        return $matches[2] === 'integer';
                    case 'bool':
                        return $matches[2] === 'boolean';
                    case 'float':
                        return $matches[2] === 'double';
                    default:
                        return false;
                }
            }
        }

        return false;
    }

    set_error_handler('basic_type_hint');
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
