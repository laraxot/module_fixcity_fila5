<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Spatie\QueueableAction\QueueableAction;

use function Safe\json_encode;

/**
 * GeoJSON FeatureCollection per export statico (backoffice) — allineato a API live.
 *
 * Output: public_html/data/tickets.json (servito come /data/tickets.json)
 */
class GenerateTicketsJsonAction
{
    use QueueableAction;

    public function execute(): string
    {
        $outputPath = base_path(LoadPublicTicketsGeoJsonAction::RELATIVE_PATH);

        $geojson = app(BuildTicketsGeoJsonAction::class)->execute(
            app(BuildPublicTicketsQueryAction::class)->execute(),
        );

        $dir = \dirname($outputPath);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($outputPath, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $outputPath;
    }
}
