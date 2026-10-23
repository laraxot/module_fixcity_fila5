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
    'seo' => [
        'title' => 'FixCity — Comunica un problema, sigue la solución',
        'description' => 'Comunica una incidencia local, consulta el mapa público y sigue las actualizaciones de tu trámite desde una sola plataforma.',
    ],
    'map_preview' => [
        'eyebrow' => [
            'label' => 'Mapa de la comunidad',
        ],
        'intro' => [
            'text' => 'Consulta el mapa para conocer los problemas ya señalados y los trabajos en curso.',
        ],
        'legend' => [
            'label' => 'Incidencias públicas',
        ],
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
