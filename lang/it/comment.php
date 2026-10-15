<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/comment.php
return [
    'fields' => [
        'user' => [
            'name' => ['label' => 'user.name'],
        ],
        'content' => ['label' => 'content'],
        'created_at' => ['label' => 'created_at'],
        'create' => ['label' => 'create'],
        'edit' => ['label' => 'edit'],
        'delete' => ['label' => 'delete'],
        'openFilters' => ['label' => 'openFilters'],
        'applyFilters' => ['label' => 'applyFilters'],
        'resetFilters' => ['label' => 'resetFilters'],
    ],
    'actions' => [
        'reorderRecords' => ['tooltip' => 'reorderRecords', 'icon' => 'reorderRecords', 'label' => 'reorderRecords'],
        'resetColumnManager' => ['tooltip' => 'resetColumnManager', 'icon' => 'resetColumnManager'],
    ],
];
