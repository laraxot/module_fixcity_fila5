<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/en/enums.php
return [
    'pending' => [
        'label' => 'Pending',
    ],
    'in_review' => [
        'label' => 'In Review',
    ],
    'in_progress' => [
        'label' => 'In Progress',
    ],
    'on_hold' => [
        'label' => 'On Hold',
    ],
    'resolved' => [
        'label' => 'Resolved',
    ],
    'closed' => [
        'label' => 'Closed',
    ],
    'reopened' => [
        'label' => 'Reopened',
    ],
];
