<?php

declare(strict_types=1);

namespace Gemriser\Routing;

use Gemriser\Application;
use Gemriser\Providers\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Router::class, function (Application $app) {
            return new Router($app);
        });

        $this->app->singleton(UrlGenerator::class, function (Application $app) {
            return new UrlGenerator($app->make(Router::class));
        });
    }

    public function boot(): void
    {
        $router = $this->app->make(Router::class);

        $routesPath = $this->app->basePath('routes/web.php');
        if (file_exists($routesPath)) {
            require $routesPath;
        }

        $apiRoutesPath = $this->app->basePath('routes/api.php');
        if (file_exists($apiRoutesPath)) {
            require $apiRoutesPath;
        }
    }
}
