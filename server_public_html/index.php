<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Fix cPanel/LiteSpeed missing QUERY_STRING from REQUEST_URI
if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '?')) {
    $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    if ($queryString) {
        $_SERVER['QUERY_STRING'] = $queryString;
        parse_str($queryString, $parsedGet);
        $_GET = array_merge($_GET ?? [], $parsedGet);
        $_REQUEST = array_merge($_REQUEST ?? [], $parsedGet);
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../../promtinglibabry/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../../promtinglibabry/bootstrap/app.php';

$app->handleRequest(Request::capture());
