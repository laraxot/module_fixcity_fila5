<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels;

use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * ViewModel per i filtri della pagina ticket elenco.
 *
 * Filtri sidebar: stesso file GeoJSON della mappa (/data/tickets.json).
 * NO dati statici: la sorgente unica e' public_html/data/tickets.json.
 * NO pattern Services: usa solo Spatie Queueable Actions (spatie/laravel-queueable-action).
 */
class TicketFilterViewModel
{
    private int $totalCount;

    /** @var array<int, array<string, mixed>> */
    private array $features = [];

    /** @var array<string, int> */
    private array $countsPerType = [];

    /** @var array<string, array<string, mixed>> */
    private array $typesMap = [];

    /** @var array<string, int> */
    private array $countsPerStatus = [];

    /** @var array<string, array<string, mixed>> */
    private array $statusesMap = [];

    public function __construct()
    {
        $aggregate = app(\Modules\Fixcity\Actions\BuildTicketFilterAggregateAction::class)->execute();
        $this->features = $aggregate['features'] ?? [];
        $this->countsPerType = $aggregate['countsPerType'] ?? [];
        $this->countsPerStatus = $aggregate['countsPerStatus'] ?? [];
        $this->typesMap = [];
        foreach ($aggregate['uniqueTypes'] ?? [] as $type) {
            $value = SafeStringCastAction::cast($type['value'] ?? '');
            if ($value !== '') {
                $this->typesMap[$value] = $type;
            }
        }
        $this->statusesMap = [];
        foreach ($aggregate['uniqueStatuses'] ?? [] as $status) {
            $value = SafeStringCastAction::cast($status['value'] ?? '');
            if ($value !== '') {
                $this->statusesMap[$value] = $status;
            }
        }
        $this->totalCount = $aggregate['totalCount'] ?? 0;
    }

    public function getCatalogLegend(): string
    {
        return (string) __('fixcity::ticket.filters.legend.label');
    }

