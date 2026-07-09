<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from lang.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/fr/lang_part10.php
return array (
  'agent_delete_successfully_and_ticket_assign_to_another_agent' => 'Agent supprimé avec succès et les tickets ont été assignés a un autre agent',
  'deleted_user' => 'Utilisateurs supprimés',
  'deleted_user_directory' => 'Dossier d\'utilisateurs supprimés',
  'restore' => 'Restaurer',
  'user_restore_successfully' => 'Utilisateur restauré avec succès',
  'apply' => 'Appliquer',
  'sort-by' => 'Trier par',
  'created-at' => 'Créé a',
  'or' => 'ou',
  'activate' => 'Activer',
  'system-email-not-configured' => 'Nous ne pouvons pas traiter la demande d\'email car le système n\'a pas de email configuré pour l\'envoi de courrier. Veuillez contacter et signaler a l\'administrateur système.',
  'assign-ticket' => 'Tickets assignés',
  'can-not-inactive-group' => 'Impossible de rendre le groupe inactif car il contient des agents. Veuillez attribuer ces agents à un autre groupe et réessayer.',
  'internal-note-has-been-added' => 'Note interne ajoutée au ticket',
  'active-users' => 'Utilisateurs actifs',
  'deleted-users' => 'Utilisateurs supprimés',
  'view-option' => 'Voir les options',
  'accoutn-not-verified' => 'Le compote utilisateur n\'est pas vérifié',
  'enabled' => 'Activé',
  'user-account-is-deleted' => 'Ce compte utilisateur a été supprimé.',
  'restore-user' => 'Restaurer le compte utilisateur',
  'delete-account-caution-info' => 'Veuillez noter que ce compte peut toujours contenir des billets ouverts dans le système.',
  'reply-can-not-be-empty' => 'La réponse ne peut pas être vide. Veuillez entrer votre réponse.',
  'account-created-contact-admin-as-we-were-not-able-to-send-opt' => 'Votre compte a été créé avec succès. Veuillez contacter admin pour l\'activation du compte car nous n\'avons pas pu vous envoyer un code OPT.',
  'only-agents' => 'Agent users',
  'only-users' => 'Clients users',
  'banned-users' => 'Utilisateurs bannis',
  'inactive-users' => 'Utilisateurs inactifs',
  'all-users' => 'Tous les utilisateurs',
  'selected-user-is-already-the-owner' => 'L\'utilisateur sélectionné est déjà le propriétaire du ticket.',
  'session-expired' => 'Session expired or invalid, please try again.',
);
