<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use function Safe\json_encode;
use Spatie\QueueableAction\QueueableAction;

/**
 * Generates a GeoJSON-like JSON file of all tickets with location data.
 * Output: public_html/data/tickets.json
 * Pattern: same as farmshops.eu — static file fetched by the Lit map component.
 */
class GenerateTicketsJsonAction
{
    use QueueableAction;

    /**
     * Generate and write tickets.json to public/data/.
     */
    public function execute(): string
    {
        $outputPath = base_path('../public_html/data/tickets.json');

        $tickets = Ticket::query()
            ->whereNotNull('location')
            ->latest()
            ->get();

        $features = $tickets->map(static function (Ticket $ticket): ?array {
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
            $typeValue = $rawType instanceof \BackedEnum ? $rawType->value : (is_scalar($rawType) ? (string) $rawType : 'other');
            $typeEnum = null;

            try {
                $typeEnum = TicketTypeEnum::from($typeValue);
            } catch (\ValueError) {
                // unknown type, leave null
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
                    'type' => $typeValue,
                    'type_label' => $typeEnum !== null ? $typeEnum->getLabel() : $typeValue,
                    'type_color' => $typeEnum !== null ? (string) $typeEnum->getColor() : '#607d8b',
                    'address' => $location['address'] ?? $location['display_name'] ?? '',
                    'city' => $location['city'] ?? '',
                    'status' => $ticket->status instanceof \BackedEnum
                        ? (string) $ticket->status->value
                        : (is_scalar($ticket->status) ? (string) $ticket->status : 'pending'),
                    'url' => '/it/tests/segnalazione-dettaglio/'.$ticket->id,
                ],
            ];
        })->filter()->values()->all();

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
