<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// Summary step of the /tickets/create wizard (DCP-33): texts of BuildTicketReviewSummaryAction.
// Same structure as lang/it/ticket_review.php.
// File: lang/en/ticket_review.php
return [
    'report' => [
        'title' => 'Report',
        'card_title' => 'Issue',
        'edit_label' => 'Edit',
        'map_label' => 'Location of the issue on the map',
        'fields' => [
            'address' => 'Address',
            'type' => 'Type of issue',
            'name' => 'Title',
            'content' => 'Details',
            'images' => 'Images',
        ],
    ],
    'author' => [
        'group_title' => 'General data',
        'card_title' => 'Report author',
    ],
    'contacts' => [
        'title' => 'Contact details',
        'fields' => [
            'phone' => 'Phone',
            'email' => 'Email',
        ],
    ],
];
