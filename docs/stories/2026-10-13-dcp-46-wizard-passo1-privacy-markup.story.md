---
id: dcp-46
title: "Passo 1 del wizard (privacy): markup del riferimento e un solo h1"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [wizard, privacy, design-comuni, passo-1]
qmd: "dcp-46 Passo 1 del wizard (privacy): markup del riferimento e un solo h1"
depends_on: [Modules/Fixcity/docs/stories/2026-10-13-dcp-35-tickets-create-privacy-dati-coordinamento.story.md, Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-32-wizard-step1-privacy.story.md]
reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html
local: http://192.168.1.40:8000/it/tickets/create
---

# DCP-46: passo 1 (privacy) del wizard

## Richiesta (2026-10-13)
`/it/tickets/create` con altissima parity su `segnalazione-01-privacy`, form come Filament wizard. Perche' un wizard: il riferimento e' un flusso di tre passi piu' conferma (stepper "1/3"); lo schema `TicketForm::getSteps()` e' la fonte unica usata anche dal back office, con validazione per passo, upload Media Library e stato Livewire.

## Eseguito (subagent S1)
- `gdpr-notice.blade.php` riscritto: i blocchi dell'informativa pubblicata diventano `p.text-paragraph.mb-lg-4`, poi `p.text-paragraph.mb-0` con `a.t-primary`; tolti `.prose` e l'`h1` dello step (resta l'unico `h1` della pagina).
- `TicketForm.php`, solo `getGdprHtml()` (scarta il titolo `#` del markdown) e `getPrivacySchema()` (usa la nuova vista del checkbox).
- Nuovo `filament/forms/components/privacy-consent-checkbox.blade.php` (fuori dallo scope previsto): il Checkbox Filament mette l'input dentro la label, il CSS Design Comuni vuole `.form-check > .checkbox-body > input + label`. Stato, validazione, `aria-*`, `data-element="privacy-consent"` e `fi-invalid` restano di Filament.

## Misure (gate, guest, 1440 px; rimisurate dal lead)
| | pixel | altezza (riferimento 1938) |
|---|---|---|
| prima | 75,92% | 2241 |
| dopo | 85,05% | 2124 |
`curl` 200; un solo `<h1`; checkbox non spuntato + "Successivo" mostra l'errore con `fi-invalid`; spuntato porta a 2/3. Test del modulo NON eseguiti (DB di sviluppo, vedi DCP-45).

## Perche' non e' al 97%
- CSS `!important` che vale perche' lo slug non e' nello scope `data-dc`: `12-wizard-dc-form-shell.css:55` e `:66` (colonna 760 px), `ticket-parity.css:2016` e `:2708` (`max-width: 640px !important`, testo 640 px; verificate dal lead con `grep`). S1 aveva citato `:2010`, `:2769` e `:2712`: `:2769` non contiene il limite e la classe `.segnalazione-privacy-page` non e' emessa da nessuna vista (nessun risultato in `Themes/Sixteen/resources/views` e `Modules/Fixcity/resources/views`), quindi non e' provato che quelle regole agiscano sulla pagina; quale selettore blocchi il testo a 640 px resta da trovare (`explain-style.mjs`).
- Shell (DCP-33/34): contenuto a y=577 contro 473, bottoni "Annulla/Successivo" invece di "Avanti", niente breadcrumb ne' "Contatta il comune", footer v1.
- Altezza: 186 px sopra; l'informativa tenant ha 3 paragrafi (8 righe contro 4) e non si accorcia senza modificare `config/local/fixcity/lang/*/policy.md`.

## Difetti trovati fuori scope
- `/it/privacy` e `/privacy` rispondono 404 (il footer linka `/privacy`): la pagina e' rimasta in `pages_old/privacy/index.blade.php`; serve un JSON di pagina, da chiedere all'utente (non si crea senza).
- Lingue: `fixcity::ticket.privacy.*` e' definito in `lang/it/ticket.php` (25 occorrenze di "privacy"); in `lang/en/ticket.php` ce n'e' 1, in de ed es 0 (ripiego italiano). Anche `ticket_form.fields.privacyAccepted.*` va controllato in en.

## Aperto
Rimisurare dopo `layout: design-comuni` e il lavoro sulla shell; decidere `/privacy`.

## Output allegati (comandi del lead, 2026-10-13)
Misura indipendente dopo la modifica di S1 (13:30 UTC circa, guest, prima dell'auth e del layout `design-comuni`):
```
$ node bashscripts/tools/visual/compare-screenshots.mjs https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html http://192.168.1.40:8000/it/tickets/create --width 1440 --json
parity 85.05 altezze 1938 2124
```
Solo 1440 px: 768 e 375 non rimisurati dopo la modifica. Il "prima" (75,92% / 2241) e' del gate di S5. Il numero e' STORICO: la pagina oggi rimanda l'ospite al login (D17) e usa `layout: design-comuni`.
PHPStan sul solo `TicketForm.php` (lead, 14:05 UTC): 9 errori; quello alla riga 192 era in `getGdprHtml()` di S1 (`preg_replace` non sicuro: serve `use function Safe\preg_replace;`) e l'ho corretto aggiungendo l'import; restano 8 errori nei blocchi di altri: riepilogo (righe 62 e 113, `BuildTicketReviewSummaryAction::execute()` e `getSummarySectionSchema()`) e autore (257-258, proprieta' `codice_fiscale`/`fiscal_code` non definite su `Modules\Fixcity\Models\User`).
Test del modulo: non eseguiti per il passo 1 (vedi DCP-45 per l'isolamento).
