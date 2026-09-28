<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_privacy.php
return [
    'privacy' => [
        'title' => [
            'label' => 'Service Report',
        ],
        'description' => [
            'text' => 'Read the privacy policy and consent to the processing of personal data.',
        ],
        'details' => [
            'text' => 'For details on the processing of personal data, see the ',
            'link' => [
                'label' => 'privacy policy.',
            ],
        ],
        'accept' => [
            'label' => 'I have read and understood the privacy policy',
        ],
        'intro' => [
            'text' => 'The municipality must publish the privacy notice for this service before personal data can be collected.',
        ],
        'detail_prefix' => [
            'text' => 'For details on the processing of personal data, see the ',
        ],
        'link' => [
            'label' => 'privacy policy.',
        ],
        'checkbox' => [
            'label' => 'I have read and understood the privacy policy',
        ],
        'error' => [
            'not_accepted' => 'You must accept the privacy policy to continue.',
        ],
        'page_title' => 'Privacy notice',
        'not_configured' => 'The municipality has not published the privacy notice for this service yet. Report submission is temporarily unavailable.',
        'open_notice' => 'Read the privacy notice',
    ],
];
