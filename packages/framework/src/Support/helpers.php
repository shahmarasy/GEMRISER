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
