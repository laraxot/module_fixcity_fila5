<?php

declare(strict_types=1);

namespace Modules\Fixcity\ViewModels;

use Illuminate\Support\Facades\File;

/**
 * ViewModel per i filtri della pagina segnalazioni elenco.
 *
 * Legge i dati da /data/tickets.json (SSoT) invece di fare query al DB.
 * Questo garantisce coerenza tra filtri e mappa che leggono la stessa fonte.
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
        $this->loadFromJson();
    }

    /**
     * Carica dati dal JSON /data/tickets.json
     */
    private function loadFromJson(): void
    {
        $jsonPath = base_path('../public_html/data/tickets.json');

        if (! File::exists($jsonPath)) {
            return;
        }

        $content = File::get($jsonPath);
        $data = json_decode($content, true);

        if (! is_array($data) || ! isset($data['features']) || ! is_array($data['features'])) {
            return;
        }

        /** @var array<int, array<string, mixed>> $features */
        $features = $data['features'];
        $this->features = $features;
        $this->aggregateData();
    }

    /**
     * Aggrega dati per tipo dal GeoJSON
     */
    private function aggregateData(): void
    {
        /** @var array<string, int> $counts */
        $counts = [];
        /** @var array<string, array<string, mixed>> $typesMap */
        $typesMap = [];

        foreach ($this->features as $feature) {
            /** @var array<string, mixed> $properties */
            $properties = $feature['properties'] ?? [];

            // Supporta sia struttura annidata (new) che flat (legacy)
            $typeObj = $properties['type'] ?? null;

            if (is_array($typeObj)) {
                // Struttura annidata: type: {value, label, color, icon, iconUrl}
                $typeValue = (string) ($typeObj['value'] ?? 'other');
                $typeLabel = (string) ($typeObj['label'] ?? $typeValue);
                $typeColor = (string) ($typeObj['color'] ?? '#607d8b');
                $typeIcon = (string) ($typeObj['icon'] ?? '');
            } else {
                // Struttura flat legacy
                $typeValue = is_string($typeObj) ? $typeObj : 'other';
                $typeLabel = (string) ($properties['type_label'] ?? $typeValue);
                $typeColor = (string) ($properties['type_color'] ?? '#607d8b');
                $typeIcon = (string) ($properties['type_icon'] ?? '');
            }

            // Incrementa conteggio
            $counts[$typeValue] = ($counts[$typeValue] ?? 0) + 1;

            // Salva info tipo (solo prima occorrenza)
            if (! isset($typesMap[$typeValue])) {
                $typesMap[$typeValue] = [
                    'value' => $typeValue,
                    'label' => $typeLabel,
                    'color' => $typeColor,
                    'icon' => $typeIcon,
                ];
            }
        }

        $this->countsPerType = $counts;
        /** @var array<int, array<string, mixed>> $uniqueTypes */
        $uniqueTypes = array_values($typesMap);
        $this->uniqueTypes = $uniqueTypes;
        $this->totalCount = count($this->features);
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
}
