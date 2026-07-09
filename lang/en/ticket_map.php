<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_map.php
return array (
  'map' => 
  array (
    'image' => 
    array (
      'alt' => 'Reports map',
    ),
    'cta' => 
    array (
      'title' => 
      array (
        'label' => 'Have you noticed a service issue?',
      ),
      'text' => 
      array (
        'label' => 'Send a new report and help the municipality respond faster.',
      ),
      'button' => 
      array (
        'label' => 'Report an issue',
      ),
    ),
  ),
);
