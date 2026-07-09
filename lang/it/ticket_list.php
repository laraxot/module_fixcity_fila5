<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/ticket_list.php
return [
    'fields' => [
        'title' => ['label' => 'title', 'placeholder' => 'title', 'helper_text' => 'title', 'description' => 'title'],
        'sub_title' => ['label' => 'sub_title', 'placeholder' => 'sub_title', 'helper_text' => 'sub_title', 'description' => 'sub_title'],
        'method' => ['label' => 'method', 'placeholder' => 'method', 'helper_text' => 'method', 'description' => 'method'],
        'limit' => ['label' => 'limit', 'placeholder' => 'limit', 'helper_text' => 'limit', 'description' => 'limit'],
        '_tpl' => ['label' => '_tpl'],
        'view' => ['label' => 'view', 'placeholder' => 'view', 'helper_text' => 'view', 'description' => 'view'],
    ],
];
