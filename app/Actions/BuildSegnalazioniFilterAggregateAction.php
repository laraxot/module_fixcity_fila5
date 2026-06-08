<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketStatusEnum;

/**
 * Aggregati filtri elenco segnalazioni da tickets.json — SSoT unico per mappa + filtri.
 */
class BuildSegnalazioniFilterAggregateAction
{
    /**
     * @return array{
     *     features: array<int, array<string, mixed>>,
     *     countsPerType: array<string, int>,
     *     uniqueTypes: array<int, array<string, mixed>>,
     *     countsPerStatus: array<string, int>,
     *     uniqueStatuses: array<int, array<string, mixed>>,
     *     totalCount: int
     * }
     */
    public function execute(): array
    {
        $geoJson = app(LoadPublicTicketsGeoJsonAction::class)->execute();
        $features = $geoJson['features'] ?? [];

        /** @var array<string, int> $counts */
        $counts = [];
        /** @var array<string, array<string, mixed>> $typesMap */
        $typesMap = [];
        /** @var array<string, int> $statusCounts */
        $statusCounts = [];
        /** @var array<string, array<string, mixed>> $statusesMap */
        $statusesMap = [];

        foreach ($features as $feature) {
            if (! is_array($feature)) {
                continue;
            }

            $props = $feature['properties'] ?? [];
            if (! is_array($props)) {
                continue;
            }

            $typeMeta = $this->resolveTypeMeta($props);
            if ($typeMeta === null) {
                continue;
            }

            $typeValue = (string) ($typeMeta['value'] ?? '');
            if ($typeValue === '') {
                continue;
            }

            $counts[$typeValue] = ($counts[$typeValue] ?? 0) + 1;
            if (! isset($typesMap[$typeValue])) {
                $typesMap[$typeValue] = $typeMeta;
            }

            $statusMeta = $this->resolveStatusMeta($props);
            if ($statusMeta !== null) {
                $statusValue = (string) ($statusMeta['value'] ?? '');
                if ($statusValue !== '') {
                    $statusCounts[$statusValue] = ($statusCounts[$statusValue] ?? 0) + 1;
                    if (! isset($statusesMap[$statusValue])) {
                        $statusesMap[$statusValue] = $statusMeta;
                    }
                }
            }
        }

        return [
            'features' => $features,
            'countsPerType' => $counts,
            'uniqueTypes' => array_values($typesMap),
            'countsPerStatus' => $statusCounts,
            'uniqueStatuses' => $this->sortStatusesByEnumOrder($statusesMap),
            'totalCount' => (int) ($geoJson['total'] ?? count($features)),
        ];
    }

    /**
     * Contratto GeoJSON properties.type (oggetto annidato o flat legacy).
     *
     * @param  array<string, mixed>  $properties
     * Solo tipologia (TicketTypeEnum): icona per legenda filtri — niente colore (riservato allo status).
     *
     * @return array{value: string, label: string, iconUrl: string}|null
     */
    private function resolveTypeMeta(array $properties): ?array
    {
        $typeRaw = $properties['type'] ?? null;

        if (is_array($typeRaw)) {
            $value = (string) ($typeRaw['value'] ?? '');
            if ($value === '') {
                return null;
            }

            $iconUrl = (string) ($typeRaw['iconUrl'] ?? $typeRaw['icon_url'] ?? '');
            if ($iconUrl === '') {
                return null;
            }

            return [
                'value' => $value,
                'label' => (string) ($typeRaw['label'] ?? $value),
                'iconUrl' => $iconUrl,
            ];
        }

        if (is_string($typeRaw) && $typeRaw !== '') {
            return app(ResolveTicketTypeMarkerPropertiesAction::class)->executeFromValue($typeRaw);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array{value: string, label: string, color: string}|null
     */
    private function resolveStatusMeta(array $properties): ?array
    {
        $statusRaw = $properties['status'] ?? null;

        if (is_array($statusRaw)) {
            $value = (string) ($statusRaw['value'] ?? '');
            if ($value === '') {
                return null;
            }

            $color = (string) ($statusRaw['color'] ?? '');

            return [
                'value' => $value,
                'label' => (string) ($statusRaw['label'] ?? $value),
                'color' => $color !== '' ? $color : app(ResolveTicketStatusMarkerPropertiesAction::class)
                    ->executeFromValue($value)['color'],
            ];
        }

        if (is_string($statusRaw) && $statusRaw !== '') {
            return app(ResolveTicketStatusMarkerPropertiesAction::class)->executeFromValue($statusRaw);
        }

        return null;
    }

    /**
     * @param  array<string, array<string, mixed>>  $statusesMap
     * @return array<int, array<string, mixed>>
     */
    private function sortStatusesByEnumOrder(array $statusesMap): array
    {
        $order = [];
        foreach (TicketStatusEnum::cases() as $index => $case) {
            $order[$case->value] = $index;
        }

        $values = array_values($statusesMap);
        usort($values, static function (array $a, array $b) use ($order): int {
            $posA = $order[(string) ($a['value'] ?? '')] ?? PHP_INT_MAX;
            $posB = $order[(string) ($b['value'] ?? '')] ?? PHP_INT_MAX;

            return $posA <=> $posB;
        });

        return $values;
    }
}
