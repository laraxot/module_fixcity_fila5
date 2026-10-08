<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_card.php
return [
    'card' => [
        'type' => [
            'label' => 'Report type',
            'short' => 'Report',
        ],
        'expand' => [
            'button' => [
                'label' => 'Show details',
            ],
        ],
        'address' => [
            'label' => 'Address',
        ],
        'detail' => [
            'label' => 'Details',
        ],
        'edit' => [
            'link' => [
                'label' => 'Edit',
            ],
        ],
    ],
];
