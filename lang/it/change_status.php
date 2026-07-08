<?php

declare(strict_types=1);

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
