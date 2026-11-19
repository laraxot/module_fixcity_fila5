<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_create_options.php
return [
    'create_options' => [
        'public_damage' => [
            'label' => 'Public property damage',
        ],
        'maintenance' => [
            'label' => 'Road maintenance',
        ],
        'urban_decorum' => [
            'label' => 'Urban furniture',
        ],
    ],
];
