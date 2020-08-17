<?php

ini_set('memory_limit', '32M');
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 1000');
}
if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header('Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization');
    }
    exit(0);
}
function __autoload($class_name)
{
    if (file_exists('./classes/'.$class_name.'.php')) {
        require_once './classes/'.$class_name.'.php';
    } elseif (file_exists('./model/'.$class_name.'.php')) {
        require_once './model/'.$class_name.'.php';
    } elseif (file_exists('./controller/'.$class_name.'.php')) {
        require_once './controller/'.$class_name.'.php';
    } elseif (0 == strncmp($class_name, 'DB_', 3)) {
        require_once './classes/Database.php';
    }
}

require_once 'routes.php';
