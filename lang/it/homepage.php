<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/homepage.php
/**
 * Homepage translations - Italian
 *
 * Translation namespace: fixcity::homepage.*
 * Format: namespace::context.collection.element.type (5 levels)
 *
 * Used by: Themes/Sixteen/resources/views/components/blocks/ticket/map-preview.blade.php
 */
return [
    'map_preview' => [
        'section' => [
            'title' => [
                'label' => 'Segnalazioni sul territorio',
            ],
        ],
        'map' => [
            'aria_label' => 'Mappa delle segnalazioni di disservizio sul territorio comunale',
        ],
        'cta_report' => [
            'label' => 'Segnala un disservizio',
        ],
        'cta_list' => [
            'label' => 'Vedi tutte le segnalazioni',
        ],
    ],
];
