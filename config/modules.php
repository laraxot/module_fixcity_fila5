<?php

use Modules\Tenant\Actions\Config\GetTenantFilePathAction;
use Nwidart\Modules\Activators\FileActivator;

return [
    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => app(GetTenantFilePathAction::class)->execute('modules_statuses.json'),
        ],
    ],
];
