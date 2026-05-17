<?php

declare(strict_types=1);

namespace Gemriser\Http;

use Gemriser\Application;
use Gemriser\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

class Kernel implements RequestHandlerInterface
{
    private array $middleware = [];

    public function __construct(private Application $app, array $middleware = [])
    {
        foreach ($middleware as $m) {
            $this->pushMiddleware($m);
        }
    }

    public function pushMiddleware(MiddlewareInterface|string $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function prependMiddleware(MiddlewareInterface|string $middleware): void
    {
        array_unshift($this->middleware, $middleware);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queue = [];

        foreach ($this->middleware as $m) {
            $queue[] = is_string($m) ? $this->app->make($m) : $m;
        }

        $queue[] = function (ServerRequestInterface $req) {
            return $this->app->make(Router::class)->dispatch($req);
        };

        $relay = new Relay($queue);
        return $relay->handle($request);
    }
}
