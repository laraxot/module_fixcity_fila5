<?php

declare(strict_types=1);

// Chiavi FO di fixcity::ticket presenti in it e mancanti in en (audit it/en 2026-10-07).
// File: lang/en/ticket_fo.php (unito da ticket.php)
return [
    'status' => [
        'label' => 'Status',
        'placeholder' => 'Select status',
        'options' => [
            'open' => 'Open',
            'in_progress' => 'In progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ],
    ],
    'priority' => [
        'label' => 'Priority',
        'placeholder' => 'Select priority',
        'help' => 'Indicates how urgent the report is',
        'description' => 'Priority level of the report',
        'helper_text' => 'Choose based on how urgent the problem is',
        'options' => [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ],
    ],
    'content' => [
        'label' => 'Content',
        'placeholder' => 'Describe the problem...',
        'help' => 'Provide a detailed description',
        'description' => 'Detailed description of the reported problem',
        'helper_text' => 'Give as much detail as possible to help us handle it',
    ],
    'created_at' => ['label' => 'Created at'],
    'updated_at' => ['label' => 'Last updated'],
    'citizen_rating' => [
        'label' => 'Citizen rating',
        'description' => '1–5 stars after resolution',
    ],
    'citizen_rated_at' => ['label' => 'Rating date'],
    'has_citizen_rating' => ['label' => 'With citizen rating'],
    'applyFilters' => ['label' => 'Apply filters'],
    'toggleColumns' => ['label' => 'Show/hide columns'],
    'value' => ['label' => 'Value'],
    'reorderRecords' => ['label' => 'Reorder items'],
    'card' => [
        'photos' => ['label' => 'Photos'],
    ],
    'contacts' => [
        'block_title' => ['label' => 'Contact the municipality'],
        'assistenza' => ['link' => ['label' => 'Request assistance']],
        'phone' => ['link' => ['label' => 'Call the toll-free number 05 0505']],
        'appointment' => ['link' => ['label' => 'Book an appointment']],
    ],
    'modal' => [
        'detail' => [
            'title' => ['label' => 'Report details'],
            'fields' => [
                'title' => ['label' => 'Title'],
                'type' => ['label' => 'Type of report'],
                'address' => ['label' => 'Address'],
                'detail' => ['label' => 'Details'],
                'images' => ['label' => 'Photos'],
            ],
            'cta' => ['label' => 'Close'],
            'image' => ['alt' => 'Photo of the reported problem'],
        ],
    ],
    'privacy_geolocation' => [
        'title' => ['label' => 'Report a problem'],
        'description' => ['text' => 'Read the privacy notice and consent to the processing of your personal data.'],
        'details' => [
            'text' => 'For details on how your personal data is processed, read the ',
            'link' => ['label' => 'privacy notice.'],
        ],
        'accept' => ['label' => 'I have read and understood the privacy notice'],
        'intro' => ['text' => 'The municipality must publish the privacy notice for this service before any personal data can be collected.'],
        'detail_prefix' => ['text' => 'For details on how your personal data is processed, read the '],
        'link' => ['label' => 'privacy notice.'],
        'checkbox' => ['label' => 'I have read and understood the privacy notice'],
        'error' => ['not_accepted' => 'You must accept the privacy notice to continue.'],
    ],
    'geolocation' => [
        'label' => 'Permissions and conditions',
        'data' => ['label' => 'Report data'],
        'summary' => ['label' => 'Summary'],
    ],
];
