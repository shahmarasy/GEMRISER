<?php

declare(strict_types=1);

namespace Gemriser\Hashing;

use Gemriser\Application;
use Gemriser\Providers\ServiceProvider;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Hashing\HashManager;

class HashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('hash', function (Application $app) {
            $manager = new HashManager($app);
            $manager->driver('bcrypt');
            return $manager;
        });

        $this->app->singleton(BcryptHasher::class);
    }
}
