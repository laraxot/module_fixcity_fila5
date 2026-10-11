<?php

use Orbit\Drivers\Json;
use Orbit\Drivers\Markdown;
use Orbit\Drivers\Yaml;

return [

    'default' => env('ORBIT_DEFAULT_DRIVER', 'md'),

    'drivers' => [
        'md' => Markdown::class,
        'json' => Json::class,
        'yaml' => Yaml::class,
    ],

    'paths' => [
        'content' => base_path('database/content'),
        // 'cache' => storage_path('framework/cache/orbit'),
        'cache' => app(\Modules\Tenant\Actions\Config\GetTenantFilePathAction::class)->execute(''),
    ],

];
