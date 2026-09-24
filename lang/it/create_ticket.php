<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/create_ticket.php
return [
    'steps' => [
        'Step 1' => [
            'label' => 'Step 1',
        ],
        'Step 2' => [
            'label' => 'Step 2',
        ],
        'step-2' => [
            'label' => 'step-2',
        ],
        'step-1' => [
            'label' => 'step-1',
        ],
    ],
    'fields' => [
        'name' => [
            'label' => 'name',
        ],
        'accept_terms' => [
            'label' => 'accept_terms',
            'description' => 'accept_terms',
            'helper_text' => 'accept_terms',
            'placeholder' => 'accept_terms',
        ],
        'privacy_notice' => [
            'label' => 'privacy_notice',
            'placeholder' => 'privacy_notice',
            'helper_text' => 'privacy_notice',
            'description' => 'privacy_notice',
        ],
        'images' => [
            'label' => 'images',
        ],
        'location' => [
            'label' => 'location',
        ],
        'longitude' => [
            'label' => 'longitude',
        ],
        'latitude' => [
            'label' => 'latitude',
        ],
        'content' => [
            'label' => 'content',
        ],
        'priority' => [
            'label' => 'priority',
        ],
        'slug' => [
            'label' => 'slug',
        ],
        'type' => [
            'label' => 'type',
        ],
        'media' => [
            'label' => 'media',
        ],
        'status' => [
            'label' => 'status',
        ],
    ],
];
