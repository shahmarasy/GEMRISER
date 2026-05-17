<?php

declare(strict_types=1);

namespace Gemriser\Config;

use RuntimeException;

class Loader
{
    public function loadFromDirectory(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $config = [];
        $files = glob($dir . '/*.php');

        if ($files === false) {
            throw new RuntimeException("Failed to read config directory: {$dir}");
        }

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $config[$key] = require $file;
        }

        return $config;
    }
}
