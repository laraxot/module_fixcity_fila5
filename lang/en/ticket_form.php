<?php

declare(strict_types=1);

return [
    'summaries' => [
        'images_none' => 'No images attached',
        'images_choice' => '{1} One image attached|[2,*] :count images attached',
    ],
    'fields' => [
        'location' => [
            'label' => 'Location',
            'invalid' => 'Choose the point on the map or search for an address: we need it to know where the problem is.',
            'placeholder' => 'Search for a place',
            'helper_text' => 'Indicate where the issue is located',
            'address' => [
                'label' => 'Location',
                'placeholder' => 'Search for a place',
                'helper_text' => 'Search for the address or select the point on the map',
                'description' => 'Address of the issue',
            ],
            'description' => 'location',
        ],
        'type' => [
            'label' => 'Issue type',
            'placeholder' => 'Select the issue type',
            'helper_text' => 'Select the category of the issue',
            'description' => 'Issue category',
        ],
        'content' => [
            'label' => 'Details',
            'placeholder' => 'Describe the issue',
            'helper_text' => 'Enter no more than 200 characters',
            'description' => 'Describe the issue clearly',
        ],
        'images' => [
            'upload_button' => 'Upload file',
            'upload_aria' => 'Upload files for the issue',
            'remove' => 'Remove :name',
            'uploading' => 'Uploading',
            'error_type' => 'File type not allowed: use JPG, PNG or PDF',
            'error_size' => 'The file is larger than :max MB',
            'error_count' => 'You can attach at most :max files',
            'error_upload' => 'Upload failed, please try again',
            'label' => 'Images',
            'helper_text' => 'Accepted formats: :formats. Up to :max_files files, :max_size MB each.',
            'placeholder' => 'Add images (optional)',
            'description' => 'Images supporting the report',
        ],
        'author' => [
            'show_more' => 'Show all',
        ],
        'author_fiscal_code' => [
            'label' => 'Tax code',
            'description' => 'Tax code of the author',
            'helper_text' => 'Information linked to your profile',
            'placeholder' => 'Tax code',
        ],
        'title' => [
            'label' => 'Title',
        ],
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Enter the name',
            'helper_text' => 'Enter an identifying name',
            'description' => 'Identifying name of the report',
        ],
        'privacy' => [
            'description' => 'Acceptance of the privacy notice',
            'label' => 'Authorisations and conditions',
            'helper_text' => 'You must accept the privacy policy to proceed',
        ],
        'privacyAccepted' => [
            'label' => 'I have read and understood the privacy policy',
            'placeholder' => '',
            'helper_text' => 'Required to submit the report',
            'description' => 'Allineato a Design Comuni segnalazione-01-privacy',
        ],
        'email' => [
            'label' => 'Email',
            'placeholder' => 'Enter your email',
            'helper_text' => 'Your email so we can contact you',
            'description' => 'Email address of the person making the report',
        ],
        'review_images' => [
            'label' => 'Attached images',
            'description' => 'Upload summary',
        ],
        'review_location' => [
            'label' => 'Location',
            'description' => 'Selected place or coordinates',
        ],
        'review_email' => [
            'label' => 'Contact email',
            'description' => 'Preview of the email address provided',
        ],
        'review_content' => [
            'label' => 'Details',
            'description' => 'Description of the issue',
        ],
        'review_name' => [
            'label' => 'Title / summary name',
            'description' => 'Short title provided by the user',
        ],
        'review_type' => [
            'label' => 'Report type',
            'description' => 'Chosen category',
        ],
        'review_priority' => [
            'label' => 'Priority',
            'description' => 'Selected urgency',
        ],
        'review_author_name' => [
            'label' => 'Full name',
            'description' => 'Author recap',
        ],
        'review_author_fiscal_code' => [
            'label' => 'Tax ID',
            'description' => 'Author fiscal code recap',
        ],
        'review_contact_phone' => [
            'label' => 'Phone',
            'description' => 'Contact phone recap',
        ],
        'review_contact_email' => [
            'label' => 'Contact email',
            'description' => 'Contact email recap',
        ],
        'author_phone' => [
            'label' => 'author_phone',
            'description' => 'author_phone',
            'helper_text' => 'author_phone',
            'placeholder' => 'author_phone',
        ],
        'author_name' => [
            'label' => 'author_name',
            'description' => 'author_name',
            'helper_text' => 'author_name',
            'placeholder' => 'author_name',
        ],
        'author_email' => [
            'label' => 'author_email',
            'description' => 'author_email',
            'helper_text' => 'author_email',
            'placeholder' => 'author_email',
        ],
        'priority' => [
            'description' => 'priority',
            'helper_text' => '',
            'label' => 'priority',
            'placeholder' => 'priority',
        ],
        'slug' => [
            'label' => 'slug',
            'placeholder' => 'slug',
            'helper_text' => '',
            'description' => 'slug',
        ],
        'type_legacy' => [
            'label' => 'type',
            'placeholder' => 'type',
            'helper_text' => '',
            'description' => 'type',
        ],
        'type_id' => [
            'description' => 'type_id',
            'helper_text' => '',
            'label' => 'type_id',
            'placeholder' => 'type_id',
        ],
        'gdpr_text' => [
            'label' => 'Privacy notice',
        ],
        'status' => [
            'label' => 'status',
            'placeholder' => 'status',
            'helper_text' => 'status',
            'description' => 'status',
        ],
    ],
    'required_notice' => 'Fields marked with an asterisk are required',
    'navscroll' => [
        'title' => 'REQUIRED INFORMATION',
    ],
    'nav' => [
        'aria_label' => 'Step navigation',
        'back' => 'Back',
        'next' => 'Next',
    ],
    'hints' => [
        'max_length' => 'Enter at most :max characters',
    ],
    'validation' => [
        'required' => 'The :attribute field is required.',
        'min' => 'The :attribute field must be at least :min characters.',
        'max' => 'The :attribute field must not be greater than :max characters.',
    ],
    'sections' => [
        'Riepilogo' => [
            'heading' => 'Summary',
            'label' => 'Summary',
        ],
        'Autore della segnalazione' => [
            'heading' => 'Report author',
            'label' => 'Report author',
        ],
        'Luogo' => [
            'label' => 'Location',
            'heading' => 'Location',
        ],
        'Disservizio' => [
            'label' => 'Issue',
            'heading' => 'Issue',
        ],
        'empty' => [
            'heading' => '',
            'label' => '',
        ],
        'Contatti' => [
            'heading' => 'Contacts',
        ],
        'location' => [
            'heading' => 'Location',
            'description' => 'Indicate where the issue is located',
        ],
        'issue' => [
            'heading' => 'Issue',
            'description' => 'Describe the reported issue',
        ],
        'author' => [
            'heading' => 'Report author',
            'description' => 'Information about you',
        ],
        'Location' => [
            'label' => 'Location',
            'heading' => 'Location',
        ],
        'Issue' => [
            'label' => 'Issue',
            'heading' => 'Issue',
        ],
        'Report author' => [
            'label' => 'Report author',
            'heading' => 'Report author',
        ],
    ],
    'steps' => [
        'privacy' => [
            'label' => 'Authorisations and conditions',
            'tooltip' => 'Acceptance of the privacy notice',
            'description' => 'Read and accept the privacy policy',
            'color' => 'success',
            'icon' => 'heroicon-o-shield-check',
        ],
        'dati-di-segnalazione' => [
            'label' => 'Report details',
            'tooltip' => 'Fill in the report details',
            'description' => 'Enter the details of the issue',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'riepilogo' => [
            'label' => 'Summary',
            'tooltip' => 'Final summary of the report',
            'description' => 'Check the details before submitting',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'summary' => [
            'label' => 'Summary',
            'tooltip' => 'Final summary of the report',
            'description' => 'Check the details before submitting',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'data' => [
            'label' => 'Report details',
            'tooltip' => 'Fill in the report details',
            'description' => 'Enter the details of the issue',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'Riepilogo' => [
            'label' => 'Summary',
        ],
        'Dati di segnalazione' => [
            'label' => 'Report details',
        ],
        'Autorizzazioni e condizioni' => [
            'label' => 'Authorisations and conditions',
        ],
    ],
];
