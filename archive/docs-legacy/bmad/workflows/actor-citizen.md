---
title: "BMAD actor — Cittadino"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, actor, citizen, wizard, fixcity]
module: Fixcity
qmd: "citizen wizard create ticket tracking rating privacy fixcity workflow"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - ../actor-flows.md
  - 07-ui-ux.md
  - 08-security.md
  - ../../wiki/concepts/actor-flow-map.md
---

# Attore: Cittadino

**Scopo:** segnalare un disservizio urbano, seguirlo e chiudere il ciclo con feedback.

## Happy path

1. Accede con il proprio account. La policy deve essere pubblicata dal tenant
   nella lingua attiva. Se manca, `/privacy` spiega che non è disponibile (503) e
   il wizard blocca raccolta/invio; il go-live dipende dal proprietario dell'ente.
   La semantica del checkbox “letto e compreso” e l'eventuale prova persistente
   sono ancora da definire con il titolare; vedere G-31.
2. Compila dati + categoria + posizione + allegati via wizard
   (`CreateTicketWizardWidget` / Folio create).
   Non reinserisce nome, codice fiscale o recapiti: il wizard è autenticato e
   questi campi non erano persistiti; identità ed email derivano dall'account.
3. `CreateTicketWizardWidget` chiama `CreateTicketAction`; il modello Ticket
   normalizza la posizione prima del salvataggio. `GetTicketFormDataForPersistAction`
   è l'adapter del lifecycle Resource backoffice, non il submit FO.
4. Riceve conferma/codice e apre tracking.
5. Vede timeline (`BuildTicketTimelineAction`) e stato pubblico consentito; se
   inserisce un codice errato conserva il valore e riceve un errore localizzato
   associato al campo.
6. Risponde / commenta nel perimetro policy.
7. A `RESOLVED`/`CLOSED` lascia rating (`SubmitCitizenTicketRatingAction`).
8. Da Area personale → Impostazioni sceglie se ricevere email operative FixCity;
   il canale in-app rimane disponibile.

## Failure path (obbligatori)

- consenso assente, validazione fallita, posizione fuori bounds, upload rifiutato;
- sessione scaduta; ticket altrui → deny `TicketPolicy::view`;
- notifica fallita → ticket comunque salvato, retry/coda.
- codice di tracking sconosciuto → messaggio IT/EN, valore conservato e campo
  marcato non valido; il cittadino può correggere e riprovare.

## Prove

Pest copre submit Livewire reale con policy configurata nel test, ticket persistito, coordinate, proprietario,
codice e payload di conferma; copre inoltre isolamento, tracking valido IT/EN,
codice errato e privacy owner-only. Le prove browser precedenti sono evidenza storica
per wizard/tracking e layout a 320/768/1440: non certificano la nuova pagina privacy.
I test correnti verificano privacy assente/presente e il blocco submit; Chromium
nell'ambiente attuale non parte per librerie OS mancanti, quindi la nuova route non
ha ancora screenshot o interazione responsive corrente. Un test HTTP
attraversa submit Livewire e pagina Folio di conferma, verificando codice ed
email nel markup; una regressione Livewire verifica l'immagine nella collection
`attachments` e sul disk isolato. Chromium su SQLite effimero attraversa privacy,
dati, selezione immagine, riepilogo e submit fino alla conferma visuale; il DB
mostra il ticket sintetico con un file Media associato. Lo smoke MySQL/staging
resta aperto. Rating:
visibilità solo al
proprietario, stato risolto, rifiuto valori fuori scala, persistenza e conferma
sono verificati da Pest.

## Non fare

Controller HTTP, Services layer, duplicare schema nel tema.
