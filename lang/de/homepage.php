<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// File: lang/de/homepage.php
/**
 * Homepage translations - Deutsch (DCP-120).
 *
 * Translation namespace: fixcity::homepage.*
 * Glossar: Themes/Sixteen/docs/i18n/01-glossario.md (segnalazione = Meldung, Sie-Form).
 *
 * Used by: Themes/Sixteen/resources/views/components/blocks/ticket/map-preview.blade.php
 */
return [
    'seo' => [
        'title' => 'FixCity — Problem melden, Lösung verfolgen',
        'description' => 'Melden Sie ein lokales Problem, sehen Sie öffentliche Meldungen auf der Karte und verfolgen Sie die Aktualisierungen Ihres Vorgangs.',
    ],
    'map_preview' => [
        'section' => [
            'title' => [
                'label' => 'Meldungen im Gemeindegebiet',
            ],
        ],
        'map' => [
            'aria_label' => 'Karte der Störungsmeldungen im Gemeindegebiet',
        ],
        'cta_report' => [
            'label' => 'Störung melden',
        ],
        'cta_list' => [
            'label' => 'Alle Meldungen ansehen',
        ],
    ],
];
