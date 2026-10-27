<?php

declare(strict_types=1);

// Texte, die von den Daten der Körperschaft abhängen (DCP-289): config('ente'), gelesen von Modules\Tenant\Actions\Ente\GetEnteDataAction.
// Die Schlüssel sind in it, en, de, es identisch.
return [
    'rights' => [
        'write' => 'Schreibe an :address',
        'missing' => 'Die Gemeinde hat ihre Kontaktdaten zur Ausübung dieser Rechte noch nicht veröffentlicht. Sobald sie verfügbar sind, findest du sie hier.',
    ],
];
