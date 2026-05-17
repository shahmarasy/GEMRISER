<?php

declare(strict_types=1);

namespace Gemriser\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModelCommand extends Command
{
    protected static $defaultName = 'make:model';

    protected function configure(): void
    {
        $this->setDescription('Create a new Eloquent model class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the model');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $path = getcwd() . '/app/Models/' . $name . '.php';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Models;

use Gemriser\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    use Authenticatable;

    protected \$fillable = [];
}

PHP;

        file_put_contents($path, $stub);
        $output->writeln("<info>Model created:</info> {$path}");

        return Command::SUCCESS;
    }
}
