<?php

declare(strict_types=1);

namespace Gemriser\Http\Middleware;

use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticateMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (app('auth')->guest()) {
            $accept = $request->getHeaderLine('Accept');
            if (str_contains($accept, 'application/json')) {
                return Response::json(['message' => 'Unauthenticated'], 401);
            }
            return Response::redirect('/login');
        }

        return $handler->handle($request);
    }
}
