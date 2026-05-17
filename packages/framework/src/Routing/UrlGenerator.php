<?php

declare(strict_types=1);

namespace Gemriser\Routing;

class UrlGenerator
{
    public function __construct(private Router $router)
    {
    }

    public function route(string $name, array $params = []): string
    {
        $route = $this->router->getRoutes()->getByName($name);

        if ($route === null) {
            throw new \RuntimeException("Route [{$name}] not defined.");
        }

        $uri = $route->getUri();
        foreach ($params as $key => $value) {
            $uri = str_replace("{{$key}}", (string) $value, $uri);
        }

        return $uri;
    }

    public function url(string $path = ''): string
    {
        $baseUrl = config('app.url', 'http://localhost:8000');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
