<?php

declare(strict_types=1);

namespace Gemriser\Http\Middleware;

use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ThrottleMiddleware implements MiddlewareInterface
{
    private array $attempts = [];
    private int $maxAttempts;
    private int $decaySeconds;

    public function __construct(int $maxAttempts = 60, int $decaySeconds = 60)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $key = $this->resolveRequestSignature($request);
        $now = time();

        $this->cleanAttempts($key, $now);

        $this->attempts[$key][] = $now;

        if (count($this->attempts[$key]) > $this->maxAttempts) {
            $retryAfter = $this->decaySeconds;
            return Response::empty(429)->withHeader('Retry-After', (string) $retryAfter);
        }

        return $handler->handle($request);
    }

    private function resolveRequestSignature(ServerRequestInterface $request): string
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';
        return $ip . '|' . $request->getMethod() . '|' . $request->getUri()->getPath();
    }

    private function cleanAttempts(string $key, int $now): void
    {
        if (!isset($this->attempts[$key])) {
            $this->attempts[$key] = [];
        }

        $this->attempts[$key] = array_filter(
            $this->attempts[$key],
            fn(int $time) => $time > ($now - $this->decaySeconds)
        );
    }
}
