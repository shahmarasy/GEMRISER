<?php

declare(strict_types=1);

use Gemriser\Http\Kernel;
use Gemriser\Http\Middleware\CsrfMiddleware;
use Gemriser\Http\Middleware\EncryptCookiesMiddleware;
use Gemriser\Http\Middleware\ErrorHandlerMiddleware;
use Gemriser\Http\Middleware\SessionMiddleware;
use Gemriser\Http\Middleware\ThrottleMiddleware;
use Gemriser\Http\Middleware\TrustProxiesMiddleware;
use Gemriser\Http\Request;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = new Kernel($app, [
    TrustProxiesMiddleware::class,
    ErrorHandlerMiddleware::class,
    SessionMiddleware::class,
    EncryptCookiesMiddleware::class,
    CsrfMiddleware::class,
    ThrottleMiddleware::class,
]);

$request = Request::capture();
$response = $kernel->handle($request->psr());

(new Nyholm\Psr7Server\ServerRequestCreator(
    new Nyholm\Psr7\Factory\Psr17Factory(),
    new Nyholm\Psr7\Factory\Psr17Factory(),
    new Nyholm\Psr7\Factory\Psr17Factory(),
    new Nyholm\Psr7\Factory\Psr17Factory()
))->emit($response);
