<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\HomeController;
use Gemriser\Http\Middleware\AuthenticateMiddleware;
use Gemriser\Http\Middleware\GuestMiddleware;

$router->get('/', [HomeController::class, 'index'])->name('home');

$router->group(['middleware' => GuestMiddleware::class], function ($router) {
    $router->get('/login', [LoginController::class, 'showForm'])->name('login');
    $router->post('/login', [LoginController::class, 'login']);
    $router->get('/register', [RegisterController::class, 'showForm'])->name('register');
    $router->post('/register', [RegisterController::class, 'register']);
});

$router->group(['middleware' => AuthenticateMiddleware::class], function ($router) {
    $router->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    $router->get('/examples', [ExampleController::class, 'index'])->name('examples.index');
    $router->post('/logout', [LoginController::class, 'logout'])->name('logout');
});
