<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_warning.php
return array (
  'warning' => 
  array (
    'title' => 
    array (
      'label' => 'Warning',
    ),
    'message' => 
    array (
      'label' => 'Fill in all required fields',
    ),
    'message_extra' => 
    array (
      'label' => 'Fields with an asterisk are required',
    ),
    'summary_declaration' => 
    array (
      'text' => 'The information you have provided is a statement. Verify that it is correct.',
    ),
  ),
);
