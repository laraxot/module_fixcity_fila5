<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use function Safe\preg_match;
use function Safe\preg_replace;

/**
 * Sidebar filter catalog aligned with disservizio-lista.json (Design Comuni reference).
 *
 * @see https://italia.github.io/design-comuni-pagine-statiche/sito/ticket-list.html
 */
final class LoadCityDesignFilterCatalogAction
{
    public const REFERENCE_RESULTS_TOTAL = 645;

    /** Reference count for H1 subtitle (12 months resolved). */
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

        /** @var array<int, mixed> $categories */
        $categories = is_array($payload['categories'] ?? null) ? $payload['categories'] : [];
        $firstRaw = $categories[0] ?? null;
        $firstCategory = is_array($firstRaw) ? $firstRaw : [];
        /** @var array<int, array<string, mixed>> $list */
        $list = is_array($firstCategory['list'] ?? null) ? $firstCategory['list'] : [];
        $legend = SafeStringCastAction::cast($firstCategory['title'] ?? 'categoria');

        $items = [];
        $sumCounts = 0;

        foreach ($list as $entry) {
            $rawLabel = SafeStringCastAction::cast($entry['label'] ?? '');
            $count = 0;
            $label = $rawLabel;

            if (preg_match('/\((\d+)\)\s*$/', $rawLabel, $matches) === 1 && isset($matches[1])) {
                $count = (int) $matches[1];
                $label = trim((string) preg_replace('/\s*\(\d+\)\s*$/', '', $rawLabel));
            }

            $sumCounts += $count;

            $items[] = [
                'id' => SafeStringCastAction::cast($entry['id'] ?? 'filter'),
                'value' => SafeStringCastAction::cast($entry['value'] ?? ''),
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
