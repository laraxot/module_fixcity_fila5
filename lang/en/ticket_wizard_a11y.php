<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_wizard_a11y.php
return [
    'wizard_a11y' => [
        'skip_to_main' => [
            'label' => 'Skip to main content',
            'placeholder' => '',
            'help' => 'Skip navigation and go directly to the report form.',
        ],
        'main_region' => [
            'label' => 'Report form',
            'placeholder' => '',
            'help' => 'Wizard steps and submission form.',
        ],
    ],
];
