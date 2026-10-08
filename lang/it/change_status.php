<?php

declare(strict_types=1);

return [
    'fields' => [
        'status' => [
            'label' => 'status',
            'placeholder' => 'status',
            'helper_text' => 'status',
            'description' => 'status',
        ],
        'reason' => [
            'label' => 'reason',
            'description' => 'reason',
            'helper_text' => 'reason',
            'placeholder' => 'reason',
        ],
        'changeStatus' => [
            'label' => 'changeStatus',
        ],
    ],
    'actions' => [
        'cancel' => [
            'tooltip' => 'cancel',
            'icon' => 'cancel',
            'label' => 'cancel',
        ],
        'submit' => [
            'label' => 'submit',
            'icon' => 'submit',
            'tooltip' => 'submit',
        ],
    ],
];
