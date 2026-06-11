<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

/**
 * Load GeoJSON tickets da public_html/data/tickets.json — SSoT unico per mappa + filtri.
 *
 * @deprecated Prefer {@see LoadPublicTicketsGeoJsonAction}; kept as alias for callers.
 */
final class LoadTicketsGeoJsonAction
{
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
        return app(LoadPublicTicketsGeoJsonAction::class)->execute();
    }

    /**
     * Get the public URL for the tickets GeoJSON file.
     */
    public function publicUrl(): string
    {
        return '/data/tickets.json';
    }
}
