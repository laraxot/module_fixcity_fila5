<?php

declare(strict_types=1);

use Modules\Fixcity\Enums\TicketTypeEnum;

/**
 * Traduzioni per {@see TicketTypeEnum}.
 *
 * Chiave namespace: fixcity::ticket_type_enum (da TransTrait::getKeyTransClass).
 * Struttura: values.{enum_value}.{key}
 */
return [
    'values' => [
        'road_maintenance' => [
            'label' => 'Manutenzione Stradale',
            'description' => 'Segnalazioni relative a buche, crepe, segnaletica stradale danneggiata',
            'color' => '#ff9800',
            'icon' => 'heroicon-o-wrench',
        ],
        'public_lighting' => [
            'label' => 'Illuminazione Pubblica',
            'description' => 'Lampioni non funzionanti o danneggiati',
            'color' => '#fbc02d',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'waste_collection' => [
            'label' => 'Raccolta Rifiuti',
            'description' => 'Problemi con la raccolta dei rifiuti, cestini pieni',
            'color' => '#4caf50',
            'icon' => 'heroicon-o-trash',
        ],
        'parks_and_gardens' => [
            'label' => 'Aree Verdi e Parchi',
            'description' => 'Manutenzione di parchi, giardini, alberi caduti',
            'color' => '#8bc34a',
            'icon' => 'heroicon-o-sparkles',
        ],
        'sewage_and_drainage' => [
            'label' => 'Fognature e Drenaggi',
            'description' => 'Problemi con fognature o drenaggi bloccati',
            'color' => '#2196f3',
            'icon' => 'heroicon-o-archive-box',
        ],
        'public_buildings' => [
            'label' => 'Edifici Pubblici',
            'description' => 'Riparazioni in edifici pubblici come scuole, biblioteche',
            'color' => '#3f51b5',
            'icon' => 'heroicon-o-building-office',
        ],
        'environmental_reports' => [
            'label' => 'Segnalazioni Ambientali',
            'description' => 'Problemi di inquinamento, smaltimento illecito di rifiuti',
            'color' => '#f44336',
            'icon' => 'heroicon-o-globe-alt',
        ],
        'public_transport' => [
            'label' => 'Trasporti Pubblici',
            'description' => 'Problemi legati ai servizi di trasporto pubblico',
            'color' => '#9c27b0',
            'icon' => 'fas-bus',
        ],
        'urban_furniture' => [
            'label' => 'Arredo Urbano',
            'description' => 'Manutenzione di panchine, cestini, fontane urbane',
            'color' => '#00bcd4',
            'icon' => 'fas-couch',
        ],
        'public_safety' => [
            'label' => 'Sicurezza Pubblica',
            'description' => 'Problemi che riguardano la sicurezza dei cittadini',
            'color' => '#ff5722',
            'icon' => 'heroicon-o-shield-check',
        ],
        'complaint' => [
            'label' => 'Reclamo',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-triangle',
        ],
        'suggestion' => [
            'label' => 'Suggerimento',
            'color' => 'success',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'report' => [
            'label' => 'Segnalazione',
            'color' => 'warning',
            'icon' => 'heroicon-o-document-report',
        ],
        'request' => [
            'label' => 'Richiesta',
            'color' => 'info',
            'icon' => 'heroicon-o-document',
        ],
        'other' => [
            'label' => 'Altro',
            'color' => 'gray',
            'icon' => 'heroicon-o-question-mark-circle',
        ],
    ],
];
