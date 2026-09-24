<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/en/ticket_priority_enum.php
/**
 * Translations for {@see TicketPriorityEnum}.
 */
return [
    'values' => [
        'low' => [
            'label' => 'Low',
            'color' => 'success',
            'icon' => 'heroicon-o-arrow-down',
        ],
        'medium' => [
            'label' => 'Medium',
            'color' => 'warning',
            'icon' => 'heroicon-o-arrow-right',
        ],
        'high' => [
            'label' => 'High',
            'color' => 'danger',
            'icon' => 'heroicon-o-arrow-up',
        ],
        'critical' => [
            'label' => 'Critical',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
        'urgent' => [
            'label' => 'Urgent',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
    ],
];