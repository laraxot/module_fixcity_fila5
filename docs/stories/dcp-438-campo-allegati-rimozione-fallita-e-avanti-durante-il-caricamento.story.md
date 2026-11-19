---
title: "DCP-438: campo allegati, rimozione fallita in silenzio e Avanti durante il caricamento"
type: story
status: ready-for-dev
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, fixcity, upload, wizard, da-riprodurre, i18n]
qmd: "campo allegati removeUploadedFile errore silenzioso avanti durante upload preparing uploading wizard dc-file-upload"
related:
  - ../bmad/stories/mobile-camera-upload.story.md
  - ./dcp-424-accessibilita-del-campo-allegati-da-fotocamera.story.md
  - ./dcp-418-matrice-dispositivi-reali-per-upload-da-fotocamera.story.md
---

# DCP-438: campo allegati, rimozione fallita in silenzio e Avanti durante il caricamento

## Richiesta (verbatim)

«crea tutte le bmad stories». Vincolo permanente: nessuna frase in una lingua sola nel codice, chiavi in it/en/de/es.

## Origine

Due rilievi del revisore client sulla story [mobile-camera-upload](../bmad/stories/mobile-camera-upload.story.md), rimasti aperti. Non sono accessibilita': per quella c'e' [DCP-424](./dcp-424-accessibilita-del-campo-allegati-da-fotocamera.story.md).

## Evidenza (lettura del codice, 2026-10-27; nessuno dei due riprodotto)

**1. Rimozione che fallisce.** `dc-file-upload.blade.php`, `remove(item)` (righe 138-144): azzera `message`, poi per un file gia' caricato fa `await this.$wire.callSchemaComponentMethod(this.componentKey, 'removeUploadedFile', { fileKey })` e solo dopo `this.drop(item.key)`. Non c'e' `try/catch`: se la chiamata fallisce (rete, 500, sessione scaduta) la promessa e' rifiutata, la riga resta e l'utente non vede alcun messaggio. Nei lang `ticket_form.fields.images` non esiste una chiave `error_remove`.

**2. Avanti durante il caricamento.** Fuori dal Blade, in `Modules/Fixcity/resources/views/filament` e `Modules/Fixcity/app/Filament`, `grep` di `uploading`, `isUploading`, `livewire-upload` non trova nulla. Il Blade non emette eventi che blocchino il pulsante «Avanti» o l'invio mentre una riga e' `preparing` o `uploading`. Conseguenza ipotizzata: Avanti toccato prima della fine e segnalazione creata senza quella foto. Il revisore non aveva letto il wizard Filament, quindi **potrebbe esserci un blocco che non ho trovato**: la prima azione e' riprodurre.

## Acceptance criteria

1. Se `removeUploadedFile` fallisce, la riga resta, compare un messaggio (`role="alert"`) con testo da chiave in it/en/de/es e l'utente puo' riprovare.
2. Con una riga in `preparing` o `uploading`, «Avanti» e l'invio non procedono: o sono disabilitati con un motivo leggibile, o attendono la fine. Se il comportamento e' gia' corretto, la story lo dimostra con una misura e si chiude senza codice.
3. Test sul sorgente delle chiavi nuove in tutte e quattro le lingue (con `hasForLocale`) e prova in browser dei due casi.

## Tasks

- [ ] Riprodurre il punto 2 in Chromium (Playwright, rete rallentata, 375 px) sul wizard reale e annotare cosa succede toccando Avanti a meta' caricamento.
- [ ] Riprodurre il punto 1 forzando l'errore sulla chiamata di rimozione (richiesta bloccata dalla rete del browser).
- [ ] Aggiungere `error_remove` in `lang/{it,en,de,es}/ticket_form.php` e gestire l'errore nel `remove()`.
- [ ] Bloccare Avanti secondo l'esito del punto 2.
- [ ] Aggiornare `CreateTicketWizardDataStepTest.php` per le chiavi e per l'attributo che blocca.

## Owned file scope

`laravel/Modules/Fixcity/resources/views/filament/forms/components/dc-file-upload.blade.php`, `laravel/Modules/Fixcity/lang/{it,en,de,es}/ticket_form.php`, `laravel/Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardDataStepTest.php`. Il JS `camera-upload.js` non cambia.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato; il Blade e' toccato anche da altre sessioni, rileggere prima |
