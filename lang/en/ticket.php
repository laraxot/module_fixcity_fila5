<?php

declare(strict_types=1);

return [
    'fields' => [
        'type' => [
            'label' => 'Issue type',
        ],
        'title' => [
            'label' => 'Title',
        ],
        'content' => [
            'label' => 'Details',
        ],
        'name' => [
            'label' => 'Full name',
        ],
        'email' => [
            'label' => 'Email',
        ],
        'address' => [
            'label' => 'Location',
        ],
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
        'summary_cosa' => [
            'label' => '1. What',
        ],
        'summary_dove' => [
            'label' => '2. Where',
        ],
        'summary_dettagli' => [
            'label' => '3. Details',
        ],
        'author' => [
            'label' => 'Report author',
            'description' => 'About you',
        ],
        'contacts' => [
            'label' => 'Contact details',
            'edit_action' => 'Edit',
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

    /*
     * Frontoffice (Design Comuni) — domain: ticket, UI label: reports
     */
    'breadcrumb' => [
        'home' => [
            'label' => 'Home',
        ],
        'elenco' => [
            'label' => 'Reports',
        ],
    ],

    'heading' => [
        'title' => [
            'label' => 'Reports',
        ],
        'subtitle' => [
            'text' => 'Browse open reports in your area and filter by category.',
        ],
    ],

    'filters' => [
        'legend' => [
            'label' => 'Filter by category',
        ],
        'empty' => 'No categories available at the moment.',
    ],

    'results' => [
        'count' => [
            'text' => ':count reports found',
        ],
        'empty' => 'No reports found.',
    ],

    'filter' => [
        'button' => [
            'label' => 'Filter',
        ],
        'remove' => [
            'label' => 'Clear filters',
        ],
    ],

    'tabs' => [
        'aria' => [
            'label' => 'Map and list view',
        ],
        'map' => [
            'label' => 'Map',
        ],
        'list' => [
            'label' => 'List',
        ],
    ],

    'map' => [
        'image' => [
            'alt' => 'Reports map',
        ],
        'cta' => [
            'title' => [
                'label' => 'Noticed an issue?',
            ],
            'text' => [
                'label' => 'Submit a new report to help the municipality respond faster.',
            ],
            'button' => [
                'label' => 'Report an issue',
            ],
        ],
    ],

    'contacts' => [
        'block_title' => [
            'label' => 'Contact the municipality',
        ],
        'title' => [
            'label' => 'Need help?',
        ],
        'faq' => [
            'link' => [
                'label' => 'Read the FAQ',
            ],
        ],
        'assistenza' => [
            'link' => [
                'label' => 'Request support',
            ],
        ],
        'phone' => [
            'link' => [
                'label' => 'Call the toll-free number :phone',
            ],
        ],
        'appointment' => [
            'link' => [
                'label' => 'Book an appointment',
            ],
        ],
    ],

    'modal' => [
        'detail' => [
            'title' => [
                'label' => 'Report details',
            ],
            'cta' => [
                'label' => 'Close',
            ],
            'image' => [
                'alt' => 'Report image',
            ],
            'fields' => [
                'title' => [
                    'label' => 'Title',
                ],
                'type' => [
                    'label' => 'Type',
                ],
                'address' => [
                    'label' => 'Address',
                ],
                'detail' => [
                    'label' => 'Details',
                ],
                'images' => [
                    'label' => 'Images',
                ],
            ],
        ],
    ],
];
