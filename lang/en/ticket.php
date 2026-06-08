<?php

declare(strict_types=1);

/**
 * Segnalazione translations - English (COMPLETE)
 *
 * Translation namespace: fixcity::segnalazione.*
 * Format: namespace::context.collection.element.type (5 levels)
 *
 * Covers both test pages (segnalazione-02-dati.blade.php)
 * and the Filament ticket-create-wizard widget.
 */
return [
    'breadcrumb' => [
        'home' => [
            'label' => 'Home',
        ],
        'services' => [
            'label' => 'Services',
        ],
        'elenco' => [
            'label' => 'Reports',
        ],
    ],

    'inefficiency_types' => [
        'property_damage' => [
            'label' => 'Public property damage',
        ],
    ],

    /*
     * Page-level keys
     */
    'page' => [
        'title' => [
            'label' => 'Service Report',
        ],
    ],

    'wizard_a11y' => [
        'skip_to_main' => [
            'label' => 'Skip to main content',
            'placeholder' => '',
            'help' => 'Skip navigation and go directly to the report form.',
        ],
        'main_region' => [
            'label' => 'Report form',
            'placeholder' => '',
            'help' => 'Wizard steps and submission form.',
        ],
    ],

    /*
     * Heading keys
     */
    'heading' => [
        'report' => [
            'label' => 'Service Report',
        ],
        'report_author' => [
            'label' => 'Report Author',
            'description' => 'Information about you',
        ],
        'contacts' => [
            'label' => 'Contacts',
        ],
        'title' => [
            'label' => 'Reports',
        ],
        'subtitle' => [
            'text' => 'Browse local reports on the map or filter the list by category.',
        ],
    ],

    /*
     * Segnalazioni elenco page keys
     */
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
            'label' => 'Map and list view of reports',
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
                'label' => 'Have you noticed a service issue?',
            ],
            'text' => [
                'label' => 'Send a new report and help the municipality respond faster.',
            ],
            'button' => [
                'label' => 'Report an issue',
            ],
        ],
    ],

    'card' => [
        'type' => [
            'label' => 'Report type',
            'short' => 'Report',
        ],
        'expand' => [
            'button' => [
                'label' => 'Show details',
            ],
        ],
        'address' => [
            'label' => 'Address',
        ],
        'detail' => [
            'label' => 'Details',
        ],
        'edit' => [
            'link' => [
                'label' => 'Edit',
            ],
        ],
    ],

    'load-more' => [
        'button' => [
            'label' => 'Load more reports',
        ],
    ],

    'contacts' => [
        'title' => [
            'label' => 'Need help?',
        ],
        'faq' => [
            'link' => [
                'label' => 'Read the frequently asked questions',
            ],
        ],
    ],

    /*
     * Fields keys - used by both test pages and widget
     */
    'fields' => [
        'title' => [
            'label' => 'Title*',
            'description' => 'Report title',
        ],
        'type' => [
            'label' => 'Type of issue*',
            'description' => 'Select the type of issue',
        ],
        /** Same semantics as `type`; Ticket model persists enum on `type_id`. */
        'type_id' => [
            'label' => 'Type of issue*',
            'description' => 'Select the type of issue',
        ],
        'map_reference' => [
            'label' => 'Map (MapPicker — comparison)',
            'description' => 'Same Lit component as LocationPicker; coordinates are not saved on the report.',
        ],
        'location' => [
            'label' => 'Map (LocationPicker)',
            'description' => 'Pick the point on the map; coordinates are persisted on the report.',
        ],
        'details' => [
            'label' => 'Details**',
            'char_limit' => 'Enter a maximum of 200 characters',
            'max_chars' => [
                'label' => 'Enter a maximum of 200 characters',
            ],
        ],
        'address' => [
            'label' => 'Location*',
            'placeholder' => 'Search for a location*',
        ],
        'use_my_location' => [
            'label' => 'Use your location',
        ],
        'images' => [
            'label' => 'Images',
            'upload_label' => 'Upload files',
            'upload_button' => 'Upload files',
            'upload_aria' => [
                'label' => 'Upload files for the service issue',
            ],
            'description' => 'Select one or more images to attach to the report',
            'help_text' => 'Select one or more images to attach to the report',
            'delete_aria' => [
                'label' => 'delete uploaded image',
            ],
        ],
        'name' => [
            'label' => 'Full name',
            'placeholder' => 'Full name',
        ],
        'fiscal_code' => [
            'label' => 'Tax Code',
            'placeholder' => 'Tax code',
        ],
        'phone' => [
            'label' => 'Phone',
            'placeholder' => '+39 xxx xxxxxxx',
        ],
        'email' => [
            'label' => 'Email',
        ],
        'priority' => [
            'label' => 'Priority*',
            'description' => 'Indicate the priority of the report',
        ],
        'content' => [
            'label' => 'Content*',
            'description' => 'Describe the service issue in detail',
            'char_limit' => 'Enter a maximum of 500 characters',
            'max_chars' => [
                'label' => 'Enter a maximum of 500 characters',
            ],
        ],
        'summary' => [
            'warning' => 'Warning',
            'declaration' => 'The information you have provided is a statement. Verify that it is correct.',
            'segnalazione_section' => 'Service Issue Report',
            'dati_generali_section' => 'General Data',
            'author' => 'Report Author',
            'cf' => 'Tax Code',
            'contacts' => 'Contacts',
            'phone' => 'Phone',
            'email' => 'Email',
        ],
        'required_note' => [
            'label' => 'Fields marked with an asterisk are required',
        ],
        'required' => [
            'note' => [
                'label' => 'Fields marked with an asterisk are required',
            ],
        ],
        'sidebar_title' => [
            'label' => 'REQUIRED INFORMATION',
        ],
        'sidebar_hint' => [
            'label' => 'Fill in the form in the main column to continue.',
            'placeholder' => '',
            'help' => 'Use the Next and Back buttons below the form; the wizard is not duplicated in the sidebar.',
        ],
        'step' => [
            'privacy' => [
                'label' => 'Privacy Policy',
            ],
            'data' => [
                'label' => 'Report Details',
            ],
            'summary' => [
                'label' => 'Summary',
            ],
            'back' => [
                'label' => 'Back',
            ],
            'next' => [
                'label' => 'Next',
            ],
            'save' => [
                'label' => 'Save',
            ],
            'save_request' => [
                'label' => 'Save Report',
            ],
            'confirm' => [
                'label' => 'Confirm and submit',
            ],
            'confirmed' => [
                'label' => 'Confirmed',
            ],
            'active' => [
                'label' => 'Active',
            ],
            'saved_success' => [
                'message' => 'Report saved successfully',
            ],
        ],
        'place' => [
            'section' => [
                'label' => 'Location',
            ],
            'help' => [
                'label' => 'Indicate the location of the service issue',
            ],
            'search' => [
                'label' => 'Search for a location*',
            ],
        ],
        'inefficiency' => [
            'section' => [
                'label' => 'Service Issue',
            ],
            'type' => [
                'label' => 'Type of service issue**',
            ],
            'options' => [
                'property_damage' => [
                    'label' => 'Public property damage',
                ],
            ],
        ],
        'author' => [
            'section' => [
                'label' => 'Report Author',
            ],
            'about' => [
                'label' => 'Information about you',
            ],
            'cf' => [
                'label' => 'Tax Code',
            ],
            'show_all' => [
                'label' => 'Show all',
            ],
            'contacts' => [
                'label' => 'Contacts',
            ],
            'edit' => [
                'label' => 'Edit',
            ],
        ],
    ],

    /*
     * Sections keys — parity with Design Comuni
     */
    'sections' => [
        'place' => [
            'label' => 'Location',
            'description' => 'Indicate the location of the issue',
        ],
        'inefficiency' => [
            'label' => 'Issue',
            'description' => '',
        ],
        'author' => [
            'label' => 'Report Author',
            'description' => 'Information about you',
        ],
        'summary' => [
            'label' => 'Summary',
            'description' => '',
        ],
        'contacts' => [
            'label' => 'Contacts',
            'edit_action' => 'Edit',
        ],
    ],

    /*
     * Actions keys - used by widget
     */
    'actions' => [
        'back' => [
            'label' => 'Back',
        ],
        'save' => [
            'label' => 'Save Report',
        ],
        'save_short' => [
            'label' => 'Save',
        ],
        'save_draft' => [
            'label' => 'Save draft',
        ],
        'next' => [
            'label' => 'Next',
        ],
        'show_all' => [
            'label' => 'Show all',
        ],
        'remove_file' => [
            'aria' => [
                'label' => 'Remove file',
            ],
        ],
        'remove_image' => [
            'aria' => [
                'label' => 'Remove image',
            ],
        ],
        'submit' => [
            'label' => 'Confirm and submit',
        ],
    ],

    /*
     * Steps keys - used by widget
     */
    'steps' => [
        'active' => [
            'label' => 'Active',
        ],
        'confirmed' => [
            'label' => 'Confirmed',
        ],
        'privacy' => [
            'label' => 'Privacy Policy',
        ],
        'data' => [
            'label' => 'Report Details',
        ],
        'summary' => [
            'label' => 'Summary',
        ],
    ],

    /*
    * GDPR Notice keys - used by wizard privacy step
    */
    'gdpr_notice' => [
        'text' => 'The Municipality of :municipality manages the personal data provided and freely communicated on the basis of Article 13 of Regulation (EU) 2016/679 General Data Protection Regulation (GDPR) and Articles 13 and subsequent amendments and additions of Legislative Decree (hereinafter d.lgs) 267/2000 (Consolidated Law on Local Authorities).',
        'privacy_link' => 'privacy policy.',
    ],

    /*
    * Privacy keys - used by widget
    */
    'privacy' => [
        'title' => [
            'label' => 'Service Report',
        ],
        'description' => [
            'text' => 'Read the privacy policy and consent to the processing of personal data.',
        ],
        'details' => [
            'text' => 'For details on the processing of personal data, see the ',
            'link' => [
                'label' => 'privacy policy.',
            ],
        ],
        'accept' => [
            'label' => 'I have read and understood the privacy policy',
        ],
        'intro' => [
            'text' => 'The Municipality of Florence manages the personal data provided and freely communicated on the basis of Article 13 of Regulation (EU) 2016/679 General data protection regulation (Gdpr) and Articles 13 and subsequent amendments and additions of Legislative Decree (hereinafter Legislative Decree) 267/2000 (Consolidated Law on Local Authorities).',
        ],
        'detail_prefix' => [
            'text' => 'For details on the processing of personal data, see the ',
        ],
        'link' => [
            'label' => 'privacy policy.',
        ],
        'checkbox' => [
            'label' => 'I have read and understood the privacy policy',
        ],
        'error' => [
            'not_accepted' => 'You must accept the privacy policy to continue.',
        ],
    ],

    /*
     * Geolocation keys - used by address-field component
     */
    'geolocation' => [
        'not_supported' => 'Geolocation is not supported by your browser.',
        'address_not_found' => 'Address not found. Try entering it manually.',
        'error' => 'Error retrieving location. Please try again.',
        'permission_denied' => 'Geolocation permission denied. Please allow location access in your browser settings.',
    ],

    /*
     * Contact keys - used by widget
     */
    'contact' => [
        'heading' => [
            'label' => 'Contact the municipality',
        ],
        'phone' => [
            'label' => 'Call the toll-free number :phone',
        ],
        'faq' => [
            'label' => 'Read frequently asked questions',
        ],
        'assistance' => [
            'label' => 'Request support',
        ],
        'appointment' => [
            'label' => 'Book an appointment',
        ],
    ],

    /*
     * Warning keys - used by widget
     */
    'warning' => [
        'title' => [
            'label' => 'Warning',
        ],
        'message' => [
            'label' => 'Fill in all required fields',
        ],
        'message_extra' => [
            'label' => 'Fields with an asterisk are required',
        ],
    ],

    /*
     * Create options keys - used by widget
     */
    'create_options' => [
        'public_damage' => [
            'label' => 'Public property damage',
        ],
        'maintenance' => [
            'label' => 'Road maintenance',
        ],
        'urban_decorum' => [
            'label' => 'Urban furniture',
        ],
    ],
];
