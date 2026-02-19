<?php

// Production configuration
return [
    'debug' => false,

    // Enable Git push on production server
    'thathoff.git-content' => [
        'commit' => [
            'enabled' => true,
        ],
        'push' => [
            'enabled' => true,
        ],
        'pull' => [
            'enabled' => false,
        ],
        'branch' => 'main',
    ],
];
