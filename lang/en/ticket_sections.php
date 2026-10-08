<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_sections.php
return [
    'sections' => [
        'place' => [
            'label' => 'Location',
            'description' => 'Indicate the location of the issue',
        ],
        'inefficiency' => [
            'label' => 'Issue',
            'description' => '',
        ],
        'author' => [
            'label' => 'Report Author',
            'description' => 'Information about you',
        ],
        'summary' => [
            'label' => 'Summary',
            'description' => '',
        ],
        'contacts' => [
            'label' => 'Contacts',
            'edit_action' => 'Edit',
        ],
    ],
];
