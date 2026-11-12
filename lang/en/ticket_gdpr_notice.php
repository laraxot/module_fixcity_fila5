<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_gdpr_notice.php
return [
    'gdpr_notice' => [
        'text' => 'FixCity uses the data you enter to receive your report about a local issue, manage it and keep you updated.',
        'text_no_name' => 'FixCity uses the data you enter to receive your report about a local issue, manage it and keep you updated.',
        'privacy_link' => 'privacy policy',
    ],
];
