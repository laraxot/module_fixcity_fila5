<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// File: lang/de/ticket_status_enum.php
/**
 * Etichette di {@see TicketStatusEnum} (chiave fixcity::ticket_status_enum). Solo label: colore e icona sono quelli di `it`, descrizioni ricadono su `it`.
 */
return [
    'values' => [
        'draft' => [
            'label' => 'Entwurf',
            'color' => 'gray',
            'icon' => 'heroicon-o-pencil-square',
        ],
        'pending' => [
            'label' => 'Ausstehend',
            'color' => 'warning',
            'icon' => 'ui-hourglass',
        ],
        'in_review' => [
            'label' => 'In Prüfung',
            'color' => 'info',
            'icon' => 'heroicon-o-clock',
        ],
        'in_progress' => [
            'label' => 'In Bearbeitung',
            'color' => 'orange',
            'icon' => 'heroicon-o-arrow-path',
        ],
        'on_hold' => [
            'label' => 'Zurückgestellt',
            'color' => 'danger',
            'icon' => 'heroicon-o-pause',
        ],
        'resolved' => [
            'label' => 'Erledigt',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
        ],
        'closed' => [
            'label' => 'Geschlossen',
            'color' => 'gray',
            'icon' => 'heroicon-o-x-circle',
        ],
        'reopened' => [
            'label' => 'Wieder geöffnet',
            'color' => 'secondary',
            'icon' => 'heroicon-o-arrow-uturn-left',
        ],
        'open' => [
            'label' => 'Offen',
            'color' => 'warning',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
    ],
];
