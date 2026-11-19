<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_steps.php
return [
    'steps' => [
        'active' => [
            'label' => 'Active',
        ],
        'confirmed' => [
            'label' => 'Confirmed',
        ],
        'privacy' => [
            'label' => 'Privacy Policy',
        ],
        'data' => [
            'label' => 'Report Details',
        ],
        'summary' => [
            'label' => 'Summary',
        ],
    ],
];
