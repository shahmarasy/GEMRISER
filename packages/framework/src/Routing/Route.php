<?php

declare(strict_types=1);

namespace Gemriser\Routing;

class Route
{
    private array $methods;
    private string $uri;
    private mixed $action;
    private ?string $name = null;
    private array $middleware = [];
    private string $prefix = '';

    public function __construct(array $methods, string $uri, mixed $action)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function middleware(array|string $middleware): self
    {
        $this->middleware = array_merge($this->middleware, (array) $middleware);
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getUri(): string
    {
        return $this->prefix . $this->uri;
    }

    public function getAction(): mixed
    {
        return $this->action;
    }
}
