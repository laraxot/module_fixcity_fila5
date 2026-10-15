<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// File: lang/es/ticket_type_enum.php
/**
 * Etichette di {@see TicketTypeEnum} (chiave fixcity::ticket_type_enum). Solo label: colore e icona sono quelli di `it`, descrizioni ricadono su `it`.
 */
return [
    'values' => [
        'road_maintenance' => [
            'label' => 'Mantenimiento de carreteras',
            'color' => '#ff9800',
            'icon' => 'heroicon-o-wrench',
        ],
        'public_lighting' => [
            'label' => 'Alumbrado público',
            'color' => '#fbc02d',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'waste_collection' => [
            'label' => 'Recogida de residuos',
            'color' => '#4caf50',
            'icon' => 'heroicon-o-trash',
        ],
        'parks_and_gardens' => [
            'label' => 'Zonas verdes y parques',
            'color' => '#8bc34a',
            'icon' => 'heroicon-o-sparkles',
        ],
        'sewage_and_drainage' => [
            'label' => 'Alcantarillado y drenaje',
            'color' => '#2196f3',
            'icon' => 'heroicon-o-archive-box',
        ],
        'public_buildings' => [
            'label' => 'Edificios públicos',
            'color' => '#3f51b5',
            'icon' => 'heroicon-o-building-office',
        ],
        'environmental_reports' => [
            'label' => 'Avisos medioambientales',
            'color' => '#f44336',
            'icon' => 'heroicon-o-globe-alt',
        ],
        'public_transport' => [
            'label' => 'Transporte público',
            'color' => '#9c27b0',
            'icon' => 'fas-bus',
        ],
        'urban_furniture' => [
            'label' => 'Mobiliario urbano',
            'color' => '#00bcd4',
            'icon' => 'fas-couch',
        ],
        'public_safety' => [
            'label' => 'Seguridad pública',
            'color' => '#ff5722',
            'icon' => 'heroicon-o-shield-check',
        ],
        'complaint' => [
            'label' => 'Reclamación',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-triangle',
        ],
        'suggestion' => [
            'label' => 'Sugerencia',
            'color' => 'success',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'report' => [
            'label' => 'Incidencia',
            'color' => 'warning',
            'icon' => 'heroicon-o-document-chart-bar',
        ],
        'request' => [
            'label' => 'Solicitud',
            'color' => 'info',
            'icon' => 'heroicon-o-document',
        ],
        'other' => [
            'label' => 'Otro',
            'color' => 'gray',
            'icon' => 'heroicon-o-question-mark-circle',
        ],
    ],
];
