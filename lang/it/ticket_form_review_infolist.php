<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/ticket_form_review_infolist.php
return [
    'fields' => [
        'review_location' => [
            'label' => 'Posizione',
        ],
        'review_type' => [
            'label' => 'Categoria',
        ],
        'review_priority' => [
            'label' => 'Priorità',
        ],
        'review_name' => [
            'label' => 'Titolo',
        ],
        'review_content' => [
            'label' => 'Descrizione',
        ],
        'review_images' => [
            'label' => 'Allegati',
        ],
        'review_author_name' => [
            'label' => 'review_author_name',
        ],
        'review_author_fiscal_code' => [
            'label' => 'review_author_fiscal_code',
        ],
        'review_contact_phone' => [
            'label' => 'review_contact_phone',
        ],
        'review_contact_email' => [
            'label' => 'review_contact_email',
        ],
    ],
];
