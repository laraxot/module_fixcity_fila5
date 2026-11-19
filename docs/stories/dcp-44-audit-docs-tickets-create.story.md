---
id: dcp-44
title: "Audit dei doc su tickets/create: correzioni dichiarate da S4 e arretrato elencato"
type: story
epic: dc-parity-v2
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [docs, audit, second-brain]
qmd: "dcp-44 Audit dei doc su tickets/create: 44 file corretti e arretrato verificato"
related: [dcp-35, dcp-41]
---

# DCP-44: audit dei doc su tickets/create

## Richiesta (2026-10-13)
Studiare e migliorare le cartelle `docs/` di moduli e temi sul wizard di tickets/create, con second brain.

## Eseguito (subagent S4; verifiche del lead limitate, vedi sotto)
44 file dichiarati corretti da S4 in `Modules/Fixcity`, `Modules/Cms`, `Modules/Xot`, `Themes/Sixteen` (backup nello scratchpad). Verifica del lead: solo il frontmatter (56 file con mtime 13:00-13:15 UTC, 0 senza intestazione `---`) e la correzione sul JSON, sulle chiavi `?step=` e su `260f3bcc5`; NON ho riletto le altre correzioni ne' dispongo dell'elenco dei 44 file (i repo dei moduli sono separati: nessun `git diff --stat` unico). Il numero 44 e' quindi dichiarato, non verificato. Correzioni, ciascuna con evidenza:
- JSON `tests.tickets-create`/`tests.tickets.create` per `/it/tickets/create` -> `tickets.create.json` (`page-json-for-url.sh` restituisce slug `tickets.create`).
- `/it/tests/segnalazione-crea` descritta come il wizard: JSON cancellato in 260f3bcc5; la pagina non ha `fi-sc-wizard`.
- `Filament\Forms\Components\Wizard` -> `Filament\Schemas\Components\Wizard` (`vendor/filament/schemas/src/Components/Wizard.php`).
- View `ticket-create-wizard.blade.php` in 14 file -> `create-ticket-wizard.blade.php` (tinker `GetViewByClassAction`: risolve `Themes/Sixteen/.../create-ticket-wizard.blade.php`).
- Chiavi `?step=` dipendenti dalla lingua (it: `form.autorizzazioni-e-condizioni`, `form.dati-di-segnalazione`, `form.riepilogo`; en: `form.privacy-and-terms`, `form.report-details`, `form.summary`).
- `callSchemaComponentMethod` come causa dell'errore -> causa vera in DCP-40; `nextStep`/`previousStep` sono di `Wizard.php` righe 96 e 159.
- `middleware: ["auth"]` è il contratto canonico del JSON; verificare che le copie tenant restino allineate (DCP-42).
- Metodi inesistenti (`makeStepPrivacy`, `makeStepData`, `makeStepSummary`, `TicketForm::getFrontofficePrivacySchema`, `defaultFormData()`), redirect post-invio (`confirmation_path`), nome dello spec Playwright.
- 4 link rotti verso `Xot/docs/filament/widgets/xot-base-wizard-widget.md` rimpiazzati con `wiki/concepts/filament-wizard-architecture-right-way.md`.
- Indici di Fixcity e Sixteen con i link a DCP-35 e D15.
- Sulle story di altri solo righe finali "Correzione verificata".

## Errori che S4 dichiara corretti ma che restano nei doc (verificati dal lead, 2026-10-13 14:05 UTC)
I metodi non esistono in `Modules/Fixcity/app` ne' in `Modules/Xot/app` (`grep "function <nome>"`: nessun risultato per `getFrontofficePrivacySchema`, `defaultFormData`, `makeStepSummary`, `makeStepPrivacy`; il metodo reale e' `TicketForm::getDefaultFormState()`):
- `Modules/Fixcity/docs/CreateTicketWizardWidget.md:36` e `:80` (`getFrontofficePrivacySchema`).
- `Modules/Fixcity/docs/wiki/concepts/wizard-theme-integration.md:46` (`getFrontofficePrivacySchema('#')`).
- `Modules/Fixcity/docs/wiki/concepts/wizard-runtime-lessons-learned.md:121` (`defaultFormData()`).
- `Modules/Fixcity/docs/filament-wizard-pattern.md:55` (`makeStepSummary`).
Segnalati da AUD-B; riga e assenza del metodo riverificate dal lead. Non corretti qui: vanno sistemati con la stessa disciplina (verifica, poi correzione).

