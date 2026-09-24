<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from lang.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/de/lang_part07.php
return array (
  'user_delete_successfully' => 'Benutzer wurde erfolgreich gelöscht',
  'agent_delete_successfully' => 'Agent wurde erfolgreich gelöscht',
  'select_another_agent' => 'Wählen Sie einen anderen Agenten aus',
  'agent_delete_successfully_and_ticket_assign_to_another_agent' => 'Der Agent wurde erfolgreich gelöscht und das Ticket wurde einem anderen Agenten zugewiesen',
  'deleted_user' => 'Gelöschter Benutzer',
  'deleted_user_directory' => 'Gelöschtes Benutzerverzeichnis',
  'restore' => 'Wiederherstellen',
  'user_restore_successfully' => 'Benutzerwiederherstellung erfolgreich',
  'apply' => 'anwenden',
  'sort-by' => 'Sortiere nach',
  'created-at' => 'Erstellt am',
  'or' => 'oder',
  'activate' => 'Aktivieren',
  'system-email-not-configured' => 'Wir können die E-Mail-Anfrage nicht verarbeiten, da das System keine konfigurierte E-Mail zum Senden von E-Mails hat. Bitte kontaktieren Sie den Systemadministrator und benachrichtigen Sie ihn.',
  'assign-ticket' => 'Tickets zuweisen',
  'can-not-inactive-group' => 'Die Gruppe kann nicht inaktiviert werden, da ihr Agenten zugewiesen sind. Weisen Sie diese Agenten einer anderen Gruppe zu und versuchen Sie es erneut.',
  'internal-note-has-been-added' => 'Interne Notiz wurde dem Ticket hinzugefügt',
  'active-users' => 'Aktive Benutzer',
  'deleted-users' => 'Gelöschte Benutzer',
  'view-option' => 'Optionen anzeigen',
  'accoutn-not-verified' => 'Benutzerkonto wurde nicht überprüft',
  'enabled' => 'Aktiviert',
  'disabled' => 'Deaktiviert',
  'user-account-is-deleted' => 'Dieses Benutzerkonto wurde gelöscht.',
  'restore-user' => 'Benutzerkonto wiederherstellen',
  'delete-account-caution-info' => 'Bitte beachten Sie, dass dieses Konto möglicherweise noch offene Tickets im System hat.',
  'reply-can-not-be-empty' => 'Antwort kann nicht leer sein. Bitte geben Sie Ihre Antwort ein.',
  'account-created-contact-admin-as-we-were-not-able-to-send-opt' => 'Ihr Konto wurde erfolgreich erstellt. Bitte kontaktieren Sie den Administrator für die Kontoaktivierung, da wir Ihnen keinen OPT-Code senden konnten.',
  'only-agents' => 'Agentenbenutzer',
  'only-users' => 'Kunden Benutzer',
  'banned-users' => 'Gesperrte Benutzer',
  'inactive-users' => 'Inaktiver Benutzer',
  'all-users' => 'Alle Nutzer',
  'selected-user-is-already-the-owner' => 'Der ausgewählte Benutzer ist bereits Inhaber dieses Tickets.',
  'session-expired' => 'Die Sitzung ist abgelaufen oder ungültig. Bitte versuchen Sie es erneut.',
);
