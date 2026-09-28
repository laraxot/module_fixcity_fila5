<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_contacts.php
return [
    'contacts' => [
        'title' => [
            'label' => 'Need help?',
        ],
        'faq' => [
            'link' => [
                'label' => 'Read the frequently asked questions',
            ],
        ],
    ],
];
