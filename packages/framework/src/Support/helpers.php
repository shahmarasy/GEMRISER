<?php

declare(strict_types=1);

use Gemriser\Application;

if (!function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        $app = Application::getInstance();

        if ($abstract === null) {
            return $app;
        }

        return $app->make($abstract);
    }
}

if (!function_exists('config')) {
    function config(string|array|null $key = null, mixed $default = null): mixed
    {
        $repository = app('config');

        if ($key === null) {
            return $repository;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $repository->set($k, $v);
            }
            return null;
        }

        return $repository->get($key, $default);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return app(Application::class)->basePath($path);
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return app(Application::class)->configPath($path);
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return app(Application::class)->storagePath($path);
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        return app(\Gemriser\Routing\UrlGenerator::class)->route($name, $params);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return app(\Gemriser\Routing\UrlGenerator::class)->url($path);
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = []): \Illuminate\View\View
    {
        return app('view')->make($template, $data);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . $method . '">';
    }
}

if (!function_exists('auth')) {
    function auth(): \Gemriser\Auth\Guard
    {
        return app('auth');
    }
}

if (!function_exists('bcrypt')) {
    function bcrypt(string $value): string
    {
        return app('hash')->make($value);
    }
}

if (!function_exists('logger')) {
    function logger(?string $message = null, array $context = []): \Psr\Log\LoggerInterface|null
    {
        $log = app('log');
        if ($message === null) {
            return $log;
        }
        $log->debug($message, $context);
        return null;
    }
}
