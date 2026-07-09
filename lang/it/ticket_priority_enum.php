<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/ticket_priority_enum.php
/**
 * Traduzioni per {@see TicketPriorityEnum}.
 *
 * Chiave namespace: fixcity::ticket_priority (da TransTrait).
 * Struttura: values.{enum_value}.{key}
 */
return [
    'values' => [
        'low' => [
            'label' => 'Bassa',
            'color' => 'success',
            'icon' => 'heroicon-o-arrow-down',
        ],
        'medium' => [
            'label' => 'Media',
            'color' => 'warning',
            'icon' => 'heroicon-o-arrow-right',
        ],
        'high' => [
            'label' => 'Alta',
            'color' => 'danger',
            'icon' => 'heroicon-o-arrow-up',
        ],
        'critical' => [
            'label' => 'Critica',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
        'urgent' => [
            'label' => 'Urgente',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
    ],
];