<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Gemriser\Database\Migration\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure(): void
    {
        $this->setDescription('Run the database migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrator = new Migrator();
        $path = getcwd() . '/database/migrations';
        $executed = $migrator->run($path);

        if (empty($executed)) {
            $output->writeln('<comment>Nothing to migrate.</comment>');
        } else {
            foreach ($executed as $migration) {
                $output->writeln("<info>Migrated:</info> {$migration}");
            }
        }

        return Command::SUCCESS;
    }
}
