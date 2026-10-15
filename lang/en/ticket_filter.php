<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_filter.php
return [
    'assignment_status' => [
        'assigned' => 'Assigned',
        'unassigned' => 'Unassigned',
    ],
    'filter' => [
        'button' => [
            'label' => 'Filter',
        ],
        'remove' => [
            'label' => 'Clear filters',
        ],
    ],
];
