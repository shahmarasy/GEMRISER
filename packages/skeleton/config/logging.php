<?php

return [
    'default' => env('LOG_CHANNEL', 'daily'),
    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/gemriser.log'),
            'level' => 'debug',
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/gemriser.log'),
            'level' => 'debug',
            'days' => 14,
        ],
        'stderr' => [
            'driver' => 'stderr',
            'level' => 'debug',
        ],
    ],
];
