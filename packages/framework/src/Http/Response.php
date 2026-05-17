<?php

declare(strict_types=1);

namespace Gemriser\Http;

use Nyholm\Psr7\Response as Psr7Response;
use Nyholm\Psr7\Factory\Psr17Factory;

class Response
{
    public static function json(mixed $data, int $status = 200, array $headers = []): Psr7Response
    {
        $headers = array_merge(['Content-Type' => 'application/json'], $headers);
        return new Psr7Response($status, $headers, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public static function html(string $html, int $status = 200, array $headers = []): Psr7Response
    {
        $headers = array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers);
        return new Psr7Response($status, $headers, $html);
    }

    public static function redirect(string $url, int $status = 302): Psr7Response
    {
        return new Psr7Response($status, ['Location' => $url]);
    }

    public static function noContent(): Psr7Response
    {
        return new Psr7Response(204);
    }

    public static function empty(int $status = 200): Psr7Response
    {
        return new Psr7Response($status);
    }
}
