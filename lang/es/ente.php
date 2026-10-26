<?php

declare(strict_types=1);

// Textos que dependen de los datos de la entidad (DCP-289): config('ente'), leídos por Modules\Tenant\Actions\Ente\GetEnteDataAction.
// Las claves son idénticas en it, en, de, es.
return [
    'rights' => [
        'write' => 'Escribe a :address',
        'missing' => 'El Ayuntamiento todavía no ha publicado sus datos de contacto para ejercer estos derechos. Los encontrarás aquí en cuanto estén disponibles.',
    ],
];
