<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

use function Safe\json_encode;

/**
 * GeoJSON FeatureCollection per mappa pubblica (segnalazioni-elenco).
 *
 * Output: public_html/data/tickets.json (servito come /data/tickets.json)
 * Path modulo nwidart: solo `app/Actions/` — PSR-4 `Modules\Fixcity\` → `app/`.
 * Ogni feature espone `properties.type` come oggetto: value, label, color, icon, iconUrl.
 */
class GenerateTicketsJsonAction
{
    use QueueableAction;

    public function execute(): string
    {
        $outputPath = base_path('../public_html/data/tickets.json');

        $features = Ticket::query()
            ->whereNotNull('location')
            ->latest()
            ->get()
            ->map(function (Ticket $ticket): ?array {
                $location = $ticket->location;

                if (! \is_array($location)) {
                    return null;
                }

                $lat = (float) ($location['lat'] ?? $location['latitude'] ?? 0);
                $lng = (float) ($location['lng'] ?? $location['longitude'] ?? 0);

                if ($lat === 0.0 || $lng === 0.0) {
                    return null;
                }

                $rawType = $ticket->getAttribute('type');
                $typeValue = $rawType instanceof \BackedEnum
                    ? (string) $rawType->value
                    : (is_string($rawType) ? $rawType : 'other');

                $typeProps = app(ResolveTicketTypeMarkerPropertiesAction::class)->executeFromValue($typeValue);

                $currentStatus = $ticket->currentStatus();
                $statusValue = is_object($currentStatus) && isset($currentStatus->name) && is_string($currentStatus->name)
                    ? $currentStatus->name
                    : '';
                if ($statusValue === '') {
                    $statusValue = (string) $ticket->getRawOriginal('status');
                }
                if ($statusValue === '') {
                    $statusValue = 'pending';
                }

                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$lng, $lat],
                    ],
                    'properties' => [
                        'id' => $ticket->id,
                        'title' => $ticket->name,
                        'type' => $typeProps,
                        'address' => $location['address'] ?? $location['display_name'] ?? '',
                        'city' => $location['city'] ?? '',
                        'status' => $statusValue,
                        'url' => '/it/tests/segnalazione-dettaglio?id='.$ticket->id,
                    ],
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($features === [] && File::exists($outputPath)) {
            return $outputPath;
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'generated_at' => now()->toISOString(),
            'total' => \count($features),
            'features' => $features,
        ];

        $dir = \dirname($outputPath);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($outputPath, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $outputPath;
    }
}
