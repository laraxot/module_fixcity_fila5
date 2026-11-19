<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// File: lang/de/ticket_type_enum.php
/**
 * Etichette di {@see TicketTypeEnum} (chiave fixcity::ticket_type_enum). Solo label: colore e icona sono quelli di `it`, descrizioni ricadono su `it`.
 */
return [
    'values' => [
        'road_maintenance' => [
            'label' => 'Straßeninstandhaltung',
            'color' => '#ff9800',
            'icon' => 'heroicon-o-wrench',
        ],
        'public_lighting' => [
            'label' => 'Öffentliche Beleuchtung',
            'color' => '#fbc02d',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'waste_collection' => [
            'label' => 'Abfallentsorgung',
            'color' => '#4caf50',
            'icon' => 'heroicon-o-trash',
        ],
        'parks_and_gardens' => [
            'label' => 'Grünanlagen und Parks',
            'color' => '#8bc34a',
            'icon' => 'heroicon-o-sparkles',
        ],
        'sewage_and_drainage' => [
            'label' => 'Kanalisation und Entwässerung',
            'color' => '#2196f3',
            'icon' => 'heroicon-o-archive-box',
        ],
        'public_buildings' => [
            'label' => 'Öffentliche Gebäude',
            'color' => '#3f51b5',
            'icon' => 'heroicon-o-building-office',
        ],
        'environmental_reports' => [
            'label' => 'Umweltmeldungen',
            'color' => '#f44336',
            'icon' => 'heroicon-o-globe-alt',
        ],
        'public_transport' => [
            'label' => 'Öffentlicher Verkehr',
            'color' => '#9c27b0',
            'icon' => 'fas-bus',
        ],
        'urban_furniture' => [
            'label' => 'Stadtmobiliar',
            'color' => '#00bcd4',
            'icon' => 'fas-couch',
        ],
        'public_safety' => [
            'label' => 'Öffentliche Sicherheit',
            'color' => '#ff5722',
            'icon' => 'heroicon-o-shield-check',
        ],
        'complaint' => [
            'label' => 'Beschwerde',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-triangle',
        ],
        'suggestion' => [
            'label' => 'Vorschlag',
            'color' => 'success',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'report' => [
            'label' => 'Meldung',
            'color' => 'warning',
            'icon' => 'heroicon-o-document-chart-bar',
        ],
        'request' => [
            'label' => 'Anfrage',
            'color' => 'info',
            'icon' => 'heroicon-o-document',
        ],
        'other' => [
            'label' => 'Sonstiges',
            'color' => 'gray',
            'icon' => 'heroicon-o-question-mark-circle',
        ],
    ],
];
