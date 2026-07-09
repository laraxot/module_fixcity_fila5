<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_fields.php
return array (
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
  'fields' => 
  array (
    'title' => 
    array (
      'label' => 'Title*',
      'description' => 'Report title',
    ),
    'type' => 
    array (
      'label' => 'Type of issue*',
      'description' => 'Select the type of issue',
    ),
    'type_id' => 
    array (
      'label' => 'Type of issue*',
      'description' => 'Select the type of issue',
    ),
    'map_reference' => 
    array (
      'label' => 'Map (MapPicker — comparison)',
      'description' => 'Same Lit component as LocationPicker; coordinates are not saved on the report.',
    ),
    'location' => 
    array (
      'label' => 'Map (LocationPicker)',
      'description' => 'Pick the point on the map; coordinates are persisted on the report.',
    ),
    'details' => 
    array (
      'label' => 'Details**',
      'char_limit' => 'Enter a maximum of 200 characters',
      'max_chars' => 
      array (
        'label' => 'Enter a maximum of 200 characters',
      ),
    ),
    'address' => 
    array (
      'label' => 'Location*',
      'placeholder' => 'Search for a location*',
    ),
    'use_my_location' => 
    array (
      'label' => 'Use your location',
    ),
    'images' => 
    array (
      'label' => 'Images',
      'upload_label' => 'Upload files',
      'upload_button' => 'Upload files',
      'upload_aria' => 
      array (
        'label' => 'Upload files for the service issue',
      ),
      'description' => 'Select one or more images to attach to the report',
      'help_text' => 'Select one or more images to attach to the report',
      'delete_aria' => 
      array (
        'label' => 'delete uploaded image',
      ),
    ),
    'name' => 
    array (
      'label' => 'Full name',
      'placeholder' => 'Full name',
    ),
    'fiscal_code' => 
    array (
      'label' => 'Tax Code',
      'placeholder' => 'Tax code',
    ),
    'phone' => 
    array (
      'label' => 'Phone',
      'placeholder' => '+39 xxx xxxxxxx',
    ),
    'email' => 
    array (
      'label' => 'Email',
    ),
    'priority' => 
    array (
      'label' => 'Priority*',
      'description' => 'Indicate the priority of the report',
    ),
    'content' => 
    array (
      'label' => 'Content*',
      'description' => 'Describe the service issue in detail',
      'char_limit' => 'Enter a maximum of 500 characters',
      'max_chars' => 
      array (
        'label' => 'Enter a maximum of 500 characters',
      ),
    ),
    'summary' => 
    array (
      'warning' => 'Warning',
      'declaration' => 'The information you have provided is a statement. Verify that it is correct.',
      'segnalazione_section' => 'Service Issue Report',
      'dati_generali_section' => 'General Data',
      'author' => 'Report Author',
      'cf' => 'Tax Code',
      'contacts' => 'Contacts',
      'phone' => 'Phone',
      'email' => 'Email',
    ),
    'required_note' => 
    array (
      'label' => 'Fields marked with an asterisk are required',
    ),
    'required' => 
    array (
      'note' => 
      array (
        'label' => 'Fields marked with an asterisk are required',
      ),
    ),
    'sidebar_title' => 
    array (
      'label' => 'REQUIRED INFORMATION',
    ),
    'sidebar_hint' => 
    array (
      'label' => 'Fill in the form in the main column to continue.',
      'placeholder' => '',
      'help' => 'Use the Next and Back buttons below the form; the wizard is not duplicated in the sidebar.',
    ),
    'step' => 
    array (
      'privacy' => 
      array (
        'label' => 'Privacy Policy',
      ),
      'data' => 
      array (
        'label' => 'Report Details',
      ),
      'summary' => 
      array (
        'label' => 'Summary',
      ),
      'back' => 
      array (
        'label' => 'Back',
      ),
      'next' => 
      array (
        'label' => 'Next',
      ),
      'save' => 
      array (
        'label' => 'Save',
      ),
      'save_request' => 
      array (
        'label' => 'Save Report',
      ),
      'confirm' => 
      array (
        'label' => 'Confirm and submit',
      ),
      'confirmed' => 
      array (
        'label' => 'Confirmed',
      ),
      'active' => 
      array (
        'label' => 'Active',
      ),
      'saved_success' => 
      array (
        'message' => 'Report saved successfully',
      ),
    ),
    'place' => 
    array (
      'section' => 
      array (
        'label' => 'Location',
      ),
      'help' => 
      array (
        'label' => 'Indicate the location of the service issue',
      ),
      'search' => 
      array (
        'label' => 'Search for a location*',
      ),
    ),
    'inefficiency' => 
    array (
      'section' => 
      array (
        'label' => 'Service Issue',
      ),
      'type' => 
      array (
        'label' => 'Type of service issue**',
      ),
      'options' => 
      array (
        'property_damage' => 
        array (
          'label' => 'Public property damage',
        ),
      ),
    ),
    'author' => 
    array (
      'section' => 
      array (
        'label' => 'Report Author',
      ),
      'about' => 
      array (
        'label' => 'Information about you',
      ),
      'cf' => 
      array (
        'label' => 'Tax Code',
      ),
      'show_all' => 
      array (
        'label' => 'Show all',
      ),
      'contacts' => 
      array (
        'label' => 'Contacts',
      ),
      'edit' => 
      array (
        'label' => 'Edit',
      ),
    ),
  ),
);
