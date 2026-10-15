<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// File: lang/es/ticket_status_enum.php
/**
 * Etichette di {@see TicketStatusEnum} (chiave fixcity::ticket_status_enum). Solo label: colore e icona sono quelli di `it`, descrizioni ricadono su `it`.
 */
return [
    'values' => [
        'draft' => [
            'label' => 'Borrador',
            'color' => 'gray',
            'icon' => 'heroicon-o-pencil-square',
        ],
        'pending' => [
            'label' => 'Pendiente',
            'color' => 'warning',
            'icon' => 'ui-hourglass',
        ],
        'in_review' => [
            'label' => 'En revisión',
            'color' => 'info',
            'icon' => 'heroicon-o-clock',
        ],
        'in_progress' => [
            'label' => 'En curso',
            'color' => 'orange',
            'icon' => 'heroicon-o-arrow-path',
        ],
        'on_hold' => [
            'label' => 'En espera',
            'color' => 'danger',
            'icon' => 'heroicon-o-pause',
        ],
        'resolved' => [
            'label' => 'Resuelta',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
        ],
        'closed' => [
            'label' => 'Cerrada',
            'color' => 'gray',
            'icon' => 'heroicon-o-x-circle',
        ],
        'reopened' => [
            'label' => 'Reabierta',
            'color' => 'secondary',
            'icon' => 'heroicon-o-arrow-uturn-left',
        ],
        'open' => [
            'label' => 'Abierta',
            'color' => 'warning',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
    ],
];
