<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;

use function Safe\file_put_contents;
use function Safe\json_encode;
use function Safe\mkdir;

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
                $rawType = $t->type;
                $typeValue = $rawType instanceof \BackedEnum
                    ? (string) $rawType->value
                    : (is_string($rawType) ? $rawType : (is_int($rawType) ? (string) $rawType : ''));
                $typeLabel = $typeValue;
                $typeColor = '#e63946';

                if ($typeValue !== '') {
                    try {
                        $typeEnum = TicketTypeEnum::from($typeValue);
                        $typeLabel = $typeEnum->getLabel();
                        $typeColor = is_string($typeEnum->getColor()) ? $typeEnum->getColor() : '#e63946';
                    } catch (\ValueError) {
                        // leave defaults
                    }
                }

                $location = is_array($t->location) ? $t->location : [];

                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) ($location['longitude'] ?? 0),
                            (float) ($location['latitude'] ?? 0),
                        ],
                    ],
                    'properties' => [
                        'id' => $t->id,
                        'title' => $t->name ?? '',
                        'type' => $typeValue,
                        'type_label' => $typeLabel,
                        'type_color' => $typeColor,
                        'status' => is_scalar($t->status) ? (string) $t->status : '',
                        'created_at' => $t->created_at?->toIso8601String() ?? '',
                        'address' => is_string($location['address'] ?? null) ? $location['address'] : (is_string($location['street'] ?? null) ? $location['street'] : ''),
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
        $jsContent = 'window.geoMapDatasets = window.geoMapDatasets || {}; window.geoMapDatasets.tickets = '.$encoded.';';
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
