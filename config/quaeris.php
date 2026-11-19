<?php

declare(strict_types=1);

return [
    'limesurvey' => [
        'api' => [
            'url' => (config('env.LIMESURVEY_URL') ?? ''),
            'username' => (config('env.LIMESURVEY_USERNAME') ?? ''),
            'password' => (config('env.LIMESURVEY_PASSWORD') ?? ''),
        ],
    ],
];
