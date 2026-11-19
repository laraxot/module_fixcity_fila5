<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Modules\Fixcity\Actions\BuildPublicTicketsGeoJsonResponseAction;
use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('api.tickets.geojson');
// SEC-12: endpoint pubblico senza autenticazione, limite per IP.
middleware(['throttle:120,1']);

// Stessi filtri dell'elenco pubblico (q, types[], statuses[], period): la mappa e l'elenco di /{lang}/tickets mostrano le stesse segnalazioni.
// In piu' `limit` e `bbox` (PublicTicketGeoJsonOptions); senza parametri la risposta ha la stessa forma di prima (DB-12).
render(function (): JsonResponse {
    return response()->json(app(BuildPublicTicketsGeoJsonResponseAction::class)->execute(request()));
});
