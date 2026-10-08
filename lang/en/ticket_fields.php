<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_fields.php
return [
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    // Fixcity — translation section (claude-audit doc ratio).
    'fields' => [
        'code' => [
            'label' => 'Report code',
        ],
        'status' => [
            'label' => 'Status',
        ],
        'responsible_id' => [
            'label' => 'Assigned operator',
        ],
        'title' => [
            'label' => 'Title*',
            'description' => 'Report title',
        ],
        'type' => [
            'label' => 'Type of issue*',
            'description' => 'Select the type of issue',
        ],
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
];
