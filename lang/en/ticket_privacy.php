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
            'text' => 'The system must have a published privacy notice before personal data can be collected.',
        ],
        'detail_prefix' => [
            'text' => 'For details on the processing of personal data, see the ',
        ],
        'link' => [
            'label' => 'privacy policy.',
        ],
        'links_prefix' => [
            'text' => 'Learn more:',
        ],
        'terms_label' => [
            'label' => 'Terms and disclaimer',
        ],
        'cookie_label' => [
            'label' => 'Cookie policy',
        ],
        'legal_label' => [
            'label' => 'Legal notice',
        ],
        'checkbox' => [
            'label' => 'I have read and understood the privacy policy',
        ],
        'error' => [
            'not_accepted' => 'You must accept the privacy policy to continue.',
        ],
        'page_title' => 'Privacy notice',
        'not_configured' => 'The system does not have a published privacy notice yet. Report submission is temporarily unavailable.',
        'open_notice' => 'Read the privacy notice',
    ],
];
