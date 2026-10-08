<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/edit_ticket.php
return [
    'fields' => [
        'category_id' => [
            'label' => 'category_id',
        ],
        'title' => [
            'label' => 'title',
        ],
        'content' => [
            'label' => 'content',
        ],
        'status' => [
            'label' => 'status',
        ],
        'priority' => [
            'label' => 'priority',
        ],
        'attachments' => [
            'label' => 'attachments',
        ],
        'type' => [
            'label' => 'type',
        ],
        'images' => [
            'label' => 'images',
        ],
    ],
    'actions' => [
        'cancel' => [
            'label' => 'cancel',
        ],
        'save' => [
            'label' => 'save',
        ],
    ],
];
