<?php

declare(strict_types=1);

if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
    \header('Access-Control-Allow-Origin: *');
    \header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    \header('Access-Control-Allow-Headers: Authorization, Content-Type');
    \header('Access-Control-Max-Age: 1728000');
    \header('Content-Length: 0');
    exit;
}

use App\Kernel;

require_once \dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function (array $context): Kernel {
    \header('Access-Control-Allow-Origin: *');

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
