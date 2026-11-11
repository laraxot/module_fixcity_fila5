<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\BuildTicketPublicDetailsPayloadAction;
use Modules\Fixcity\Actions\LoadPublicTicketsGeoJsonAction;
use Modules\Fixcity\Models\Ticket;
use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('api.ticket-details');
// SEC-12: endpoint pubblico senza autenticazione, limite per IP.
middleware(['throttle:240,1']);

render(function (int|string $ticket): JsonResponse {
    $payloadAction = app(BuildTicketPublicDetailsPayloadAction::class);
    $record = app(BuildPublicTicketsQueryAction::class)
        ->execute()
        ->whereKey($ticket)
        ->first();

    if ($record instanceof Ticket) {
        return response()->json(
            $payloadAction->execute($record),
        );
    }

    // Un ticket reale non visibile (es. in attesa di moderazione) non deve ricadere sul GeoJSON demo con lo stesso id.
    // withTrashed: anche un ticket cancellato non deve ricadere sul GeoJSON demo (titolo e indirizzo resterebbero pubblici).
    abort_if(Ticket::withTrashed()->whereKey($ticket)->exists(), 404);

    $ticketId = (int) $ticket;
    $geoJson = app(LoadPublicTicketsGeoJsonAction::class)->execute();
    foreach ($geoJson['features'] as $feature) {
        $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
        if ((int) ($properties['id'] ?? 0) === $ticketId) {
            return response()->json(
                $payloadAction->execute($feature),
            );
        }
    }

    abort(404);
});