<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from lang.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/lang_part11.php
return array (
  'going-overdue-today' => 'This ticket will become overdue today.',
  'ticket_has_attachments' => 'This ticket contains attachment(s)',
  'is_overdue' => 'This ticket is marked as overdue',
  'ticket_has_collaborator' => 'This ticket has collaborator(s)',
  'ticket_created_source' => 'This ticket is created via :source',
  'ticket-has-x-priority' => 'This ticket has :priority priority',
  'clean-forever' => 'delete permanently',
  'mail-sent-to-job-for-process' => 'Mail has been sent to job for process, it will appear in your mailbox once it gets processed by your selected queue service. If you don\'t recieve the mail check logs for errors or warnings.',
  'click-here-to-see-more-details' => 'Click here to see more details',
);
