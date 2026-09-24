<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use function Safe\file_put_contents;
use function Safe\json_encode;

use Spatie\QueueableAction\QueueableAction;

/**
 * GeoJSON di test per clustering (Roma) — solo dev/QA.
 */
class GenerateClusterTestJsonAction
{
    use QueueableAction;

    /** @return array<string, mixed> */
    public function execute(): array
    {
        $types = [
            ['type' => 'bug', 'label' => 'Bug', 'color' => '#e63946'],
            ['type' => 'feature', 'label' => 'Feature', 'color' => '#2a9d8f'],
            ['type' => 'improvement', 'label' => 'Miglioramento', 'color' => '#e9c46a'],
        ];

        $baseLat = 41.9028;
        $baseLng = 12.4964;

        $features = [];
        $id = 1;

        for ($i = 0; $i < 50; $i++) {
            $lat = $baseLat + (rand(-100, 100) / 10000);
            $lng = $baseLng + (rand(-100, 100) / 10000);
            $type = $types[$i % 3];

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $id++,
                    'title' => "Segnalazione {$id} - Test Cluster",
                    'type' => $type['type'],
                    'type_label' => $type['label'],
                    'type_color' => $type['color'],
                    'status' => 'pending',
                    'created_at' => now()->toIso8601String(),
                    'address' => "Via del Test {$id}, Roma",
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$lng, $lat],
                ],
            ];
        }

        for ($i = 0; $i < 20; $i++) {
            $lat = $baseLat + (rand(-200, 200) / 10000);
            $lng = $baseLng + (rand(-200, 200) / 10000);
            $type = $types[$i % 3];

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $id++,
                    'title' => "Segnalazione {$id} - Test Cluster 2",
                    'type' => $type['type'],
                    'type_label' => $type['label'],
                    'type_color' => $type['color'],
                    'status' => 'pending',
                    'created_at' => now()->toIso8601String(),
                    'address' => "Via del Test {$id}, Roma",
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$lng, $lat],
                ],
            ];
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];

        $path = public_path('data/cluster-test.json');
        file_put_contents($path, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $geojson;
    }
}
