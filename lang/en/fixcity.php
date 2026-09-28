<?php

declare(strict_types=1);

// English counterpart of lang/it/fixcity.php for the public ticket wizard.
return [
    'ticket' => [
        'title' => [
            'label' => 'Title',
            'placeholder' => 'Title',
            'help' => 'Enter a descriptive title',
        ],
        'type' => [
            'label' => 'Issue type',
            'placeholder' => 'Issue type',
        ],
        'content' => [
            'label' => 'Details',
            'placeholder' => 'Describe the issue',
            'helper_text' => 'Enter up to 200 characters',
        ],
        'your-location' => 'Your location',
        'insert-images' => 'Images',
        'priorities' => [
            'label' => 'Priority',
        ],
        'steps' => [
            'auth' => [
                'label' => 'Privacy and terms',
                'description' => 'Read and accept the terms to continue',
            ],
            'data' => [
                'label' => 'Report details',
                'description' => 'Enter the details of your report',
            ],
            'summary' => [
                'label' => 'Summary',
                'description' => 'Review your report before submitting',
            ],
        ],
        'fields' => [
            'accept_terms' => [
                'label' => 'I have read and understood the privacy policy',
                'helper' => 'You must accept the privacy policy to continue',
            ],
            'privacy_notice' => [
                'content' => 'The municipality must publish the privacy notice for this service before personal data can be collected.',
            ],
            'issue' => [
                'label' => 'Issue*',
            ],
        ],
        'actions' => [
            'next' => [
                'label' => 'Next',
                'icon' => 'heroicon-m-arrow-right',
                'color' => 'primary',
            ],
            'save' => [
                'label' => 'Submit report',
                'icon' => 'heroicon-m-check',
                'color' => 'success',
            ],
        ],
        'validation' => [
            'accept_terms' => 'You must accept the privacy policy to continue.',
        ],
    ],
];
