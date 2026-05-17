<?php

declare(strict_types=1);

namespace Gemriser\Routing;

use Closure;
use Gemriser\Application;
use Gemriser\Http\Request;
use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private RouteCollection $routes;
    private array $groupStack = [];
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->routes = new RouteCollection();
    }

    private function addRoute(array $methods, string $uri, mixed $action): Route
    {
        $route = new Route($methods, $uri, $action);

        if (!empty($this->groupStack)) {
            $group = end($this->groupStack);
            if (isset($group['prefix'])) {
                $route->prefix($group['prefix']);
            }
            if (isset($group['middleware'])) {
                $route->middleware($group['middleware']);
            }
        }

        $this->routes->add($route);
        return $route;
    }

    public function get(string $uri, mixed $action): Route
    {
        return $this->addRoute(['GET'], $uri, $action);
    }

    public function post(string $uri, mixed $action): Route
    {
        return $this->addRoute(['POST'], $uri, $action);
    }

    public function put(string $uri, mixed $action): Route
    {
        return $this->addRoute(['PUT'], $uri, $action);
    }

    public function patch(string $uri, mixed $action): Route
    {
        return $this->addRoute(['PATCH'], $uri, $action);
    }

    public function delete(string $uri, mixed $action): Route
    {
        return $this->addRoute(['DELETE'], $uri, $action);
    }

    public function match(array $methods, string $uri, mixed $action): Route
    {
        return $this->addRoute($methods, $uri, $action);
    }

    public function group(array $attributes, Closure $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $dispatcher = $this->routes->compile();
        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();

        $uri = rawurldecode($uri);
        $uri = '/' . ltrim($uri, '/');

        $result = $dispatcher->dispatch($method, $uri);

        return match ($result[0]) {
            Dispatcher::NOT_FOUND => Response::html('<h1>404 Not Found</h1>', 404),
            Dispatcher::METHOD_NOT_ALLOWED => Response::empty(405)
                ->withHeader('Allow', implode(', ', $result[1])),
            Dispatcher::FOUND => $this->resolveAction($result[1], $result[2]),
        };
    }

    private function resolveAction(Route $route, array $params): ResponseInterface
    {
        $action = $route->getAction();

        if ($action instanceof Closure) {
            $result = $this->app->call($action, $params);
        } elseif (is_array($action)) {
            [$class, $method] = $action;
            $instance = $this->app->make($class);
            $result = $this->app->call([$instance, $method], $params);
        } elseif (is_string($action) && str_contains($action, '@')) {
            [$class, $method] = explode('@', $action);
            $instance = $this->app->make($class);
            $result = $this->app->call([$instance, $method], $params);
        } else {
            $result = $action;
        }

        return $this->toResponse($result);
    }

    private function toResponse(mixed $result): ResponseInterface
    {
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return Response::html((string) $result);
    }

    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }
}
