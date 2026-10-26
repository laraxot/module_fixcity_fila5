<?php

declare(strict_types=1);

// Texts that depend on the ente data (DCP-289): config('ente'), read by Modules\Tenant\Actions\Ente\GetEnteDataAction.
// The keys are the same in it, en, de, es.
return [
    'rights' => [
        'write' => 'Write to :address',
        'missing' => 'The Municipality has not published its contact details for exercising these rights yet. You will find them here as soon as they are available.',
    ],
];
