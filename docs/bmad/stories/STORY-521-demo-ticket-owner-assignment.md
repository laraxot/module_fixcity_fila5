---
id: STORY-521
title: "Assegnare i ticket demo al cittadino demo"
type: story
status: verified
module: Fixcity
priority: high
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, seeders, demo, ownership]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../README.md
  - ../gap-analysis.md
  - ../../../../database/seeders/DemoUsersSeeder.php
  - ../../../../database/seeders/TicketDatabaseSeeder.php
  - ../../../../tests/Feature/Database/FixcityDatabaseSeederTest.php
---

# Ticket demo — ownership coerente con l’attore cittadino

## Evidenza osservata

`TicketDatabaseSeeder` assegnava ogni ticket all’utente con ID minimo nel database.
Il proprietario effettivo cambiava in base ai dati preesistenti, poteva non essere
un attore demo e impediva di riprodurre in modo affidabile il percorso cittadino.
La prova tracking su SQLite isolato ha trovato `DEMO-001` di proprietà di un
account diverso dagli utenti demo.

## User story e risultato atteso

Come cittadino demo, voglio trovare i ticket dimostrativi nel mio account, così
posso provare tracking autenticato, pratiche personali e autorizzazioni owner.

## Acceptance criteria

- [x] L'identità canonica del cittadino demo è dichiarata una volta nel seeder utenti.
- [x] `TicketDatabaseSeeder` cerca quell'account tramite `XotData` e il model dinamico
      che implementa `UserContract`; nessun fallback al primo utente del database.
- [x] Se il cittadino demo non esiste, il seeder salta i ticket con un messaggio preciso.
- [x] Test con un utente estraneo già presente prova che `DEMO-001.owner_id` resta
      uguale all'identificativo del cittadino demo.
- [x] Riesecuzione seed idempotente; browser isolato con owner e secondo account
      verifica il confine privato/pubblico, la protezione del codice, il 403 guest
      per ID e il tracking guest by-code.

## Verifica

Usare un database SQLite effimero ignorato da Git. Non eseguire `migrate:fresh` sul
database locale persistente e non modificare `.env.testing`, credenziali o grant.
Il seed della demo deve lasciare inalterati dati, file e stato del database condiviso.

## Evidenze di verifica

- `APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity/tests/Feature/Database/FixcityDatabaseSeederTest.php` — **2 test, 10 asserzioni passati**. Copre owner deterministico con un utente estraneo preesistente, idempotenza e skip senza citizen demo.
- Playwright su `database/tmp/fixcity-browser-e2e.sqlite`, server isolato `127.0.0.1:8095` — **1 test passato**: owner vede il codice del proprio ticket privato/pubblico; un altro account vede solo i campi pubblici e non il codice, e riceve 403 sul ticket `pending` privato; guest via ID riceve 403; guest via capability vede stato e titolo senza ricevere il codice; zero overflow/JS errors.
- Seed reale isolato: 20 ticket demo, `DEMO-001.owner_id` uguale all'identificativo del cittadino demo. Nessun database condiviso o grant MySQL modificato.
- PHPStan `analyse Modules` — **zero errori**; Pint mirato e `verify-llm-wiki.sh` passano.
