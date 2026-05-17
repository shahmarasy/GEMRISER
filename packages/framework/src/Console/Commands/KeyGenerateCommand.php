<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KeyGenerateCommand extends Command
{
    protected static $defaultName = 'key:generate';

    protected function configure(): void
    {
        $this->setDescription('Generate a random application key');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = 'base64:' . base64_encode(random_bytes(32));
        $envPath = getcwd() . '/.env';

        if (!file_exists($envPath)) {
            $output->writeln('<error>.env file not found.</error>');
            return Command::FAILURE;
        }

        $content = file_get_contents($envPath);
        if (preg_match('/^APP_KEY=.*$/m', $content)) {
            $content = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$key}", $content);
        } else {
            $content .= "\nAPP_KEY={$key}\n";
        }

        file_put_contents($envPath, $content);
        $output->writeln('<info>Application key generated successfully:</info> ' . $key);

        return Command::SUCCESS;
    }
}
