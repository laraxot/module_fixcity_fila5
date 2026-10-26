---
id: dcp-286
title: "DCP-286: wizard di segnalazione, invio idempotente, sessione scaduta, ricerca del luogo e conferma onesta"
type: story
epic: go-live-front-office
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
qmd: "dcp 286 wizard invio idempotente doppio clic token di invio Cache::lock sessione scaduta Accedi di nuovo cerca un luogo geocodifica conferma onesta link morti numero verde servizio inventato FO-3-02 FO-3-06 FO-3-07 FO-2-25 FO-2-26 FO-3-03 FO-3-24 FO-6-02"
related: [dcp-124, dcp-127, dcp-128, dcp-146, dcp-186, dcp-284, dcp-289]
tags: [wizard, idempotenza, sessione, geocodifica, conferma, link-morti, front-office, bmad]
---

# DCP-286: wizard di segnalazione, invio idempotente, sessione scaduta, ricerca del luogo e conferma onesta

## Fase BMAD

Dev (TDD): story prima, poi test rosso, codice, test verde, misura nel browser su :8091 (istanza di prova, DB usa-e-getta).
Origine: audit front office del 2026-10-26, gruppi fo-3 (FO-3-02, 03, 06, 07, 17..19, 21..26), fo-2 (FO-2-25, FO-2-26) e fo-6 (FO-6-02, parte
conferma e creazione). Ogni affermazione dell'audit e' DA RIVERIFICARE: sotto, ogni fatto e' marcato MISURATO (eseguito da me, con l'output) o
DEDOTTO (letto dal codice, non eseguito).

## Stato

in-progress. Nessun commit, nessuna migrazione, nessuna dipendenza nuova, nessuna build del tema.

## Fatti misurati prima di toccare

Compilati durante il lavoro: vedi sezione Esito per gli output reali. Fatti di partenza, letti dal codice (DEDOTTO fino a prova):

1. DEDOTTO. `CreateTicketWizardWidget::save()` non ha alcuna idempotenza: due chiamate di `save()` con lo stesso stato del form chiamano due volte
   `CreateTicketAction::execute()`, quindi due `Ticket::create`. Il pulsante finale e' `x-on:click="$wire.submit()"` (JSON della pagina) senza `wire:loading`.
2. DEDOTTO. Per un ospite `save()` fa `addError('global', ...)`; la vista del tema del widget mostra solo `data.submit` e `submit`.
3. DEDOTTO. "Cerca un luogo" (`location.address`) usa la vista `dc-place-autocomplete`: un `input[type=search]` legato a `data.location.address`,
   senza alcun collegamento alla ricerca del `coordinate-picker-lit` (il commento della vista dice: "Suggerimenti mentre si digita: non implementati").
4. DEDOTTO. La conferma (`tickets/confirmation.json`, tre copie: `config/local`, `config/eu`, `Modules/Fixcity/config`) porta "Abbiamo inviato il
   riepilogo all'email :email", cinque URL `#`, il servizio correlato "Richiesta appuntamento" e "Chiama il numero verde 05 0505".
5. MISURATO. Meccanismo "dati dell'ente" (DCP-289): al momento della lettura non esiste ne' una Action `*Ente*` ne' un file `config/**/ente*`
   (`ls` vuoti, nessuna story `dcp-289-*`).
6. MISURATO. Baseline dei test del widget prima di toccare: `Tests: 18 passed (60 assertions)` su `CreateTicketWizardWidgetTest`,
   `CreateTicketWizardTamperingTest`, `CreateTicketWizardInputValidationTest`.

## Causa

Da completare dopo la riproduzione di ogni difetto.

## Decisioni

Da completare.

## Criteri di accettazione verificabili

1. Due chiamate di `save()` con lo stesso token di invio creano UNA sola segnalazione e portano alla stessa conferma (stesso codice).
2. Il token di invio e' generato al mount, `#[Locked]` (il client non lo scrive) e nessun ospite o altro utente puo' riusare il token di un altro.
3. Il pulsante finale del wizard e' disabilitato mentre l'invio e' in corso (`wire:loading.attr="disabled"` con `wire:target`).
4. Invia con sessione scaduta mostra un messaggio chiaro con l'azione "Accedi di nuovo" che riporta al wizard dopo il login.
5. "Cerca un luogo" cerca davvero (collegamento esistente al picker) oppure il campo e' onesto sul suo ruolo; l'errore non resta a campo compilato.
6. La conferma non promette un'email che non parte: testo veritiero in it, en, de, es, che cita l'area personale.
7. Nessun link `#` nella conferma e nel blocco contatti del wizard; nessun servizio inventato; nessun numero di telefono copiato dal template.

## Test

Esecuzione solo con `PEST_OUT=<file> bash bashscripts/tools/pest-scratch-db.sh <percorso relativo a laravel/>`.
Da completare.

## Owned file scope

Elenco esatto dei file che tocco (C creato, M modificato). Aggiornato a fine lavoro.

- C `laravel/Modules/Fixcity/docs/stories/dcp-286-wizard-invio-idempotente-e-conferma-onesta.story.md`

## Esito

Da completare.
