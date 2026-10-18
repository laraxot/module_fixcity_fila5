---
title: "Filament user creation — UUID connection audit"
type: bug-audit
module: Fixcity
status: fixed
created: 2026-10-18
updated: 2026-10-18
tags: [bmad, second-brain, filament, user, uuid, data-sacred]
qmd: "make filament user uuid fixcity connection users"
related:
  - ../project-context.md
  - ../second-brain.md
  - ./decision-log.md
---

# Causa e correzione

## Sintomo

`php artisan make:filament-user` selezionava `fixcity::admin` ma falliva con
`Data truncated for column 'id'`: il model `Modules\Fixcity\Models\User`
generava UUID, mentre `fixcity.users.id` era `int`.

## Causa radice

`Fixcity\Models\User` estende `Modules\User\Models\BaseUser`, che usa UUID e
la connessione `user`, ma sovrascriveva la connessione a `fixcity`. Il comando
usava quindi una tabella legacy vuota/incompatibile invece della tabella owner
`user.users` (`varchar(36)`).

## Correzione non distruttiva

Rimossa la sovrascrittura della connessione dal model Fixcity User. Il model
continua a fornire il comportamento Fixcity e `CanComment`, ma eredita la
connessione UUID del modulo User. La tabella legacy `fixcity.users` non è stata
modificata, svuotata o migrata: i dati sono sacri.

## Verifica

- `user.users.id` è `varchar(36)`.
- `fixcity.users.id` è `int` legacy.
- `fixcity.users` contiene 0 righe al momento dell'audit.
- l'utente `marco.sottana@gmail.com` esiste già su `user.users`.
- nessuna migrazione distruttiva è stata eseguita.

### Verifica di regressione — 2026-10-18

Il retry del comando ha confermato che il primo errore apparteneva al percorso
precedente: dopo la correzione, il tentativo raggiunge `fixcity_user.users` e
non `fixcity_data.users`. Con l'indirizzo già esistente il comando fallisce
correttamente per il vincolo univoco `users_email_unique`; non è stato creato o
modificato alcun dato. Il controllo statico del modulo conferma inoltre
`connection=user`, `keyType=string` e UUID non incrementale.

### Stato operativo (ri-verifica)

- `Modules\Fixcity\Models\User` → `connection=user`, DB `fixcity_user`,
  `id` varchar, non incrementale.
- `marco.sottana@gmail.com` esiste già su `user.users` (id
  `01a117c0-b8f0-73bb-8e81-1685acb426dd`, name Marco Sottana).
- Anti-pattern archiviato:
  `database/migrations/2026_10_18_170300_update_users_id_to_string_uuid.php.old`
  (alterava `fixcity.users` invece di correggere la connessione).
- Guardrail: `tests/Unit/MigrationConventionsTest.php`.

## Regola preventiva

Quando un model estende `BaseUser`, verificare sempre insieme:

1. `getConnectionName()` del model;
2. tipo della colonna `users.id` sulla stessa connessione;
3. `config('auth.providers.users.model')` dell'ambiente attivo;
4. compatibilità del panel Filament prima di creare utenti.
