<?php

declare(strict_types=1);

namespace Gemriser\Http;

use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7Server\ServerRequestCreator;

class Request
{
    private static ?self $current = null;

    private function __construct(private ServerRequest $psrRequest)
    {
    }

    public static function capture(): self
    {
        $creator = new ServerRequestCreator(
            \Nyholm\Psr7\Factory\Psr17Factory::class,
            \Nyholm\Psr7\Factory\Psr17Factory::class,
            \Nyholm\Psr7\Factory\Psr17Factory::class,
            \Nyholm\Psr7\Factory\Psr17Factory::class
        );

        self::$current = new self($creator->fromGlobals());
        return self::$current;
    }

    public static function current(): ?self
    {
        return self::$current;
    }

    public function psr(): ServerRequest
    {
        return $this->psrRequest;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $data = array_merge(
            $this->psrRequest->getQueryParams(),
            (array) $this->psrRequest->getParsedBody()
        );
        return $data[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge(
            $this->psrRequest->getQueryParams(),
            (array) $this->psrRequest->getParsedBody()
        );
    }

    public function method(): string
    {
        return $this->psrRequest->getMethod();
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($this->method()) === strtoupper($method);
    }

    public function uri(): string
    {
        return $this->psrRequest->getUri()->getPath();
    }

    public function header(string $key, ?string $default = null): ?string
    {
        return $this->psrRequest->getHeaderLine($key) ?: $default;
    }

    public function wantsJson(): bool
    {
        $accept = $this->header('Accept', 'text/html');
        return str_contains($accept, 'application/json');
    }

    public function ip(): string
    {
        return $this->psrRequest->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function serverParam(string $key, mixed $default = null): mixed
    {
        return $this->psrRequest->getServerParams()[$key] ?? $default;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->psrRequest->getCookieParams()[$key] ?? $default;
    }

    public function validate(array $rules, array $messages = []): array
    {
        $validator = app('validator')->make($this->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new \Gemriser\Exceptions\ValidationException(
                $validator->errors()->toArray()
            );
        }

        return $validator->validated();
    }
}
