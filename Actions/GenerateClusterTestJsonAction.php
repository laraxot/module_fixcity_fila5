<?php

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Storage;

/**
 * Generate a GeoJSON with MANY points in the SAME area (Rome center)
 * to properly test marker clustering.
 * All points are within ~2km radius to ensure clustering at zoom < 12.
 */
class GenerateClusterTestJsonAction
{
    public function __invoke(): array
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

        // Generate 50 points in Rome center (within ~1km radius)
        for ($i = 0; $i < 50; $i++) {
            // Random offset within ~1km (0.009 degrees ≈ 1km)
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

        // Add a few more points slightly further away (within ~2km)
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

        // Write to public_html/data/cluster-test.json
        $path = public_path('data/cluster-test.json');
        file_put_contents($path, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $geojson;
    }
}
