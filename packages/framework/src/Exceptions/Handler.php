<?php

declare(strict_types=1);

namespace Gemriser\Exceptions;

use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;

class Handler
{
    public function render(\Throwable $e): ResponseInterface
    {
        return match (true) {
            $e instanceof ValidationException => $e->render(),
            default => Response::html('<h1>500 Internal Server Error</h1>', 500),
        };
    }

    public function report(\Throwable $e): void
    {
        if (app()->bound('log')) {
            app('log')->error($e->getMessage(), [
                'exception' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }
}
