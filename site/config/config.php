<?php

return [
    'debug' => true,

    'panel' => [
        'install' => true,
    ],

    // Kirby Git plugin configuration
    // Automatically commits content changes to Git when using the panel
    'oblik.git' => [
        'commit' => [
            'enabled' => true,
            'message' => 'Content updated via Kirby Panel',
        ],
        'push' => [
            'enabled' => false, // Enable this on production server only
        ],
        'pull' => [
            'enabled' => false,
        ],
        'paths' => [
            'content' => true,
            'site/blueprints' => true,
            'site/templates' => true,
            'site/snippets' => true,
        ],
    ],
];
