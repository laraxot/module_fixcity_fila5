<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/ticket_infolist.php
return [
    'sections' => [
        'empty' => ['label' => 'empty', 'heading' => 'empty'],
        'detail' => [
            'label' => 'Segnalazione',
            'heading' => 'Segnalazione',
        ],
        'location' => [
            'label' => 'Posizione',
            'heading' => 'Posizione',
        ],
        'comments' => [
            'label' => 'Commenti',
            'heading' => 'Commenti',
        ],
    ],
    'fields' => [
        'id' => ['label' => 'id'],
        'slug' => ['label' => 'slug'],
        'name' => ['label' => 'name'],
        'status' => ['label' => 'status'],
        'priority' => ['label' => 'priority'],
        'type' => ['label' => 'type'],
        'owner' => [
            'name' => ['label' => 'owner.name'],
        ],
        'assignee' => [
            'name' => ['label' => 'assignee.name'],
        ],
        'created_at' => ['label' => 'created_at'],
        'updated_at' => ['label' => 'updated_at'],
        'citizen_rating' => ['label' => 'citizen_rating'],
        'citizen_rated_at' => ['label' => 'citizen_rated_at'],
        'content' => ['label' => 'content'],
        'location' => [
            'address' => ['label' => 'location.address'],
            'lat' => ['label' => 'location.lat'],
            'lng' => ['label' => 'location.lng'],
        ],
        'images' => ['label' => 'images'],
        'comments_list' => ['label' => 'comments_list'],
        'location_map' => ['label' => 'location_map'],
        'legacy_images' => ['label' => 'legacy_images'],
        'attachments' => ['label' => 'attachments'],
    ],
];
