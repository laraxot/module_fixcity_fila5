@php
    use function Laravel\Folio\name;

    name('api.tickets.geojson');

    $types = request()->collect('types')
        ->filter(fn ($type): bool => is_string($type) && $type !== '')
        ->values()
        ->all();

    $payload = app(\Modules\Fixcity\Actions\BuildTicketsGeoJsonAction::class)
        ->execute(
            app(\Modules\Fixcity\Actions\BuildPublicTicketsQueryAction::class)->execute(),
            $types
        );

    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
@endphp
