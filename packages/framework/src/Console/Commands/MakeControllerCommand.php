<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeControllerCommand extends Command
{
    protected static $defaultName = 'make:controller';

    protected function configure(): void
    {
        $this->setDescription('Create a new controller class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $path = getcwd() . '/app/Http/Controllers/' . $name . '.php';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Gemriser\Http\Request;
use Gemriser\Http\Response;

class {$name}
{
    public function index(): \Psr\Http\Message\ResponseInterface
    {
        return Response::html('<h1>{$name} index</h1>');
    }
}

PHP;

        file_put_contents($path, $stub);
        $output->writeln("<info>Controller created:</info> {$path}");

        return Command::SUCCESS;
    }
}
