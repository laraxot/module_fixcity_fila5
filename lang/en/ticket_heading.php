<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_heading.php
return [
    'heading' => [
        'report' => [
            'label' => 'Service Report',
        ],
        'report_author' => [
            'label' => 'Report Author',
            'description' => 'Information about you',
        ],
        'contacts' => [
            'label' => 'Contacts',
        ],
        'title' => [
            'label' => 'Reports',
        ],
        'subtitle' => [
            'text' => 'Browse local reports on the map or filter the list by category.',
        ],
    ],
];
