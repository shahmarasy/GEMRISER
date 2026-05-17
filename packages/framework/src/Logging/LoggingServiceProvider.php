<?php

declare(strict_types=1);

namespace Gemriser\Logging;

use Gemriser\Application;
use Gemriser\Providers\ServiceProvider;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('log', function (Application $app) {
            $channel = $app->make('config')->get('logging.default', 'daily');
            $channels = $app->make('config')->get('logging.channels', []);
            $config = $channels[$channel] ?? [];

            $logger = new Logger('gemriser');

            match ($config['driver'] ?? 'daily') {
                'single' => $logger->pushHandler(
                    new StreamHandler($config['path'] ?? $app->storagePath('logs/gemriser.log'), $config['level'] ?? Logger::DEBUG)
                ),
                'daily' => $logger->pushHandler(
                    new RotatingFileHandler($config['path'] ?? $app->storagePath('logs/gemriser.log'), $config['days'] ?? 14, $config['level'] ?? Logger::DEBUG)
                ),
                'stderr' => $logger->pushHandler(
                    new StreamHandler('php://stderr', $config['level'] ?? Logger::DEBUG)
                ),
                default => $logger->pushHandler(
                    new StreamHandler($app->storagePath('logs/gemriser.log'), Logger::DEBUG)
                ),
            };

            return $logger;
        });
    }
}
