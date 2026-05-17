<?php

declare(strict_types=1);

namespace Gemriser\Http\Middleware;

use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $handler->handle($request);
        }

        $token = $this->getTokenFromRequest($request);

        if (!$token || !hash_equals($this->getSessionToken(), $token)) {
            return Response::html('<h1>419 Page Expired</h1><p>CSRF token mismatch.</p>', 419);
        }

        return $handler->handle($request);
    }

    private function getTokenFromRequest(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('X-CSRF-TOKEN');
        if ($header) {
            return $header;
        }

        $body = $request->getParsedBody();
        return is_array($body) ? ($body['_token'] ?? null) : null;
    }

    private function getSessionToken(): string
    {
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }
}
