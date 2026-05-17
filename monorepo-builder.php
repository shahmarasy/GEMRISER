<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (Config $config): void {
    $config->packageDirectories([__DIR__ . '/packages']);

    $config->dataByKey(Option::DATA_BY_KEY, [
        'gemriser/framework' => [
            'name' => 'gemriser/framework',
            'description' => 'GEMRISER — modern micro PHP framework',
            'type' => 'library',
        ],
        'gemriser/skeleton' => [
            'name' => 'gemriser/skeleton',
            'description' => 'GEMRISER skeleton application',
            'type' => 'project',
        ],
    ]);
};
