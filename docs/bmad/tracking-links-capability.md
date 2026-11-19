---
title: "Tracking per codice capability — notifiche e lista seguite"
type: bugfix
status: fixed
created: 2026-09-27
updated: 2026-09-27
module: Fixcity
tags: [bmad, tracking, notification, privacy, citizen]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - stories/STORY-508-confirmation-tracking-fo.md
  - stories/STORY-511-citizen-ticket-follow.md
  - ../wiki/concepts/user-journey-map.md
qmd: "ticket follower status notification sequential ticket id tracking capability code"
---

# Tracking per codice capability

## Difetto osservato

La notifica di cambio stato e la pagina “Segnalazioni seguite” componevano il tracking usando `ticket_id`, l'identificativo sequenziale. Un follower non proprietario non poteva aprire il tracking owner-only per ID, nonostante la segnalazione fosse pubblica; il link inoltre esponeva l'identificativo interno.

## Correzione

- Le notifiche di stato contengono il codice-capability e collegano a `/tickets/track?code=...`; il payload non pubblica `ticket_id`.
- Il dispatch salta con warning una notifica se un ticket storico non ha un codice di tracking.
- La lista seguite genera lo stesso link capability. I record storici senza codice mostrano un testo localizzato invece di un link vuoto.
- Il limite del tracking guest (10 ricerche/minuto) ora ha copertura HTTP.

## Verifica

- Pest mirato dopo la correzione: 6 test / 24 asserzioni (include ticket legacy senza codice nella notifica).
- Suite completa Fixcity eseguita con `APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity/tests --compact`; test esclusivamente sulla SQLite condivisa e transazioni del modulo. Esito verificato: **345 test / 1.416 asserzioni** in 97,52 secondi.
- PHPStan `analyse Modules`: nessun errore; Pint sui file PHP cambiati e `php artisan view:cache` passano.
- Smoke HTTP sul server locale `127.0.0.1:8094`: `/it/tickets/track`, `/it/auth/register`, `/it/segnalazioni` **200**; `/it/area-personale/seguite` guest **302** verso `/it/auth/login`; cookie stylesheet **200**. Questo verifica routing/rendering HTTP e redirect, non layout responsive.
- Il percorso visuale responsive resta non verificato da browser: `@playwright/test` non è installato e Chromium manca delle dipendenze native. Non è stato richiesto un grant MariaDB né modificato alcun DB persistente.

## Gap aperti

- E2E browser su mobile e due account autenticati.
- Verifica di schema/migrazioni su MariaDB test e staging con autorizzazione e credenziali owner.
- Preferenze e notifiche email/push restano una feature separata, come specificato nella STORY-511.
