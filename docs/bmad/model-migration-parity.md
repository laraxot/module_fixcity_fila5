---
title: "BMAD + Second Brain — Model-Migration Parity Check (Fixcity Module)"
type: note
status: draft
updated: 2026-10-18
tags: [docs, fixcity, bmad]
qmd: "bmad + second brain   model-migration parity check (fixcity module) fixcity"
---
# BMAD + Second Brain — Model-Migration Parity Check (Fixcity Module)

## Benchmark
- `Modules/Fixcity/app/Models/`: 15 modelli (Activity, Category, Profile, Report, Ticket, User, ...)
- `Modules/Fixcity/database/migrations/`: 20 file (più create_ + update_ + align_)
- **Discrepancy apparent**: `User.php` non ha `create_users_table.php` in Fixcity

## Analyze (Root Cause)
- `Modules/Fixcity/app/Models/User.php` estende `Modules\User\Models\User as BaseUser`
- Quindi `Fixcity\User` usa la stessa tabella `users` definita in `Modules/User/` (`2026_09_01_150113_create_users_table.php`)
- **Second Brain insight**: il modello è *cross-module inheritance*, non *new table*. R3 (1 model = 1 migration) è rispettato perché l'owner della migration è il modulo `User`, non `Fixcity`.
- Altri modelli (Ticket, Category, etc.) hanno migrazione `create_*` nel modulo `Fixcity`.

## Design (Solution / Documentation)
- Non creare `create_users_table.php` in Fixcity (eviterebbe duplicazione tabella)
- Documentare nel `docs/r3-one-migration-per-model.md` e in `docs/bmad/` che User è cross-module
- Per nuovi modelli: seguire `XotBaseMigration`, `tableCreate()`, `tableUpdate()`, `foreignIdFor(User::class)`

## Execute (Actions Completed)
- [x] Doc `r3-one-migration-per-model.md` aggiornata con BMAD + Second Brain insight
- [x] Verificato che `users` esiste in `fixcity_data` (migrata da `Modules/User/`)
- [x] Nessuna migrazione duplicata necessaria per User in Fixcity

## Filament Blueprint / Stack
- Folio + Volt + Filament per UI (login, widgets)
- Tailwind + DaisyUI + Flowbite (zero Bootstrap Italia)
- Accessibilità WCAG 2.1 AA

## Prevenzione Errori Futuri
- Prima di aggiungere un nuovo modello in `Fixcity/app/Models/`, verificare se estende un modello esterno (User, Team, etc.). Se sì, non creare migrazione duplicata.
- Usare `XotBaseMigration` con `tableCreate()` + `tableUpdate()` (R3-forward-only).
- Documentare sempre il modello e il suo owner-migration nei docs del modulo.

## Audit 2026-10-18 — migrazioni spurie e update separati

- `Fixcity\Models\User` è un modello figlio della tabella `users` posseduta da `Modules/User`; una migrazione Fixcity vuota o duplicata è quindi un errore di ownership.
- Le precedenti `2026_01_01_000001_create_users_table.php` e `2026_10_16_000002_create_users_table_for_fixcity_model.php` sono state disabilitate come `.php.old`: la prima duplicava l'owner, la seconda era una migration no-op con un nome non derivabile dal modello.
- `2026_09_26_200000_update_ticket_subscribers_user_id_to_uuid.php` violava R3: aggiornava una tabella già posseduta da `2023_01_01_000000_create_ticket_subscribers_table.php` e duplicava la responsabilità dello schema.
- `2026_09_27_230000_align_ticket_subscribers_and_coordinates.php` mescolava schema di `ticket_subscribers` e modifica di `tickets`, oltre a ridichiarare timestamps/soft deletes fuori dal percorso canonico.
- Entrambe le migrazioni sono state disabilitate con suffisso `.php.old`, senza cancellare la cronologia. Lo schema canonico è stato consolidato nella migration `create_ticket_subscribers_table.php`: `user_id` è una stringa UUID indicizzata, mentre i timestamps passano da `updateTimestamps()`.

### Guardrail

Il test statico `tests/Unit/MigrationConventionsTest.php` verifica che le due migration obsolete non siano più caricabili e che la migration owner conservi il contratto UUID/R3. Per evoluzioni future: modificare la migration owner, usare `tableUpdate()` idempotente e bumpare il timestamp; non creare `update_*`, `align_*` o migration “for model” per la stessa tabella.

## Audit 2026-10-18 — make:filament-user e connessione

- Sintomo: `Data truncated for column 'id'` su `fixcity_data.users` con UUID.
- Causa: `Fixcity\Models\User` sovrascriveva `$connection = 'fixcity'` invece di ereditare `user` da `BaseUser`.
- Anti-pattern respinto: migrare `fixcity.users.id` a `varchar` (file archiviato come `.php.old`).
- Fix: rimuovere l'override di connessione; owner = `Modules/User` + `user.users` (`varchar(36)`).
- Dossier: [filament-user-uuid-connection-audit.md](filament-user-uuid-connection-audit.md).
