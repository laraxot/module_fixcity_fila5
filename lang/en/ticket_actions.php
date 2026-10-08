<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_actions.php
return [
    'actions' => [
        'create' => [
            'label' => 'Create a report',
            'tooltip' => 'Create a new report',
        ],
        'assign' => [
            'label' => 'Assign operator',
        ],
        'back' => [
            'label' => 'Back',
        ],
        'save' => [
            'label' => 'Save Report',
        ],
        'save_short' => [
            'label' => 'Save',
        ],
        'save_draft' => [
            'label' => 'Save draft',
        ],
        'next' => [
            'label' => 'Next',
        ],
        'show_all' => [
            'label' => 'Show all',
        ],
        'remove_file' => [
            'aria' => [
                'label' => 'Remove file',
            ],
        ],
        'remove_image' => [
            'aria' => [
                'label' => 'Remove image',
            ],
        ],
        'submit' => [
            'label' => 'Confirm and submit',
        ],
    ],
];
