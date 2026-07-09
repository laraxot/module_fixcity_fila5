<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/change_status.php
return [
    'fields' => [
        'status' => ['label' => 'status'],
        'reason' => ['label' => 'reason', 'description' => 'reason', 'helper_text' => 'reason', 'placeholder' => 'reason'],
        'changeStatus' => ['label' => 'changeStatus'],
    ],
    'actions' => [
        'cancel' => ['tooltip' => 'cancel', 'icon' => 'cancel'],
    ],
];
