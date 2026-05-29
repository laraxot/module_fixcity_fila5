<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

/**
 * Aggregati filtri elenco segnalazioni dalla stessa query pubblica della mappa (STORY-029).
 */
class BuildSegnalazioniFilterAggregateAction
{
    /**
     * @return array{
     *     features: array<int, array<string, mixed>>,
     *     countsPerType: array<string, int>,
     *     uniqueTypes: array<int, array<string, mixed>>,
     *     totalCount: int
     * }
     */
    public function execute(): array
    {
        $tickets = app(BuildPublicTicketsQueryAction::class)
            ->execute()
            ->latest()
            ->get();

        /** @var array<string, int> $counts */
        $counts = [];
        /** @var array<string, array<string, mixed>> $typesMap */
        $typesMap = [];
        /** @var array<int, array<string, mixed>> $features */
        $features = [];

        foreach ($tickets as $ticket) {
            $rawType = $ticket->getAttribute('type');
            $typeValue = $rawType instanceof \BackedEnum
                ? (string) $rawType->value
                : (is_string($rawType) ? $rawType : 'other');

            $typeProps = app(ResolveTicketTypeMarkerPropertiesAction::class)->executeFromValue($typeValue);

            $counts[$typeValue] = ($counts[$typeValue] ?? 0) + 1;
            if (! isset($typesMap[$typeValue])) {
                $typesMap[$typeValue] = [
                    'value' => $typeValue,
                    'label' => (string) ($typeProps['label'] ?? $typeValue),
                    'color' => (string) ($typeProps['color'] ?? '#607d8b'),
                    'icon' => (string) ($typeProps['icon'] ?? ''),
                ];
            }

            $location = $ticket->location;
            if (! \is_array($location)) {
                continue;
            }

            $lat = (float) ($location['lat'] ?? $location['latitude'] ?? $ticket->getAttribute('latitude') ?? 0);
            $lng = (float) ($location['lng'] ?? $location['longitude'] ?? $ticket->getAttribute('longitude') ?? 0);
            if ($lat === 0.0 && $lng === 0.0) {
                continue;
            }

            $features[] = [
                'properties' => [
                    'id' => $ticket->id,
                    'title' => $ticket->name,
                    'type' => $typeProps,
                    'address' => $location['address'] ?? $location['display_name'] ?? '',
                    'type_label' => (string) ($typeProps['label'] ?? $typeValue),
                ],
            ];
        }

        return [
            'features' => $features,
            'countsPerType' => $counts,
            'uniqueTypes' => array_values($typesMap),
            'totalCount' => $tickets->count(),
        ];
    }
}
