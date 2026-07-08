<?php

declare(strict_types=1);

return [
    'sections' => [
        'empty' => ['label' => 'empty', 'heading' => 'empty'],
        'detail' => [
            'label' => 'Report',
            'heading' => 'Report',
        ],
        'location' => [
            'label' => 'Location',
            'heading' => 'Location',
        ],
        'comments' => [
            'label' => 'Comments',
            'heading' => 'Comments',
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
        'attachments' => ['label' => 'attachments'],
        'legacy_images' => ['label' => 'legacy_images'],
        'location' => [
            'address' => ['label' => 'location.address'],
            'lat' => ['label' => 'location.lat'],
            'lng' => ['label' => 'location.lng'],
        ],
        'location_map' => ['label' => 'location_map'],
        'comments_list' => ['label' => 'comments_list'],
    ],
];
