<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Modules\Fixcity\Actions\BuildPublicNeighborhoodStatsAction;
use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

// @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
name('api.tickets.neighborhood-stats');
middleware(['throttle:120,1']);

render(function (): JsonResponse {
    $days = request()->query('days', 30);

    return response()->json(app(BuildPublicNeighborhoodStatsAction::class)->execute(
        is_scalar($days) ? (int) $days : 30,
    ));
});
