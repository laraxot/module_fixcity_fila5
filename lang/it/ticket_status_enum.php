<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/ticket_status_enum.php
/**
 * Traduzioni per {@see TicketStatusEnum}.
 *
 * Chiave namespace: fixcity::ticket_status (da TransTrait::getKeyTransClass).
 * Struttura: values.{enum_value}.{key}
 */
return [
    'values' => [
        'draft' => [
            'label' => 'Bozza',
            'color' => 'gray',
            'icon' => 'heroicon-o-pencil-square',
        ],
        'pending' => [
            'label' => 'In attesa',
            'color' => 'warning',
            'icon' => 'ui-hourglass',
        ],
        'in_review' => [
            'label' => 'In revisione',
            'color' => 'info',
            'icon' => 'heroicon-o-clock',
        ],
        'in_progress' => [
            'label' => 'In lavorazione',
            'color' => 'orange',
            'icon' => 'heroicon-o-arrow-path',
        ],
        'on_hold' => [
            'label' => 'In sospeso',
            'color' => 'danger',
            'icon' => 'heroicon-o-pause',
        ],
        'resolved' => [
            'label' => 'Risolto',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
        ],
        'closed' => [
            'label' => 'Chiuso',
            'color' => 'gray',
            'icon' => 'heroicon-o-x-circle',
        ],
        'reopened' => [
            'label' => 'Riaperto',
            'color' => 'secondary',
            'icon' => 'heroicon-o-arrow-uturn-left',
        ],
        'open' => [
            'label' => 'Aperto',
            'color' => 'warning',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
    ],
];