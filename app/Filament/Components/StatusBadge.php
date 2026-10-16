<?php

/**
 * StatusBadge Component
 *
 * Displays a color-coded badge for ticket status.
 * Supported statuses:
 * - risolto (resolved) → green
 * - scartato (scrapped) → gray
 * - delegato (delegated) → blue
 * - non risolto (not resolved) → orange
 * - in attesa (waiting) → orange light
 *
 * @extends Filament\Components\BaseComponent
 */

namespace Modules\Fixcity\Filament\Components;

use Filament\Components\BaseComponent;

class StatusBadge extends BaseComponent
{
    /**
     * Available statuses mapped to colors.
     *
     * @value string
     */
    private const STATUS_COLORS = [
        'resolved' => '#16a34a',   // green – risolto
        'closed' => '#64748b',     // gray – scartato
        'in_progress' => '#2563eb', // blue – delegato
        'pending' => '#ea580c',     // orange – non risolto
        'on_hold' => '#fbbf24',    // orange light – in attesa
    ];

    /**
     * Display the badge based on the given status.
     *
     * @param  string|null  $status  The ticket status (TicketStatusEnum or string)
     * @return array Structured badge data
     */
    public function renderStatus($status): array
    {
        if ($status === null) {
            return [
                'icon' => 'fas fa-circle',
                'label' => 'Nessuno',
                'color' => '#9ca3af',
                'date' => null,
            ];
        }

        $statusValue = $this->normalizeStatus($status);

        return [
            'icon' => $this->getIconForStatus($statusValue),
            'label' => $this->labelForStatus($statusValue),
            'color' => $this->getColorForStatus($statusValue),
            'date' => $this->formatDate($statusValue),
        ];
    }

    /**
     * Normalize the status to a known TicketStatusEnum value.
     *
     * @param  mixed  $status  Input status
     * @return string Normalized status value
     */
    private function normalizeStatus(mixed $status): string
    {
        if (is_null($status)) {
            return 'open';
        }

        if (is_string($status)) {
            return $this->enumToValue($status);
        }

        return $status->value;
    }

    /**
     * Get the icon for a given status.
     */
    private function getIconForStatus(string $statusValue): string
    {
        $mapping = [
            'resolved' => 'fa fa-check-circle',
            'closed' => 'fa fa-xmark',
            'in_progress' => 'fa fa-spinner',
            'pending' => 'fa fa-clock-o',
            'on_hold' => 'fa fa-clock-half',
        ];

        return $mapping[$statusValue] ?? 'fa fa-circle';
    }

    /**
     * Get the label for a given status.
     */
    private function labelForStatus(string $statusValue): string
    {
        return match ($statusValue) {
            'resolved' => 'Risolto',
            'closed' => 'Chiuso',
            'in_progress' => 'In corso',
            'pending' => 'In attesa',
            'on_hold' => 'In attesa',
            default => 'Altro',
        };
    }

    /**
     * Get the color for a given status.
     */
    private function getColorForStatus(string $statusValue): string
    {
        return self::STATUS_COLORS[$statusValue] ?? '#9ca3af';
    }

    /**
     * Format the date for the badge (optional).
     */
    private function formatDate(string $statusValue): ?string
    {
        // In a real implementation, this would use the ticket's created_at/updated_at
        // For now, return null (no date displayed)
        return null;
    }

    /**
     * Get the normalized status value from a TicketStatusEnum.
     */
    private function enumToValue(TicketStatusEnum $status): string
    {
        return $status->value;
    }
}