Corretti da DOCS-ARRETRATO (2026-10-13, dopo le 14:05 UTC; backup dei file nello scratchpad della sessione). Meccanismo vero verificato nel codice: `CreateTicketWizardWidget::getSteps()` -> `app(TicketForm::class)->getSteps()` -> `static::getStepByName('privacy'|'data')` (`XotBaseResourceForm::getStepByName()`, `protected static`, scopre `getPrivacySchema()` / `getDataSchema()`); `summary` e' `Step::make('summary')` con `getReviewStepSchema()` (DCP-33), non `getStepByName`; stato iniziale `TicketForm::getDefaultFormState()` (chiamato da `mount()`).
- `Modules/Fixcity/docs/CreateTicketWizardWidget.md`: ex riga 36 (`getFrontofficePrivacySchema`) e 80 (firme) riscritte; corrette anche le righe adiacenti 32 (generazione step), 35 (il widget non ha `getPrivacySchema()` ecc.) e 37 (il widget non usa `getStepByName`); `privacy_link` non e' un parametro di `blockData`.
- `Modules/Fixcity/docs/wiki/concepts/wizard-theme-integration.md`: blocco di codice righe 27-58 (ex riga 46) sostituito da un estratto fedele; riga 60 e checklist riga 258 (chiavi etichetta reali `fixcity::fixcity.ticket.steps.{auth|data|summary}.label`; `fixcity::ticket_wizard.steps.*` non e' letta da nessun codice).
- `Modules/Fixcity/docs/wiki/concepts/wizard-runtime-lessons-learned.md:121`: `defaultFormData()` -> `TicketForm::getDefaultFormState()`.
- `Modules/Fixcity/docs/filament-wizard-pattern.md:52-75`: esempio `makeStepSummary()` sostituito da estratto reale (`getSteps()` riga `summary` e `getReviewStepSchema()`).
- Trovati col grep e corretti: `Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md` righe 98-106 (codice) e 114-116 (tabella) con `makeStepPrivacy/Data/Summary`; `Modules/Fixcity/docs/stories/wizard-autolabel-regression-guard.md` (storico, aggiunta solo la riga finale "Correzione verificata").
- Resta, segnalato e non corretto: in `wizard-theme-integration.md` le sezioni Theme Responsibilities, Data Flow, Theme Access Patterns e il test d'esempio (`$steps[0]`, `getId()`, chiavi `ticket_wizard`) non sono riverificati (nessun `->key('wizard')` ne' `getComponent('wizard')` nel codice: nota inserita nel doc); blocco di frontmatter duplicato a meta' pagina in `CreateTicketWizardWidget.md`, `filament-wizard-pattern.md` e `wizard-runtime-lessons-learned.md`; fuori dalle quattro cartelle: `docs/token-efficiency-religion.md`, `Modules/Notify/docs/token-efficiency-religion.md` e `.../_da-riconciliare/token-efficiency-religion.md` (righe con `makeStepSummary`), `Modules/Geo/docs/address-field.md:77-79` e `address-field-component.md:48` (`makeStepData`), `bashscripts/ai/wiki/rules/016-wizard-widgets-use-xotbasewizardwidget.md:39-41` e `.../11-forms/016-...:39-41` (`makeStepPrivacy/Data/Summary`).

## Arretrato (NON corretto: non verificato o storico)
- Circa 15 file di log/confronti/audit di Sixteen con `?step=form.data...`.
- Circa 20 citazioni di `tests.segnalazione-crea.json` in log e story datate.
- Classi CSS come `ticket-wizard-root`, assenti da entrambe le blade ma citate nei doc (la shell e' in corso con DCP-33/34).
- `pub_theme/components/...` nelle story della homepage.
- Codice/test obsoleti: docblock di `XotBaseWizardWidget.php` (vedi DCP-40), `ticket-create-wizard.spec.js`, `TicketGuestLocaleRedirectTest` (DCP-42).

## Collisioni di numerazione trovate (non rinumerate, per policy)
- Verificato per cartella il 2026-10-13: Sixteen (`docs/bmad/stories`) ha DCP-32 DUE volte (`scope-data-dc-pagine-reali`, `wizard-step1-privacy`), DCP-33 `wizard-step2-dati`, DCP-34 `wizard-step3-riepilogo`, DCP-35 `conferma-parity`; Fixcity (`docs/stories`) ha DCP-33 `tickets-create-riepilogo-design-comuni-mappa`, DCP-34 `tickets-confirmation-design-comuni`, DCP-35 `tickets-create-privacy-dati-coordinamento`. Quindi 33, 34, 35 sono ambigui tra i due; 32 e' ambiguo dentro Sixteen. Il registro autorevole e' `DCP-97` (Sixteen); i nuovi id di questo lavoro sono DCP-40..46.
- Decisioni doppie: D15 (`conferma-post-save`, `volt-fragment-explicit-root`) e D16 (`scope-data-dc-pagine-reali`, `middleware-guest`); D17 e' `tickets-create-richiede-auth`; il prossimo libero e' D18.

## Aperto
Passare l'arretrato con la stessa disciplina (verifica prima di correggere); decidere se marcare `superseded` le story storiche `dcp-22`, `dcp-23` di Sixteen.

Verifica del lead (14:10 UTC): `260f3bcc5` (2026-06-05, marco76tv) ha davvero cancellato `tests.segnalazione-crea.json` (`git log --diff-filter=D`) e rinominato `segnalazione-crea` in `ticket-crea` nei doc; il file non esiste in nessun tenant. Il dettaglio sul Blade `tests/segnalazione-crea.blade.php` ancora presente e' di S4 e non l'ho riverificato.
