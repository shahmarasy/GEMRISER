<?php

declare(strict_types=1);

use App\Models\User;
use Gemriser\Http\Middleware\AuthenticateMiddleware;
use Gemriser\Http\Response;

$router->group(['prefix' => 'api', 'middleware' => 'throttle:60,1'], function ($router) {
    $router->get('/users', function () {
        return Response::json(User::all());
    })->middleware(AuthenticateMiddleware::class);

    $router->get('/ping', function () {
        return Response::json(['status' => 'ok']);
    });
});
