<?php

declare(strict_types=1);

return [
    'name' => 'Ticket',
    'namespace' => 'Modules\\Fixcity\\Ticket',
    'routes' => [
        'prefix' => 'ticket',
        'middleware' => ['web', 'auth'],
    ],
    'notifications' => [
        'enabled' => true,
        'channels' => ['mail', 'database'],
    ],
    'events' => [
        'enabled' => true,
    ],
];
