<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_map.php
return [
    'map' => [
        'image' => [
            'alt' => 'Reports map',
        ],
        'cta' => [
            'title' => [
                'label' => 'Have you noticed a service issue?',
            ],
            'text' => [
                'label' => 'Send a new report and help the municipality respond faster.',
            ],
            'button' => [
                'label' => 'Report an issue',
            ],
        ],
    ],
];
