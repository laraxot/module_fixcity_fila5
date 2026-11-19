<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_load_more.php
return [
    'load-more' => [
        'button' => [
            'label' => 'Load more reports',
        ],
    ],
    'pagination' => [
        'aria' => 'Reports pagination',
        'page' => 'Page :page',
        'previous' => 'Previous page',
        'next' => 'Next page',
    ],
];
