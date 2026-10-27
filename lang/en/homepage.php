<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/en/homepage.php
/**
 * Homepage translations - English
 *
 * Translation namespace: fixcity::homepage.*
 * Format: namespace::context.collection.element.type (5 levels)
 *
 * Used by: Themes/Sixteen/resources/views/components/blocks/ticket/map-preview.blade.php
 */
return [
    'seo' => [
        'title' => 'FixCity — Report a problem, follow the solution',
        'description' => 'Report a local issue, explore the public reports map and follow updates on your request from one civic platform.',
    ],
    'map_preview' => [
        'eyebrow' => [
            'label' => 'Community map',
        ],
        'intro' => [
            'text' => 'Explore the map to see reported issues and work in progress.',
        ],
        'legend' => [
            'label' => 'Public reports',
        ],
        'section' => [
            'title' => [
                'label' => 'Reports on the territory',
            ],
        ],
        'map' => [
            'aria_label' => 'Map of service issue reports in the municipal area',
        ],
        'cta_report' => [
            'label' => 'Report an issue',
        ],
        'cta_list' => [
            'label' => 'View all reports',
        ],
    ],
];
