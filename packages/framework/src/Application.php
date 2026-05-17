<?php

declare(strict_types=1);

namespace Gemriser;

use Dotenv\Dotenv;
use Gemriser\Config\Loader;
use Gemriser\Config\Repository;
use Gemriser\Container\Container;
use Gemriser\Providers\ServiceProvider;

class Application extends Container
{
    private string $basePath;
    private ?Repository $config = null;
    private bool $booted = false;
    private array $serviceProviders = [];

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
        parent::__construct();
        $this->instance(self::class, $this);
        $this->instance('app', $this);
    }

    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function configPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function storagePath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'storage' . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function environment(): string
    {
        return $this->config?->get('app.env', 'production') ?? 'production';
    }

    public function isProduction(): bool
    {
        return $this->environment() === 'production';
    }

    public function loadEnvironment(): void
    {
        $envPath = $this->basePath;
        $envFile = $envPath . DIRECTORY_SEPARATOR . '.env';

        if (!file_exists($envFile)) {
            return;
        }

        $dotenv = Dotenv::createImmutable($envPath);
        $dotenv->load();
    }

    public function loadConfiguration(): void
    {
        $loader = new Loader();
        $items = $loader->loadFromDirectory($this->configPath());
        $this->config = new Repository($items);
        $this->instance('config', $this->config);
    }

    public function register(ServiceProvider $provider): void
    {
        $this->serviceProviders[] = $provider;
        $provider->register();
    }

    public function bootProviders(): void
    {
        foreach ($this->serviceProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
    }

    public function bootstrap(): void
    {
        $this->loadEnvironment();
        $this->loadConfiguration();
        $this->bootProviders();
        $this->booted = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }
}
