<?php

declare(strict_types=1);

namespace Gemriser\Auth;

use Gemriser\Application;
use Gemriser\Providers\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('auth', function (Application $app) {
            $model = $app->make('config')->get('auth.providers.users.model', 'App\Models\User');
            return new Guard($model);
        });
    }
}
