<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/en/ticket_form.php
return [
    'summaries' => [
        'images_none' => 'No images attached',
        'images_choice' => '{1} One image attached|[2,*] :count images attached',
    ],
    'fields' => [
        'review_location' => [
            'label' => 'Location',
            'description' => 'Selected place or coordinates',
        ],
        'review_type' => [
            'label' => 'Report type',
            'description' => 'Chosen category',
        ],
        'review_priority' => [
            'label' => 'Priority',
            'description' => 'Selected urgency',
        ],
        'review_name' => [
            'label' => 'Title / summary name',
            'description' => 'Short title provided by the user',
        ],
        'review_content' => [
            'label' => 'Details',
            'description' => 'Description of the issue',
        ],
        'review_images' => [
            'label' => 'Attached images',
            'description' => 'Upload summary',
        ],
        'review_author_name' => [
            'label' => 'Full name',
            'description' => 'Author recap',
        ],
        'review_author_fiscal_code' => [
            'label' => 'Tax ID',
            'description' => 'Author fiscal code recap',
        ],
        'review_contact_phone' => [
            'label' => 'Phone',
            'description' => 'Contact phone recap',
        ],
        'review_contact_email' => [
            'label' => 'Contact email',
            'description' => 'Contact email recap',
        ],
        'gdpr_text' => [
            'label' => 'Privacy notice',
        ],
    ],
    'steps' => [
        'privacy' => [
            'label' => 'Authorisations and conditions',
        ],
        'data' => [
            'label' => 'Report details',
        ],
        'summary' => [
            'label' => 'Summary',
        ],
    ],
];
