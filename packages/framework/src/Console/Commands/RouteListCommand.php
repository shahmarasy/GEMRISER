<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Gemriser\Application;
use Gemriser\Routing\Route;
use Gemriser\Routing\RouteServiceProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RouteListCommand extends Command
{
    protected static $defaultName = 'route:list';

    public function __construct(private Application $app)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Display all registered routes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $router = $this->app->make(\Gemriser\Routing\Router::class);
        $routes = $router->getRoutes();

        $rows = [];

        $ref = new \ReflectionClass($routes);
        $routesProp = $ref->getProperty('routes');
        $routesProp->setAccessible(true);
        $routeList = $routesProp->getValue($routes);

        foreach ($routeList as $route) {
            $rows[] = [
                implode('|', $route->getMethods()),
                $route->getUri(),
                $route->getName() ?? '',
                is_string($route->getAction()) ? $route->getAction() : 'Closure',
            ];
        }

        $table = new Table($output);
        $table->setHeaders(['Method', 'URI', 'Name', 'Action'])->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