    public function getStatusCatalogLegend(): string
    {
        return (string) __('fixcity::ticket.filters.status.legend.label');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getFilterItems(): array
    {
        $items = [];
        foreach ($this->typesMap as $type) {
            $value = SafeStringCastAction::cast($type['value'] ?? '');
            if ($value === '') {
                continue;
            }
            $count = $this->countsPerType[$value] ?? 0;
            $label = SafeStringCastAction::cast($type['label'] ?? $value);

            $iconUrl = SafeStringCastAction::cast($type['iconUrl'] ?? $type['icon_url'] ?? '');

            $items[] = [
                'id' => $value,
                'value' => $value,
                'label' => $label,
                'display_label' => $label,
                'count' => $count,
                'iconUrl' => $iconUrl,
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getStatusFilterItems(): array
    {
        $items = [];
        foreach ($this->statusesMap as $status) {
            $value = SafeStringCastAction::cast($status['value'] ?? '');
            if ($value === '') {
                continue;
            }
            $label = SafeStringCastAction::cast($status['label'] ?? $value);

            $items[] = [
                'id' => 'status-'.$value,
                'value' => $value,
                'label' => $label,
                'display_label' => $label,
                'count' => $this->countsPerStatus[$value] ?? 0,
                'color' => SafeStringCastAction::cast($status['color'] ?? '#607d8b'),
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFiltersData(string $title = ''): array
    {
        return [
            'title' => $title !== '' ? $title : $this->getCatalogLegend(),
            'items' => $this->getFilterItems(),
            'total' => $this->getTotalCount(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatusFiltersData(string $title = ''): array
    {
        return [
            'title' => $title !== '' ? $title : $this->getStatusCatalogLegend(),
            'items' => $this->getStatusFilterItems(),
            'total' => $this->getTotalCount(),
        ];
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return array<string, int>
     */
    public function getCountsPerType(): array
    {
        return $this->countsPerType;
    }

    /**
     * @return array<string, int>
     */
    public function getCountsPerStatus(): array
    {
        return $this->countsPerStatus;
    }

    /**
     * @param  array<int, string>  $selectedTypes
     * @param  array<int, string>  $selectedStatuses
     */
    public function getFilteredCount(array $selectedTypes, array $selectedStatuses = []): int
    {
        return $this->countFeaturesMatching($selectedTypes, $selectedStatuses);
    }

    /**
     * @param  array<int, string>  $selectedTypes
     * @param  array<int, string>  $selectedStatuses
     */
    private function countFeaturesMatching(array $selectedTypes, array $selectedStatuses): int
    {
        if ($this->features === []) {
            if ($selectedTypes === [] && $selectedStatuses === []) {
                return $this->totalCount;
            }

            $count = 0;
            if ($selectedTypes !== []) {
                foreach ($selectedTypes as $type) {
                    $count += $this->countsPerType[$type] ?? 0;
                }
            } elseif ($selectedStatuses !== []) {
                foreach ($selectedStatuses as $status) {
                    $count += $this->countsPerStatus[$status] ?? 0;
                }
            }

            return $count > 0 ? $count : $this->totalCount;
        }

        $typeSet = $selectedTypes === [] ? null : array_fill_keys($selectedTypes, true);
        $statusSet = $selectedStatuses === [] ? null : array_fill_keys($selectedStatuses, true);

        if ($typeSet === null && $statusSet === null) {
            return $this->totalCount;
        }

        $matched = 0;
        foreach ($this->features as $feature) {
            if (! is_array($feature)) {
                continue;
            }

            $props = $feature['properties'] ?? [];
            if (! is_array($props)) {
                continue;
            }

            if ($typeSet !== null) {
                /** @var array<string, mixed> $typedProps */
                $typedProps = $props;
                $typeValue = $this->extractTypeValue($typedProps);
                if ($typeValue === '' || ! isset($typeSet[$typeValue])) {
                    continue;
                }
            }

            if ($statusSet !== null) {
                /** @var array<string, mixed> $typedProps */
                $typedProps = $props;
                $statusValue = $this->extractStatusValue($typedProps);
                if ($statusValue === '' || ! isset($statusSet[$statusValue])) {
                    continue;
                }
            }

            $matched++;
        }

        return $matched;
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    private function extractTypeValue(array $properties): string
    {
        $typeRaw = $properties['type'] ?? null;
        if (is_array($typeRaw)) {
            return SafeStringCastAction::cast($typeRaw['value'] ?? '');
        }

        return is_string($typeRaw) ? $typeRaw : '';
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    private function extractStatusValue(array $properties): string
    {
        $statusRaw = $properties['status'] ?? null;
        if (is_array($statusRaw)) {
            return SafeStringCastAction::cast($statusRaw['value'] ?? '');
        }

        return is_string($statusRaw) ? $statusRaw : '';
    }

    /**
     * @param  array<int, int|string>  $excludeIds
     * @return array<int, object{
     *     id: int|string|null,
     *     name: string,
     *     type_label: string,
     *     location: array<string, mixed>
     * }>
     */
    public function getSupplementListItems(int $needed, array $excludeIds = []): array
    {
        if ($needed <= 0) {
            return [];
        }

        $exclude = array_map(static fn (int|string $id): string => SafeStringCastAction::cast($id), $excludeIds);
        $items = [];

        foreach ($this->features as $feature) {
            if (count($items) >= $needed) {
                break;
            }

            if (! is_array($feature)) {
                continue;
            }

            /** @var array<string, mixed> $properties */
            $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
            /** @var array<string, mixed> $geom */
            $geom = is_array($feature['geometry'] ?? null) ? $feature['geometry'] : [];
            $coords = $geom['coordinates'] ?? [];

            if (! is_array($coords) || count($coords) < 2) {
                continue;
            }

            $id = $properties['id'] ?? null;
            $idKey = is_int($id) || is_string($id) ? SafeStringCastAction::cast($id) : '';

            if ($idKey !== '' && in_array($idKey, $exclude, true)) {
                continue;
            }

            $typeObj = $properties['type'] ?? null;
            if (is_array($typeObj)) {
                $typeLabel = SafeStringCastAction::cast($typeObj['label'] ?? $typeObj['value'] ?? '');
            } else {
                $typeLabel = SafeStringCastAction::cast($properties['type_label'] ?? '');
            }

            $items[] = (object) [
                'id' => is_int($id) || is_string($id) ? $id : null,
                'name' => SafeStringCastAction::cast($properties['title'] ?? 'Ticket'),
                'type_label' => $typeLabel,
                'location' => [
                    'address' => SafeStringCastAction::cast($properties['address'] ?? ''),
                ],
            ];
        }

        return $items;
    }
}
