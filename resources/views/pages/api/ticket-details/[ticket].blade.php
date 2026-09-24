<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\BuildTicketPublicDetailsPayloadAction;
use Modules\Fixcity\Actions\LoadPublicTicketsGeoJsonAction;
use Modules\Fixcity\Models\Ticket;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('api.ticket-details');

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
