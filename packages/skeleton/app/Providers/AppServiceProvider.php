<?php

declare(strict_types=1);

namespace App\Providers;

use Gemriser\Providers\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        app('view')->share('appName', config('app.name', 'Gemriser'));
    }
}
