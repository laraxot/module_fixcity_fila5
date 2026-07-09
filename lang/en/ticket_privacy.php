<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_privacy.php
return array (
  'privacy' => 
  array (
    'title' => 
    array (
      'label' => 'Service Report',
    ),
    'description' => 
    array (
      'text' => 'Read the privacy policy and consent to the processing of personal data.',
    ),
    'details' => 
    array (
      'text' => 'For details on the processing of personal data, see the ',
      'link' => 
      array (
        'label' => 'privacy policy.',
      ),
    ),
    'accept' => 
    array (
      'label' => 'I have read and understood the privacy policy',
    ),
    'intro' => 
    array (
      'text' => 'The Municipality of Florence manages the personal data provided and freely communicated on the basis of Article 13 of Regulation (EU) 2016/679 General data protection regulation (Gdpr) and Articles 13 and subsequent amendments and additions of Legislative Decree (hereinafter Legislative Decree) 267/2000 (Consolidated Law on Local Authorities).',
    ),
    'detail_prefix' => 
    array (
      'text' => 'For details on the processing of personal data, see the ',
    ),
    'link' => 
    array (
      'label' => 'privacy policy.',
    ),
    'checkbox' => 
    array (
      'label' => 'I have read and understood the privacy policy',
    ),
    'error' => 
    array (
      'not_accepted' => 'You must accept the privacy policy to continue.',
    ),
  ),
);
