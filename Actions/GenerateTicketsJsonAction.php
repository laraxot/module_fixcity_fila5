<?php

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;

class GenerateTicketsJsonAction
{
    /**
     * Execute the action and generate the GeoJSON file.
     */
    public function execute(): void
    {
        $features = Ticket::query()
            ->whereNotNull('location')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->get()
            ->filter(function (Ticket $t): bool {
                $loc = $t->location;

                return is_array($loc)
                    && isset($loc['latitude'], $loc['longitude'])
                    && is_numeric($loc['latitude'])
                    && is_numeric($loc['longitude']);
            })
            ->map(function (Ticket $t): array {
                $typeValue = (string) $t->type;
                $typeLabel = $typeValue;
                $typeColor = '#e63946';

                try {
                    $typeEnum = \Modules\Fixcity\Enums\TicketTypeEnum::from($typeValue);
                    $typeLabel = $typeEnum->getLabel();
                    $typeColor = is_string($typeEnum->getColor()) ? $typeEnum->getColor() : '#e63946';
                } catch (\ValueError) {
                    // leave defaults
                }

                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $t->location['longitude'],
                            (float) $t->location['latitude'],
                        ],
                    ],
                    'properties' => [
                        'id' => $t->id,
                        'title' => $t->name ?? '',
                        'type' => $typeValue,
                        'type_label' => $typeLabel,
                        'type_color' => $typeColor,
                        'status' => (string) ($t->status ?? ''),
                        'created_at' => $t->created_at?->toIso8601String() ?? '',
                        'address' => $t->location['address'] ?? $t->location['street'] ?? '',
                    ],
                ];
            })
            ->values()
            ->toArray();

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];

        $encoded = json_encode($geojson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // JS bundle (window.geoMapDatasets) — used by geo-map-lit via module import
        $jsPath = base_path('../public_html/assets/geo/data/tickets-geojson.js');
        $jsDir = dirname($jsPath);
        if (! is_dir($jsDir)) {
            mkdir(directory: $jsDir, permissions: 0755, recursive: true);
        }
        $jsContent = "window.geoMapDatasets = window.geoMapDatasets || {}; window.geoMapDatasets.tickets = " . $encoded . ";";
        file_put_contents($jsPath, $jsContent);

        // Pure JSON — fetched directly by geo-map-lit via data-url="/data/tickets.json"
        $jsonPath = base_path('../public_html/data/tickets.json');
        $jsonDir = dirname($jsonPath);
        if (! is_dir($jsonDir)) {
            mkdir($jsonDir, 0755, true);
        }
        file_put_contents($jsonPath, $encoded);
    }
}