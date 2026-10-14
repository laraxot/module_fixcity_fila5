---
id: dcp-146-w3-form
title: "DCP-146 W3-FORM: verifica funzionale del passo Dati di segnalazione"
type: note
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
related:
  - Modules/Fixcity/docs/stories/2026-10-13-dcp-146-wizard-passo-dati-mappa-sempre-funzionante.story.md
  - Modules/Fixcity/docs/stories/2026-10-13-dcp-128-wizard-passo2-cablaggio-design-comuni.story.md
tags: [wizard, passo-2, i18n, validazione, http, filepond]
---

# W3-FORM: cosa e' stato provato e cosa non funzionava

Nessun file di prodotto e' stato modificato da W3-FORM: durante la prova (21:27 UTC) un altro agente (DCP-128) ha riscritto `TicketForm.php`, le viste `dc-*` e `lang/*/ticket_form.php`. Regola dei 5 minuti: fermo, solo verifica.

## Cosa non funzionava (con prova)
1. HTTP LAN: il punto non si imposta, quindi "Avanti" resta fermo con "Indica un punto valido sulla mappa". Il tocco sulla mappa non scrive `data.location.latitude/longitude` (stato letto da `Livewire.all()`), il campo indirizzo e' solo testo (non geocodifica), il GPS non e' disponibile su origine non sicura. Su localhost (contesto sicuro, posizione mockata) lo stesso flusso arriva alla conferma. Dipende dal bundle della mappa (W2) e dal suo rebuild.
2. Messaggi di validazione in italiano nelle altre lingue: `/en/`, `/de/`, `/es/` mostrano "Il campo ... e' obbligatorio" e "Il campo ... deve essere accettato" con l'etichetta tradotta dentro. `Modules/User/lang/en/validation.php` e `de/validation.php` sono copie italiane (stessi 8702 byte, stesso testo) e non esiste `es`. Causa del passaggio dalla richiesta web non provata fino in fondo (in `tinker` `trans('validation.required')` risponde in inglese).
3. Il deep link `?step=` dipende dall'etichetta del passo: `form.dati-di-segnalazione::...` esiste solo in it. In en/de/es lo slug e' `form.report-details`, `form.angaben-zur-meldung`, `form.datos-de-la-incidencia`; con lo slug italiano il wizard parte dal passo 1 (non e' un errore, ma i link non sono portabili tra lingue).
4. Prima del cablaggio DCP-128: FilePond senza CSS sotto `data-dc` lasciava l'elemento file sopra "Avanti" (`elementFromPoint` = `.filepond--file`, Playwright "intercepts pointer events"). Dopo il cablaggio (`dc-file-upload`) il test di hit e' libero.

## Cosa funziona (prova)
- Scratch (127.0.0.1:8091, DB usa-e-getta): privacy, dati (indirizzo, tipo, titolo, descrizione, foto PNG, punto da GPS mockato), Avanti al riepilogo, Invia, redirect a `/it/tickets/confirmation?code=...`, ticket con `status=pending`, `priority=low`, media `attachments` salvato. Idem `/en/`.
- Nessun testo con "https" o chiavi grezze (`fixcity::`, `ticket_form.`, `geo::`) in it/en/de/es nei passi 2 e 3.
