<?php

declare(strict_types=1);

namespace Gemriser\Http\Middleware;

use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (app('auth')->check()) {
            return Response::redirect('/dashboard');
        }

        return $handler->handle($request);
    }
}
