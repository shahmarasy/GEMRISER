# GEMRISER Framework

Modern micro PHP framework — spiritual successor to Lumen.

## Installation

```bash
composer require gemriser/framework
```

## Usage

```php
<?php

require 'vendor/autoload.php';

$app = new Gemriser\Application(__DIR__);
$app->bootstrap();

$router = $app->make(Gemriser\Routing\Router::class);
$router->get('/', function () {
    return 'Hello, World!';
});

$kernel = new Gemriser\Http\Kernel($app, [
    Gemriser\Http\Middleware\ErrorHandlerMiddleware::class,
]);

$request = Gemriser\Http\Request::capture();
$response = $kernel->handle($request->psr());
```

For a full application, use `gemriser/skeleton`.
