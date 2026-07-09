<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_card.php
return array (
  'card' => 
  array (
    'type' => 
    array (
      'label' => 'Report type',
      'short' => 'Report',
    ),
    'expand' => 
    array (
      'button' => 
      array (
        'label' => 'Show details',
      ),
    ),
    'address' => 
    array (
      'label' => 'Address',
    ),
    'detail' => 
    array (
      'label' => 'Details',
    ),
    'edit' => 
    array (
      'link' => 
      array (
        'label' => 'Edit',
      ),
    ),
  ),
);
