<?php

declare(strict_types=1);

namespace Gemriser\Database;

use Gemriser\Application;
use Gemriser\Providers\ServiceProvider;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $capsule = new Capsule($this->app);

        $connections = $this->app->make('config')->get('database.connections', []);
        foreach ($connections as $name => $config) {
            $capsule->addConnection($config, $name);
        }

        $capsule->setEventDispatcher(new Dispatcher($this->app));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $this->app->instance('db', $capsule);
        $this->app->instance(Capsule::class, $capsule);
    }
}
