<?php

declare(strict_types=1);

// Testi che dipendono dai dati dell'ente (DCP-289): config('ente'), letti da Modules\Tenant\Actions\Ente\GetEnteDataAction.
// Le chiavi sono identiche in it, en, de, es.
return [
    'rights' => [
        'write' => 'Scrivi a :address',
        'missing' => 'I recapiti del Comune per esercitare questi diritti non sono ancora stati pubblicati. Li troverai qui appena saranno disponibili.',
    ],
];
