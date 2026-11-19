<?php

use Nwidart\Modules\Activators\FileActivator;

return [
    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => app(\Modules\Tenant\Actions\Config\GetTenantFilePathAction::class)->execute('modules_statuses.json'),
        ],
    ],
];
