<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Carica il GeoJSON pubblico servito come /data/tickets.json (SSoT mappa + filtri).
 */
final class LoadPublicTicketsGeoJsonAction
{
    use QueueableAction;

    public const RELATIVE_PATH = '../public_html/data/tickets.json';

    public const PUBLIC_URL = '/data/tickets.json';

    /**
     * @return array{
     *     type?: string,
     *     generated_at?: string,
     *     total?: int,
     *     features: array<int, array<string, mixed>>
     * }
     */
    public function execute(): array
    {
        $path = base_path(self::RELATIVE_PATH);

        if (! File::isFile($path)) {
            return [
                'type' => 'FeatureCollection',
                'features' => [],
                'total' => 0,
            ];
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        if (! isset($payload['features']) || ! \is_array($payload['features'])) {
            $payload['features'] = [];
        }

        $payload['total'] = SafeIntCastAction::cast($payload['total'] ?? \count($payload['features']));

        /** @var array{type?: string, generated_at?: string, total?: int, features: array<int, array<string, mixed>>} $payload */
        return $payload;
    }
}
