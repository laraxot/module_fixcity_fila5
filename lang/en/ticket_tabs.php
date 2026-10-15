<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_tabs.php
return [
    'tabs' => [
        'aria' => [
            'label' => 'Map and list view of reports',
        ],
        'map' => [
            'label' => 'Map',
        ],
        'list' => [
            'label' => 'List',
        ],
    ],
];
