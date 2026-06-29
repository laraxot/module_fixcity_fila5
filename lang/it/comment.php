<?php

declare(strict_types=1);

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
