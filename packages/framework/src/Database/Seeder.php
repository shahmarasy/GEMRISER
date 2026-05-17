<?php

declare(strict_types=1);

namespace Gemriser\Database;

abstract class Seeder
{
    abstract public function run(): void;

    protected function call(string|array $seeders): void
    {
        foreach ((array) $seeders as $seeder) {
            $instance = new $seeder();
            $instance->run();
        }
    }
}
