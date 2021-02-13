<?php

if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
    \header('Access-Control-Allow-Origin: *');
    \header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    \header('Access-Control-Allow-Headers: Authorization, Content-Type');
    \header('Access-Control-Max-Age: 1728000');
    \header('Content-Length: 0');
    exit();
}

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require \dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(\dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    \umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->headers->set('Access-Control-Allow-Origin', '*');

$response->send();
$kernel->terminate($request, $response);
