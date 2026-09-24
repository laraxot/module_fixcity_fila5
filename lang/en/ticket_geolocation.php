<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_geolocation.php
return array (
  'geolocation' => 
  array (
    'not_supported' => 'Geolocation is not supported by your browser.',
    'address_not_found' => 'Address not found. Try entering it manually.',
    'error' => 'Error retrieving location. Please try again.',
    'permission_denied' => 'Geolocation permission denied. Please allow location access in your browser settings.',
  ),
);
