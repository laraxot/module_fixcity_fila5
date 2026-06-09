<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Modules\Fixcity\Actions\BuildTicketPublicDetailsPayloadAction;
use Modules\Fixcity\Models\Ticket;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('api.ticket-details');

render(function (?Ticket $ticket): JsonResponse {
    if (! $ticket instanceof Ticket || ! $ticket->isVisibleOnPublicFrontoffice()) {
        abort(404);
    }

    return response()->json(
        app(BuildTicketPublicDetailsPayloadAction::class)->execute($ticket),
    );
});
