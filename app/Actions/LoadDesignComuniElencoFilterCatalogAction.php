<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;

/**
 * Catalogo filtri sidebar allineato a disservizio-lista.json (Design Comuni reference).
 *
 * @see https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html
 */
final class LoadDesignComuniElencoFilterCatalogAction
{
    public const REFERENCE_RESULTS_TOTAL = 645;

    /** Conteggio reference sottotitolo H1 (12 mesi risolte). */
    public const REFERENCE_RESOLVED_LAST_12_MONTHS = 73;

    /**
     * @return array{
     *     legend: string,
     *     items: array<int, array<string, mixed>>,
     *     totalCount: int
     * }
     */
    public function execute(): array
    {
        $path = module_path('Fixcity', 'resources/json/disservizio-lista.json');
        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        /** @var array<int, array<string, mixed>> $list */
        $list = $payload['categories'][0]['list'] ?? [];
        $legend = (string) ($payload['categories'][0]['title'] ?? 'categoria');

        $items = [];
        $sumCounts = 0;

        foreach ($list as $entry) {
            $rawLabel = (string) ($entry['label'] ?? '');
            $count = 0;
            $label = $rawLabel;

            if (preg_match('/\((\d+)\)\s*$/', $rawLabel, $matches) === 1) {
                $count = (int) $matches[1];
                $label = trim((string) preg_replace('/\s*\(\d+\)\s*$/', '', $rawLabel));
            }

            $sumCounts += $count;

            $items[] = [
                'id' => (string) ($entry['id'] ?? 'filter'),
                'value' => (string) ($entry['value'] ?? ''),
                'label' => $label,
                'display_label' => $rawLabel,
                'count' => $count,
                'color' => '#007a53',
                'icon' => 'it-tag',
            ];
        }

        return [
            'legend' => $legend,
            'items' => $items,
            'totalCount' => self::REFERENCE_RESULTS_TOTAL,
        ];
    }
}
