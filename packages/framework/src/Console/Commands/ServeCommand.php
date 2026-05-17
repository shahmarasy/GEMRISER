<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    protected static $defaultName = 'serve';

    protected function configure(): void
    {
        $this->setDescription('Serve the application on PHP development server')
            ->addOption('port', 'p', mode: \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, default: '8000')
            ->addOption('host', mode: \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, default: '127.0.0.1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $publicDir = getcwd() . '/public';

        $output->writeln("<info>GEMRISER development server started:</info> http://{$host}:{$port}");
        $output->writeln("<comment>Press Ctrl+C to stop.</comment>");

        $command = sprintf('php -S %s:%s -t %s', $host, $port, escapeshellarg($publicDir));
        passthru($command);

        return Command::SUCCESS;
    }
}
