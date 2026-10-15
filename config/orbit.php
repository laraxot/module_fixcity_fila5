<?php

use Modules\Tenant\Actions\Config\GetTenantFilePathAction;

return [

    'default' => (config('env.ORBIT_DEFAULT_DRIVER') ?? 'md'),

    // Orbit is not installed in this application; the configuration is kept for compatibility.
    'drivers' => [],

    'paths' => [
        'content' => base_path('database/content'),
        // 'cache' => storage_path('framework/cache/orbit'),
        'cache' => app(GetTenantFilePathAction::class)->execute(''),
    ],

];
