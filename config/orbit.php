<?php

return [

    'default' => (config('env.ORBIT_DEFAULT_DRIVER') ?? 'md'),

    'drivers' => [
        'md' => 'Orbit\\Drivers\\Markdown',
        'json' => 'Orbit\\Drivers\\Json',
        'yaml' => 'Orbit\\Drivers\\Yaml',
    ],

    'paths' => [
        'content' => base_path('database/content'),
        // 'cache' => storage_path('framework/cache/orbit'),
        'cache' => app(\Modules\Tenant\Actions\Config\GetTenantFilePathAction::class)->execute(''),
    ],

];
