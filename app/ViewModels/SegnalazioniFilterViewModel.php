<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels;

use Modules\Fixcity\Actions\BuildSegnalazioniFilterAggregateAction;

/**
 * ViewModel per i filtri della pagina segnalazioni elenco.
 *
 * Aggrega dalla stessa query pubblica usata da GET /api/tickets/geojson (STORY-029).
 */
class SegnalazioniFilterViewModel
{
    /** @var array<int, array<string, mixed>> */
    private array $features = [];

    /** @var array<string, int> */
    private array $countsPerType = [];

    /** @var array<int, array<string, mixed>> */
    private array $uniqueTypes = [];

    private int $totalCount = 0;

    public function __construct()
    {
        $aggregate = app(BuildSegnalazioniFilterAggregateAction::class)->execute();
        $this->features = $aggregate['features'];
        $this->countsPerType = $aggregate['countsPerType'];
        $this->uniqueTypes = $aggregate['uniqueTypes'];
        $this->totalCount = $aggregate['totalCount'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getFilterItems(): array
    {
        $items = [];

        foreach ($this->uniqueTypes as $type) {
            /** @var string $value */
            $value = (string) $type['value'];
            $items[] = [
                'id' => 'filter-' . $value,
                'value' => $value,
                'label' => (string) $type['label'],
                'color' => (string) $type['color'],
                'icon' => (string) $type['icon'],
                'count' => $this->countsPerType[$value] ?? 0,
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
            'title' => $title,
            'items' => $this->getFilterItems(),
            'total' => $this->totalCount,
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
     * Conta filtrata per tipi selezionati
     *
     * @param array<int, string> $selectedTypes
     */
    public function getFilteredCount(array $selectedTypes): int
    {
        if ($selectedTypes === []) {
            return $this->totalCount;
        }

        $count = 0;
        foreach ($selectedTypes as $type) {
            $typeKey = (string) $type;
            $count += $this->countsPerType[$typeKey] ?? 0;
        }

        return $count;
    }

    /**
     * Righe lista da GeoJSON quando il DB ha meno ticket del minimo reference (parity DOM).
     *
     * @param array<int, int|string> $excludeIds
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

        $exclude = array_map(static fn (int|string $id): string => (string) $id, $excludeIds);
        $items = [];

        foreach ($this->features as $feature) {
            if (count($items) >= $needed) {
                break;
            }

            /** @var array<string, mixed> $properties */
            $properties = $feature['properties'] ?? [];
            $id = $properties['id'] ?? null;
            $idKey = $id !== null ? (string) $id : '';

            if ($idKey !== '' && in_array($idKey, $exclude, true)) {
                continue;
            }

            $typeObj = $properties['type'] ?? null;
            if (is_array($typeObj)) {
                $typeLabel = (string) ($typeObj['label'] ?? $typeObj['value'] ?? '');
            } else {
                $typeLabel = (string) ($properties['type_label'] ?? (is_string($typeObj) ? $typeObj : ''));
            }

            $items[] = (object) [
                'id' => $id,
                'name' => (string) ($properties['title'] ?? 'Segnalazione'),
                'content' => null,
                'type_label' => $typeLabel,
                'location' => [
                    'address' => (string) ($properties['address'] ?? ''),
                ],
            ];
        }

        return $items;
    }
}
