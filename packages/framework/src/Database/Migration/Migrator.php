<?php

declare(strict_types=1);

namespace Gemriser\Database\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;

class Migrator
{
    private Builder $schema;

    public function __construct()
    {
        $this->schema = Capsule::getInstance()->schema();
    }

    public function run(string $path): array
    {
        if (!$this->schema->hasTable('migrations')) {
            $this->createMigrationTable();
        }

        $ran = $this->getRan();
        $files = $this->getMigrationFiles($path);
        $executed = [];

        foreach ($files as $file) {
            $migration = basename($file, '.php');
            if (in_array($migration, $ran, true)) {
                continue;
            }

            $instance = require $file;
            $instance->up();
            $this->logMigration($migration);
            $executed[] = $migration;
        }

        return $executed;
    }

    public function rollback(int $steps = 1): array
    {
        $ran = $this->getRan();
        $rolledBack = [];

        for ($i = 0; $i < min($steps, count($ran)); $i++) {
            $migration = array_pop($ran);
            $file = $this->findMigrationFile($migration);

            if ($file) {
                $instance = require $file;
                $instance->down();
                $this->deleteMigration($migration);
                $rolledBack[] = $migration;
            }
        }

        return $rolledBack;
    }

    public function status(): array
    {
        $ran = $this->getRan();
        $path = config('database.migrations_path', base_path('database/migrations'));
        $files = $this->getMigrationFiles($path);
        $status = [];

        foreach ($files as $file) {
            $migration = basename($file, '.php');
            $status[] = [
                'migration' => $migration,
                'ran' => in_array($migration, $ran, true),
            ];
        }

        return $status;
    }

    private function createMigrationTable(): void
    {
        $this->schema->create('migrations', function ($table) {
            $table->increments('id');
            $table->string('migration');
            $table->integer('batch');
        });
    }

    private function getRan(): array
    {
        if (!$this->schema->hasTable('migrations')) {
            return [];
        }

        return Capsule::getInstance()->table('migrations')->pluck('migration')->all();
    }

    private function logMigration(string $migration): void
    {
        $batch = Capsule::getInstance()->table('migrations')->max('batch') ?? 0;
        Capsule::getInstance()->table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch + 1,
        ]);
    }

    private function deleteMigration(string $migration): void
    {
        Capsule::getInstance()->table('migrations')->where('migration', $migration)->delete();
    }

    private function getMigrationFiles(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = glob($path . '/*.php');
        if ($files === false) {
            return [];
        }

        sort($files);
        return $files;
    }

    private function findMigrationFile(string $migration): ?string
    {
        $path = config('database.migrations_path', base_path('database/migrations'));
        $file = $path . '/' . $migration . '.php';
        return file_exists($file) ? $file : null;
    }
}
