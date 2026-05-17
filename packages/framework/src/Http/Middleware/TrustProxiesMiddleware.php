<?php

declare(strict_types=1);

namespace Gemriser\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrustProxiesMiddleware implements MiddlewareInterface
{
    private array $proxies = [];
    private array $headers = [];

    public function __construct(array $proxies = ['*'], array $headers = [])
    {
        $this->proxies = $proxies;
        $this->headers = $headers ?: [
            'X-Forwarded-For',
            'X-Forwarded-Host',
            'X-Forwarded-Port',
            'X-Forwarded-Proto',
        ];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
