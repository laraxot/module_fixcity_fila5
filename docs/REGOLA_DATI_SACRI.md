---
title: "Regola Sacra — Dati non si toccano mai"
status: active
decision: D35
tags: [bmad, second-brain, dati-sacri, migrate, tests]
---

# I dati sono sacri — regola inviolabile

## Cosa NON si fa mai
- `php artisan migrate:fresh` — mai, anche in dev
- `php artisan migrate --force` — mai
- `php artisan migrate:refresh` — mai
- Nei test: `RefreshDatabase`, `RefreshDatabase` trait, `LazilyRefreshDatabase` se cancella dati esistenti
- `DatabaseTruncation` se tocca dati reali

## Perché
Il database `fixcity_data` contiene dati reali (segnalazioni, utenti, commenti, ticket). Un reset cancellerebbe evidenze civiche irreversibili.

## Cosa si fa invece
- Se serve schema nuovo: migrazione `up()` che aggiunge colonne/tabelle senza cancellare
- Nei test: usare `DatabaseMigrations` (solo se serve e con dati isolati) o mock/factories; mai `RefreshDatabase`
- Se un test ha bisogno di stato pulito: creare transaction rollback (`DatabaseTransactions`) o usare database separato / in-memory SQLite per test unitari

## Documentazione in memoria
- `docs/bmad/prevenzione-migrazioni-convenzioni.story.md` (D34) — convenzione migrazione
- `docs/second-brain/KNOWLEDGE.md` — aggiornata con regola D35
- Questo file è la fonte primaria; deve essere letto prima di ogni operazione DB

## Verifica preventiva
```bash
# Prima di eseguire qualsiasi comando DB
# Controlla che non ci sia --force / --fresh
grep -rni 'migrate:fresh\|migrate --force\|RefreshDatabase' Modules/*/tests/ app/Tests/ 2>/dev/null | grep -v 'docs/' | grep -v 'LazilyRefreshDatabase' | head
```

## Allineamento DB-codice (D36)
Prima di usare `filament-user` o comandi che inseriscono nel DB, verificare
**connessione + schema** del model auth, non solo il tipo colonna sulla
connessione di default. Per Fixcity: `User` eredita la connessione `user`
(`fixcity_user.users.id` = `varchar(36)` con `HasUuids`). Non “riparare”
alterando `fixcity.users` legacy. Dossier:
[bmad/filament-user-uuid-connection-audit.md](bmad/filament-user-uuid-connection-audit.md).
