---
title: "DCP-419: il testo del limite allegati distingue foto e PDF"
type: story
status: ready-for-dev
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, upload, i18n, ticket-form]
qmd: "testo limite allegati massimo 1.8 MB foto ridotte pdf chiavi lingua ticket_form it en de es"
related:
  - ../bmad/stories/mobile-camera-upload.story.md
issues: []
discussions: []
---

# DCP-419: testo del limite allegati

## Origine

La story madre ([mobile-camera-upload](../bmad/stories/mobile-camera-upload.story.md), «Aperto») nota che il campo dice «massimo 1.8 MB ciascuno» (il limite PHP reale, 90% di 2M) anche per le foto, che invece vengono ridotte da sole nel browser. La frase e' vera per i PDF e fuorviante per le foto: un cittadino con una foto da 6 MB legge che e' troppo grande, e non lo e'.

## Acceptance criteria

1. Il suggerimento sotto il campo distingue le foto (si riducono da sole, nessun limite pratico) dai PDF (il limite vale).
2. Il limite mostrato per i PDF e' quello reale, letto a runtime come oggi, non scritto a mano.
3. Nessuna frase sta nel codice: le chiavi sono in `Modules/Fixcity/lang/{it,en,de,es}/ticket_form.php`, con le stesse chiavi in tutte e quattro le lingue.
4. `Modules/Fixcity/tests/Unit/TicketFormImagesLangTest.php` copre le nuove chiavi nelle quattro lingue.
5. Se si decide di tenere la frase com'e', la decisione e la ragione sono scritte qui e la story si chiude senza codice.

## Tasks

- [ ] Rileggere `dc-file-upload.blade.php` e le chiavi `fields.images.*` correnti (l'altra sessione le ha modificate il 2026-10-27).
- [ ] Scrivere le nuove frasi in it, en, de, es e collegarle alla vista.
- [ ] Estendere il test di lingua e farlo girare con `bashscripts/tools/pest-scratch-db.sh`, mai con `DB_DATABASE=:memory:`.
- [ ] Controllare a 390 px che il testo non vada a capo male nelle quattro lingue.

## Owned file scope

`laravel/Modules/Fixcity/lang/{it,en,de,es}/ticket_form.php`, `laravel/Modules/Fixcity/resources/views/filament/forms/components/dc-file-upload.blade.php`, `laravel/Modules/Fixcity/tests/Unit/TicketFormImagesLangTest.php`. Un'altra sessione ha toccato questi file: rileggerli prima di scrivere.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
