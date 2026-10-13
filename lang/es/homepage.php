<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// File: lang/es/homepage.php
/**
 * Homepage translations - Español (DCP-120).
 *
 * Translation namespace: fixcity::homepage.*
 * Glosario: Themes/Sixteen/docs/i18n/01-glossario.md (segnalazione = incidencia, tuteo).
 *
 * Used by: Themes/Sixteen/resources/views/components/blocks/ticket/map-preview.blade.php
 */
return [
    'map_preview' => [
        'section' => [
            'title' => [
                'label' => 'Incidencias en el territorio',
            ],
        ],
        'map' => [
            'aria_label' => 'Mapa de las incidencias de servicio en el término municipal',
        ],
        'cta_report' => [
            'label' => 'Comunicar una incidencia',
        ],
        'cta_list' => [
            'label' => 'Ver todas las incidencias',
        ],
    ],
];
