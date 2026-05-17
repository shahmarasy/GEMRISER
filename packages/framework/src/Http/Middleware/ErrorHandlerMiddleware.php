<?php

declare(strict_types=1);

namespace Gemriser\Http\Middleware;

use Gemriser\Application;
use Gemriser\Http\Request;
use Gemriser\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(private Application $app)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            if ($this->app->isProduction()) {
                $this->app->make('log')?->error($e->getMessage(), [
                    'exception' => $e,
                    'url' => (string) $request->getUri(),
                ]);
                return Response::html('<h1>500 Internal Server Error</h1>', 500);
            }

            $whoops = new Run();
            $whoops->pushHandler(new PrettyPageHandler());
            $responseBody = $whoops->handleException($e);

            return Response::html($responseBody, 500);
        }
    }
}
