<?php

declare(strict_types=1);

namespace Gemriser\Console;

use Gemriser\Application;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    public function __construct(private \Gemriser\Application $app)
    {
        parent::__construct('GEMRISER', '1.0.0');
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->add(new Commands\ServeCommand());
        $this->add(new Commands\KeyGenerateCommand());
        $this->add(new Commands\RouteListCommand($this->app));
        $this->add(new Commands\MakeControllerCommand());
        $this->add(new Commands\MakeModelCommand());
        $this->add(new Commands\MakeMiddlewareCommand());
        $this->add(new Commands\MigrateCommand());
        $this->add(new Commands\MigrateRollbackCommand());
        $this->add(new Commands\MigrateStatusCommand());
    }
}
