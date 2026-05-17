<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Gemriser\Database\Migration\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateStatusCommand extends Command
{
    protected static $defaultName = 'migrate:status';

    protected function configure(): void
    {
        $this->setDescription('Show the status of each migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrator = new Migrator();
        $status = $migrator->status();

        $rows = [];
        foreach ($status as $item) {
            $rows[] = [
                $item['migration'],
                $item['ran'] ? '<info>Y</info>' : '<comment>N</comment>',
            ];
        }

        $table = new Table($output);
        $table->setHeaders(['Migration', 'Ran?'])->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
