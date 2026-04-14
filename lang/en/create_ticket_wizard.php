<?php

declare(strict_types=1);

use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;

/**
 * Translations for {@see CreateTicketWizardWidget}.
 *
 * Chiave: fixcity::create_ticket_wizard.*
 */
return [
    'steps' => [
        '1' => [
            'label' => 'Authorisations and conditions',
        ],
        '2' => [
            'label' => 'Report details',
        ],
        '3' => [
            'label' => 'Summary',
        ],
    ],
    'actions' => [
        'previous' => [
            'label' => 'Previous',
            'tooltip' => 'Go back to previous step',
            'icon' => '',
        ],
        'next' => [
            'label' => 'Next',
            'tooltip' => 'Continue to next step',
            'icon' => '',
        ],
        'submit' => [
            'label' => 'Submit',
            'tooltip' => 'Submit the report to the Municipality',
            'icon' => '',
        ],
    ],
    'fields' => [
        'place' => [
            'label' => 'Location',
        ],
        'inefficiency' => [
            'label' => 'Issue',
        ],
        'author' => [
            'label' => 'Report author',
            'description' => 'Information about you',
        ],
        'type' => [
            'label' => 'Issue type',
        ],
        'name' => [
            'label' => 'Title',
        ],
        'content' => [
            'label' => 'Details',
            'helper_text' => 'Maximum 200 characters',
        ],
        'author_name' => [
            'label' => 'Full name',
        ],
        'author_fiscal_code' => [
            'label' => 'Tax code',
        ],
        'author_phone' => [
            'label' => 'Phone',
        ],
        'privacyAccepted' => [
            'label' => 'I have read and understood the privacy notice',
            'validation_attribute' => 'the privacy notice',
            'description' => 'Privacy acceptance',
            'helper_text' => 'Read the privacy policy',
        ],
        'address' => [
            'label' => 'Location',
            'description' => 'Address',
            'helper_text' => 'Search for address',
            'placeholder' => 'Search address',
        ],
        'issueType' => [
            'label' => 'Issue type',
            'description' => 'Category of the problem',
            'helper_text' => 'Select the category',
            'placeholder' => 'Select type',
        ],
        'title' => [
            'label' => 'Title',
            'description' => 'Title of the report',
            'helper_text' => 'Max 255 characters',
            'placeholder' => 'Brief title',
        ],
        'details' => [
            'label' => 'Description',
            'description' => 'Description of the problem',
            'helper_text' => 'Max 200 characters',
            'placeholder' => 'Describe the problem...',
        ],
        'email' => [
            'label' => 'Email',
            'description' => 'Email address',
            'helper_text' => 'Optional',
            'placeholder' => 'Enter email',
        ],
        'images' => [
            'label' => 'Images',
            'description' => 'Images of the problem',
            'helper_text' => 'You can upload up to 10 images',
            'placeholder' => 'Upload images',
        ],
        'userName' => [
            'label' => 'Full name',
            'description' => 'Name and surname',
            'helper_text' => 'Optional',
            'placeholder' => 'Enter name',
        ],
        'userFiscalCode' => [
            'label' => 'Tax ID',
            'description' => 'Tax identification code',
            'helper_text' => 'Optional',
            'placeholder' => 'Enter tax ID',
        ],
        'userPhone' => [
            'label' => 'Phone',
            'description' => 'Phone number',
            'helper_text' => 'Optional',
            'placeholder' => 'Enter phone',
        ],
        'summary_notice' => [
            'label' => '',
            'placeholder' => '',
            'helper_text' => '',
            'description' => '',
        ],
    ],
    'summary' => [
        'empty' => 'Not provided',
        'section' => [
            'label' => 'Report summary',
            'description' => 'Review data before submission',
        ],
        'images' => [
            'section_label' => 'Attached images',
            'count_description' => ':count images uploaded',
            'empty_description' => 'No images uploaded',
            'limit_message' => 'And :count more images',
        ],
    ],
    'notifications' => [
        'submit_failed' => [
            'title' => 'Submission failed',
            'body' => 'The report could not be saved. Check the entered data and try again.',
        ],
    ],
];
