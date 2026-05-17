<?php

declare(strict_types=1);

namespace Gemriser\Routing;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

class RouteCollection
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function add(Route $route): void
    {
        $this->routes[] = $route;
        if ($route->getName() !== null) {
            $this->namedRoutes[$route->getName()] = $route;
        }
    }

    public function getByName(string $name): ?Route
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function compile(): Dispatcher
    {
        $collector = new RouteCollector(new Std(), new GroupCountBased());

        foreach ($this->routes as $route) {
            $collector->addRoute($route->getMethods(), $route->getUri(), $route);
        }

        return new Dispatcher\GroupCountBased($collector->getData());
    }
}
