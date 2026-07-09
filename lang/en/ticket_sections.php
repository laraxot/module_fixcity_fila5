<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_sections.php
return array (
  'sections' => 
  array (
    'place' => 
    array (
      'label' => 'Location',
      'description' => 'Indicate the location of the issue',
    ),
    'inefficiency' => 
    array (
      'label' => 'Issue',
      'description' => '',
    ),
    'author' => 
    array (
      'label' => 'Report Author',
      'description' => 'Information about you',
    ),
    'summary' => 
    array (
      'label' => 'Summary',
      'description' => '',
    ),
    'contacts' => 
    array (
      'label' => 'Contacts',
      'edit_action' => 'Edit',
    ),
  ),
);
