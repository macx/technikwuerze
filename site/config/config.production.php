<?php

// Production configuration
return [
    'debug' => false,

    // Enable Git push on production server
    'oblik.git' => [
        'commit' => [
            'enabled' => true,
            'message' => 'Content updated via Kirby Panel',
        ],
        'push' => [
            'enabled' => true,
            'branch' => 'main',
        ],
        'pull' => [
            'enabled' => false,
        ],
    ],
];
