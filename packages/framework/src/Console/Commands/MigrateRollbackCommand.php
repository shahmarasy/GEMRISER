<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Gemriser\Database\Migration\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRollbackCommand extends Command
{
    protected static $defaultName = 'migrate:rollback';

    protected function configure(): void
    {
        $this->setDescription('Rollback the last database migration')
            ->addArgument('steps', InputArgument::OPTIONAL, 'Number of steps to roll back', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrator = new Migrator();
        $steps = (int) $input->getArgument('steps');
        $rolledBack = $migrator->rollback($steps);

        if (empty($rolledBack)) {
            $output->writeln('<comment>Nothing to rollback.</comment>');
        } else {
            foreach ($rolledBack as $migration) {
                $output->writeln("<info>Rolled back:</info> {$migration}");
            }
        }

        return Command::SUCCESS;
    }
}
