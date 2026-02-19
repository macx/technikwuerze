<?php

return [
    'debug' => true,

    'panel' => [
        'install' => true,
    ],

    // Kirby Git Content plugin configuration
    // More actively maintained alternative with panel UI
    'thathoff.git-content' => [
        'commit' => [
            'enabled' => true,
        ],
        'push' => [
            'enabled' => false, // Enable on production server only
        ],
        'pull' => [
            'enabled' => false,
        ],
        'gitBinary' => 'git',
        'branch' => 'main',
    ],
];
