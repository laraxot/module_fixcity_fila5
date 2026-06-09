<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Fixcity\Actions\BuildTicketsGeoJsonAction;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('api.tickets.geojson');

render(function (): JsonResponse {
    /** @var array<int, string> $types */
    $types = request()->collect('types')
        ->filter(static fn ($type): bool => is_string($type) && $type !== '')
        ->values()
        ->all();

    $payload = app(BuildTicketsGeoJsonAction::class)->execute(
        app(BuildPublicTicketsQueryAction::class)->execute(),
        $types,
    );

    return response()->json($payload);
});
