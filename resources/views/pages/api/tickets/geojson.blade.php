<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Modules\Fixcity\Actions\ApplyPublicTicketFiltersAction;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\BuildTicketsGeoJsonAction;
use Modules\Fixcity\Datas\PublicTicketListFilters;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('api.tickets.geojson');

// Stessi filtri dell'elenco pubblico (q, types[], statuses[], period): la mappa e l'elenco di /{lang}/tickets mostrano le stesse segnalazioni.
render(function (): JsonResponse {
    $query = app(ApplyPublicTicketFiltersAction::class)->execute(
        app(BuildPublicTicketsQueryAction::class)->execute(),
        PublicTicketListFilters::fromRequest(request()),
    );

    return response()->json(app(BuildTicketsGeoJsonAction::class)->execute($query));
});
