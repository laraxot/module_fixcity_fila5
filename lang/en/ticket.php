<?php

declare(strict_types=1);

return [
    'fields' => [
        'images' => [
            'label' => 'Images',
        ],
    ],
    'messages' => [
        'no_tickets' => [
            'text' => 'No tickets found.',
        ],
        'images_uploaded' => [
            'text' => '{0} No images uploaded|{1} :count image uploaded|[2,*] :count images uploaded',
        ],
    ],
    'sections' => [
        'summary' => [
            'label' => 'Report Summary',
            'description' => 'Verify your data before submission',
        ],
        'images' => [
            'label' => 'Attached Images',
        ],
    ],
    'notifications' => [
        'submit_failed' => [
            'title' => 'Error',
            'body' => 'An error occurred during submission. Please try again.',
        ],
    ],
    'rules' => [
        'image' => [
            'max_files' => 10,
            'allowed_types' => 'jpeg, png, jpg, gif, webp',
        ],
    ],
];
