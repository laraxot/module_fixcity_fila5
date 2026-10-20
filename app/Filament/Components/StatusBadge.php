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
 */

namespace Modules\Fixcity\Filament\Components;

use Modules\Fixcity\Enums\TicketStatusEnum;

final class StatusBadge
{
    /**
     * Colori badge per stati pubblici del workflow ticket.
     *
     * @var array<string, string>
     */
    private const array STATUS_COLORS = [
        'resolved' => '#16a34a',
        'closed' => '#64748b',
        'in_progress' => '#2563eb',
        'pending' => '#ea580c',
        'on_hold' => '#fbbf24',
        'open' => '#3b82f6',
        'draft' => '#94a3b8',
        'in_review' => '#8b5cf6',
        'reopened' => '#f97316',
    ];

    /**
     * Display the badge based on the given status.
     *
     * @param  string|null  $status  The ticket status (TicketStatusEnum or string)
     * @return array Structured badge data
     */
    /** @return array{icon: string, label: string, color: string, date: string|null} */
    public function renderStatus(mixed $status): array
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
            'date' => $this->formatDate(),
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
            return $status;
        }

        return $status instanceof TicketStatusEnum ? $status->value : 'open';
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
    private function formatDate(): null
    {
        // In a real implementation, this would use the ticket's created_at/updated_at
        // For now, return null (no date displayed)
        return null;
    }
}
