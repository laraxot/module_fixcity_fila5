<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_wizard_a11y.php
return array (
  'wizard_a11y' => 
  array (
    'skip_to_main' => 
    array (
      'label' => 'Skip to main content',
      'placeholder' => '',
      'help' => 'Skip navigation and go directly to the report form.',
    ),
    'main_region' => 
    array (
      'label' => 'Report form',
      'placeholder' => '',
      'help' => 'Wizard steps and submission form.',
    ),
  ),
);
