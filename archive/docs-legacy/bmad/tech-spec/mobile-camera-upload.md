---
title: "Tech spec: percorso camera per allegati segnalazione"
type: tech-spec
status: superseded
superseded_by: ../stories/CAM-1.1-runtime-transport-contract.story.md
module: Fixcity
related_module: Media
updated: 2026-10-27
---

# Obiettivo storico

> La specifica originaria descriveva `getUserMedia` e un percorso camera
> dedicato. Il backlog operativo corrente è CAM-1.1..CAM-1.5; questa pagina
> resta come evidenza delle decisioni iniziali e non come contratto eseguibile.

Consentire al cittadino autenticato di scattare una foto dal browser mobile e
allegarla alla segnalazione senza l'errore generico di Livewire.

## Requisiti MUST

- M1: il percorso camera deve usare `getUserMedia` solo in contesto sicuro e
  chiedere esclusivamente video, senza audio;
- M2: il frame deve essere ridimensionato a massimo 1280 px sul lato maggiore e
  convertito in JPEG prima di `$wire.upload()`;
- M3: il file deve entrare nello stesso stato `data.images` del picker e nella
  stessa collezione `attachments`;
- M4: permessi negati, HTTPS mancante, camera assente, conversione fallita e
  upload fallito devono mostrare una chiave tradotta, non una frase hardcoded;
- M5: il contratto server resta JPEG/PNG/PDF, 5 file, 10 MB per file;
- M6: `camera=()` va sostituito con `camera=(self)` per il front office, senza
  concedere microfono;
- M7: il limite infrastrutturale deve restare almeno PHP 12M/16M e Nginx 16M.

## Non in scope

Uppy, upload a chunk, S3 diretto, OCR, editor fotografico, più fotocamere,
audio, base64 persistente e nuova tabella media.

## Contratto UI

Il componente mantiene il picker esistente e aggiunge un'azione camera. Il
risultato è un `File` JPEG con nome localizzato/derivato dal nome del campo,
poi passa dalla stessa funzione `send(file)`. Le chiavi camera appartengono a
`Modules/Fixcity/lang/{it,en,de,es}/ticket_form.php`.

## Verifica pianificata

- unit/feature: markup, traduzioni presenti in quattro lingue, MIME e limiti;
- browser: 375 px con camera simulata, permesso negato, HTTPS mancante, foto
  oltre 2 MB, upload riuscito e anteprima al riepilogo;
- browser: 768 e 1440 px per non rompere il picker desktop;
- infrastruttura: fixture con PHP 12M/16M e probe Nginx `client_max_body_size`;
- regressione: nessun ticket/media orfano e nessun URL di preview esposto.
