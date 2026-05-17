<?php

declare(strict_types=1);

$app = new Gemriser\Application(__DIR__ . '/..');

$app->bootstrap();

$app->register(new Gemriser\Database\DatabaseServiceProvider($app));
$app->register(new Gemriser\View\ViewServiceProvider($app));
$app->register(new Gemriser\Routing\RouteServiceProvider($app));
$app->register(new Gemriser\Hashing\HashServiceProvider($app));
$app->register(new Gemriser\Auth\AuthServiceProvider($app));
$app->register(new Gemriser\Validation\ValidationServiceProvider($app));
$app->register(new Gemriser\Logging\LoggingServiceProvider($app));
$app->register(new App\Providers\AppServiceProvider($app));

$app->boot();

return $app;
