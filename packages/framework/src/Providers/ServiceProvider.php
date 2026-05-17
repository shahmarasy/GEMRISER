<?php

declare(strict_types=1);

namespace Gemriser\Providers;

use Gemriser\Application;

abstract class ServiceProvider
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function register(): void;

    public function boot(): void
    {
    }
}
