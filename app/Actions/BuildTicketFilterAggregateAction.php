<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Aggregati filtri elenco ticket da tickets.json — SSoT unico per mappa + filtri.
 */
class BuildTicketFilterAggregateAction
{
    use QueueableAction;

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

            /** @var array<string, mixed> $typedProps */
            $typedProps = $props;
            $typeMeta = $this->resolveTypeMeta($typedProps);
            if ($typeMeta === null) {
                continue;
            }

            $typeValue = SafeStringCastAction::cast($typeMeta['value'] ?? '');
            if ($typeValue === '') {
                continue;
            }

            $counts[$typeValue] = ($counts[$typeValue] ?? 0) + 1;
            if (! isset($typesMap[$typeValue])) {
                $typesMap[$typeValue] = $typeMeta;
            }

            $this->accumulateStatusMeta($typedProps, $statusCounts, $statusesMap);
        }

        return [
            'features' => $features,
            'countsPerType' => $counts,
            'uniqueTypes' => array_values($typesMap),
            'countsPerStatus' => $statusCounts,
            'uniqueStatuses' => $this->sortStatusesByEnumOrder($statusesMap),
            'totalCount' => SafeIntCastAction::cast($geoJson['total'] ?? count($features)),
        ];
    }

    /**
     * @param  array<string, mixed>  $props
     * @param  array<string, int>  $statusCounts
     * @param  array<string, array<string, mixed>>  $statusesMap
     */
    private function accumulateStatusMeta(array $props, array &$statusCounts, array &$statusesMap): void
    {
        $statusMeta = $this->resolveStatusMeta($props);
        if ($statusMeta === null) {
            return;
        }

        $statusValue = SafeStringCastAction::cast($statusMeta['value'] ?? '');
        if ($statusValue === '') {
            return;
        }

        $statusCounts[$statusValue] = ($statusCounts[$statusValue] ?? 0) + 1;
        if (! isset($statusesMap[$statusValue])) {
            $statusesMap[$statusValue] = $statusMeta;
        }
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
            $value = SafeStringCastAction::cast($typeRaw['value'] ?? '');
            if ($value === '') {
                return null;
            }

            $iconUrl = SafeStringCastAction::cast($typeRaw['iconUrl'] ?? $typeRaw['icon_url'] ?? '');
            if ($iconUrl === '') {
                return null;
            }

            return [
                'value' => $value,
                'label' => SafeStringCastAction::cast($typeRaw['label'] ?? $value),
                'iconUrl' => $iconUrl,
            ];
        }

        if (is_string($typeRaw) && $typeRaw !== '') {
            return app(ResolveTicketTypeMarkerPropertiesAction::class)->execute($typeRaw);
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
            $value = SafeStringCastAction::cast($statusRaw['value'] ?? '');
            if ($value === '') {
                return null;
            }

            $color = SafeStringCastAction::cast($statusRaw['color'] ?? '');

            return [
                'value' => $value,
                'label' => SafeStringCastAction::cast($statusRaw['label'] ?? $value),
                'color' => $color !== '' ? $color : app(ResolveTicketStatusMarkerPropertiesAction::class)
                    ->execute($value)['color'],
            ];
        }

        if (is_string($statusRaw) && $statusRaw !== '') {
            return app(ResolveTicketStatusMarkerPropertiesAction::class)->execute($statusRaw);
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
            $posA = $order[SafeStringCastAction::cast($a['value'] ?? '')] ?? PHP_INT_MAX;
            $posB = $order[SafeStringCastAction::cast($b['value'] ?? '')] ?? PHP_INT_MAX;

            return $posA <=> $posB;
        });

        return $values;
    }
}
