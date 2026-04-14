<?php

declare(strict_types=1);

return [
    /*
     * Ticket Status Translations
     */
    'pending' => [
        'label' => 'In Attesa',
    ],
    'in_review' => [
        'label' => 'In Revisione',
    ],
    'in_progress' => [
        'label' => 'In Corso',
    ],
    'on_hold' => [
        'label' => 'In Attesa di Riscontro',
    ],
    'resolved' => [
        'label' => 'Risolto',
    ],
    'closed' => [
        'label' => 'Chiuso',
    ],
    'reopened' => [
        'label' => 'Riaperto',
    ],

    /*
     * Ticket Type Translations - TicketTypeEnum values
     */
    'road_maintenance' => [
        'label' => 'Manutenzione Stradale',
        'description' => 'Segnalazioni relative a buche, crepe, segnaletica stradale danneggiata',
        'color' => '#ff9800',
    ],
    'public_lighting' => [
        'label' => 'Illuminazione Pubblica',
        'description' => 'Lampioni non funzionanti o danneggiati',
        'color' => '#fbc02d',
    ],
    'waste_collection' => [
        'label' => 'Raccolta Rifiuti',
        'description' => 'Problemi con la raccolta dei rifiuti, cestini pieni',
        'color' => '#4caf50',
    ],
    'parks_and_gardens' => [
        'label' => 'Aree Verdi e Parchi',
        'description' => 'Manutenzione di parchi, giardini, alberi caduti',
        'color' => '#8bc34a',
    ],
    'sewage_and_drainage' => [
        'label' => 'Fognature e Drenaggi',
        'description' => 'Problemi con fognature o drenaggi bloccati',
        'color' => '#2196f3',
    ],
    'public_buildings' => [
        'label' => 'Edifici Pubblici',
        'description' => 'Riparazioni in edifici pubblici come scuole, biblioteche',
        'color' => '#3f51b5',
    ],
    'environmental_reports' => [
        'label' => 'Segnalazioni Ambientali',
        'description' => 'Problemi di inquinamento, smaltimento illecito di rifiuti',
        'color' => '#f44336',
    ],
    'public_transport' => [
        'label' => 'Trasporti Pubblici',
        'description' => 'Problemi legati ai servizi di trasporto pubblico',
        'color' => '#9c27b0',
    ],
    'urban_furniture' => [
        'label' => 'Arredo Urbano',
        'description' => 'Manutenzione di panchine, cestini, fontane urbane',
        'color' => '#00bcd4',
    ],
    'public_safety' => [
        'label' => 'Sicurezza Pubblica',
        'description' => 'Problemi che riguardano la sicurezza dei cittadini',
        'color' => '#ff5722',
    ],
    'complaint' => [
        'label' => 'Reclamo',
    ],
    'suggestion' => [
        'label' => 'Suggerimento',
    ],
    'report' => [
        'label' => 'Segnalazione',
    ],
    'request' => [
        'label' => 'Richiesta',
    ],
    'other' => [
        'label' => 'Altro',
    ],
];
