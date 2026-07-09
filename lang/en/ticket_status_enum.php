<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/en/ticket_status_enum.php
/**
 * Translations for {@see TicketStatusEnum}.
 */
return [
    'values' => [
        'draft' => [
            'label' => 'Draft',
            'color' => 'gray',
            'icon' => 'heroicon-o-pencil-square',
        ],
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning',
            'icon' => 'ui-hourglass',
        ],
        'in_review' => [
            'label' => 'In Review',
            'color' => 'info',
            'icon' => 'heroicon-o-clock',
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'color' => 'orange',
            'icon' => 'heroicon-o-arrow-path',
        ],
        'on_hold' => [
            'label' => 'On Hold',
            'color' => 'danger',
            'icon' => 'heroicon-o-pause',
        ],
        'resolved' => [
            'label' => 'Resolved',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
        ],
        'closed' => [
            'label' => 'Closed',
            'color' => 'gray',
            'icon' => 'heroicon-o-x-circle',
        ],
        'reopened' => [
            'label' => 'Reopened',
            'color' => 'secondary',
            'icon' => 'heroicon-o-arrow-uturn-left',
        ],
        'open' => [
            'label' => 'Open',
            'color' => 'warning',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
    ],
];