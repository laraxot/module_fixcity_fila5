<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketStatusEnum;
use Spatie\QueueableAction\QueueableAction;

/**
 * Oggetto {@code properties.status} per GeoJSON mappa — colore pin = stato workflow.
 *
 * @phpstan-type TicketStatusGeoJson array{value: string, label: string, color: string}
 */
final class ResolveTicketStatusMarkerPropertiesAction
{
    use QueueableAction;

    /**
     * @return TicketStatusGeoJson
     */
    public function execute(TicketStatusEnum|string $statusEnum): array
    {
        if (is_string($statusEnum)) {
            return $this->resolveFromValue($statusEnum);
        }

        return [
            'value' => $statusEnum->value,
            'label' => $statusEnum->getLabel(),
            'color' => $this->resolveMapHexColor($statusEnum),
        ];
    }

    /**
     * @return TicketStatusGeoJson
     */
    public function executeFromValue(string $statusValue): array
    {
        return $this->execute($statusValue);
    }

    /**
     * Colore pin mappa = hex. Le traduzioni enum possono usare token Filament (warning, orange…).
     */
    private function resolveMapHexColor(TicketStatusEnum $statusEnum): string
    {
        $raw = trim((string) $statusEnum->getColor());
        if ($this->isHexColor($raw)) {
            return $raw;
        }

        return match ($raw) {
            'warning' => '#f59e0b',
            'success' => '#16a34a',
            'danger' => '#dc2626',
            'info' => '#0ea5e9',
            'orange' => '#ea580c',
            'gray', 'secondary' => '#64748b',
            default => '#607d8b',
        };
    }

    private function isHexColor(string $color): bool
    {
        if ($color === '' || ! str_starts_with($color, '#')) {
            return false;
        }

        $digits = substr($color, 1);

        return (strlen($digits) === 3 || strlen($digits) === 6) && ctype_xdigit($digits);
    }

    /**
     * @return TicketStatusGeoJson
     */
    private function resolveFromValue(string $statusValue): array
    {
        $status = TicketStatusEnum::tryFrom($statusValue);

        if ($status instanceof TicketStatusEnum) {
            return $this->execute($status);
        }

        return [
            'value' => $statusValue,
            'label' => $statusValue,
            'color' => '#607d8b',
        ];
    }
}
