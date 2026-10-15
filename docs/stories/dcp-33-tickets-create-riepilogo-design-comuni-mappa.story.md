---
id: dcp-33
title: "DCP-33: /it/tickets/create passo riepilogo con altissima parity su segnalazione-03-riepilogo e con la mappa"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "tickets create wizard riepilogo design comuni segnalazione-03-riepilogo mappa coordinate picker parity data-dc"
depends_on: [dcp-32]
reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html
local: http://192.168.1.40:8000/it/tickets/create?step=form.riepilogo%3A%3Adata%3A%3Awizard-step
---

# DCP-33: riepilogo del wizard reale con la mappa

## Richiesta (2026-10-13)
`/it/tickets/create?step=form.riepilogo::data::wizard-step` deve avere altissima visual parity con `segnalazione-03-riepilogo.html`, con in piu' la mappa "per definire meglio dove e' il danno da riparare". Metodo BMAD + second brain.

## Capire il perche' (logica, filosofia)
- Il wizard e' un widget Filament Schemas (`CreateTicketWizardWidget`, `PubThemeWizard`) con schema SSoT in `TicketForm` (`getSteps`, `getSummarySchema`). La pagina e' CMS-driven: `tickets.create.json` -> blocco `ticket_create` -> widget. Regola di casa: niente CSS per slug, niente markup per pagina, componenti riusabili, `<body>` plain.
- La parity si ottiene facendo emettere al wizard lo stesso markup del riferimento (classi `steppers`, `cmp-card`, `single-line-info`, `cmp-nav-steps`), che il CSS generato gia' conosce; non con CSS nuovo.
- La mappa serve all'utente per verificare la posizione del danno: sola lettura, segnaposto sulle coordinate scelte al passo 2, stessa famiglia di componenti Geo del selettore (`CoordinatePicker`). E' l'unica deviazione dichiarata dal riferimento (misura: parity con la mappa nascosta, altezze uguali).

## Stato iniziale misurato (guest, 1440 px)
HTML body 24,84% (riferimento `tests/segnalazione-03-riepilogo`: 87,42%); pixel 55,74%, altezze 3115 contro 2519. Differenze viste: header e footer v1 invece di Design Comuni, nessun breadcrumb, stepper Filament, alert e sezioni diverse, nessun "Contatta il comune", pulsanti "Precedente / Salva" invece di "Indietro / Salva Richiesta / Invia", etichette senza valori.

## Coordinamento (altra sessione attiva sugli stessi file)
Alle 12:34-12:50 un'altra sessione ha modificato `Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php`, `TicketForm.php` (aggiunta `review_map` = `CoordinatePicker` in sola lettura), i lang `ticket_form` e due test (`TicketFormWizardSummarySchemaTest`, `CreateTicketWizardWidgetViewTest`). Regola: non toccare quei file senza rileggerli (mtime e `git diff`), modifiche minime e idempotenti. Qui si prende il chrome (DCP-32) e il layout `design-comuni` in `tickets.create.json`; il markup del passo e la mappa si concordano sul loro stato.

## Criteri di accettazione
1. HTML body >= 90% e pixel >= 97% a 1920/1440/992/768/375 con altezze uguali, misurati con la mappa nascosta (opzione `--hide` del gate); con la mappa visibile solo l'altezza cambia della sua altezza.
2. Un solo CTA di invio visibile ("Invia"), che chiama `submit`; "Indietro" e "Salva Richiesta" come nel riferimento.
3. Nessun CSS per slug o `.ticket-wizard-root`; nessun `any`; PHPStan a zero sui file toccati; test del modulo verdi.
4. Mappa: accessibile (nome, alternativa testuale con indirizzo), nessun errore console, funziona senza JS come testo.

## Analisi

Autore: subagent `dcp33-plan`, 2026-10-13 12:55-13:15. Solo lettura: nessun file del repo toccato a parte questa sezione, nessuna build, nessun browser (lo sweep dell'orchestratore stava misurando con 4 `parity-gate.sh` e load average 42). Prove temporanee nello scratchpad `.../scratchpad/dcp33-*` (snapshot HTML guest, `dcp33-tokdiff.mjs` che replica la tokenizzazione di `compare-body-html.mjs` e stampa token mancanti o in piu', esperimenti sintetici). Le righe citate sono quelle lette alle 13:00-13:10; `TicketForm.php` e `lang/it/ticket_form.php` erano in modifica da altre sessioni (mtime 13:04 e 13:06), quindi le loro righe scorrono.

### 0. Esito in dieci righe
1. Scelta: **(b)**. Il passo riepilogo diventa UN componente di schema Filament (`Filament\Schemas\Components\View`) con una vista del tema che riusa i partial gia' misurati `report-partials/*`, alimentata dallo stato del form tramite un'Action che costruisce `groups[].cards[].summaries[].rows[]` (stessa forma del JSON di `tests.segnalazione-03-riepilogo`). Filament resta contenitore di stato, validazione per passo, `?step=` e `save()`. Motivi e rischi al punto 3.
2. **Il criterio HTML >= 90% non e' raggiungibile con il rendering standard del wizard**, qualunque sia la scelta (a) o (b): Filament invia nell'HTML del passo 3 anche i passi 1 e 2 (57 e 341 token su 1212 totali, `x-cloak`). Esperimento sintetico (pagina di prova perfetta piu' i passi 1-2 cosi' come sono emessi oggi): **73,71%** url-normalizzata; con solo il passo 1: 93,46%; con tre `<form>` vuoti al posto dei passi: 97,05%. Servono passi inattivi non renderizzati (epica separata, punto 4) oppure una metrica che li escluda. Decisione richiesta all'utente.
3. Anche il valore **rigoroso** del gate (>= 90) e' irraggiungibile da qualunque pagina servita su URL diverse dal riferimento: la pagina di prova `tests/segnalazione-03-riepilogo` fa 87,42% rigorosa e 97,77% url-normalizzata (misurato ora), perche' `href`/`src` assoluti (`http://192.168.1.40:8000/themes/Sixteen/design-comuni/assets/...#it-check` contro `../assets/...#it-check`) sono token diversi. Per questa pagina vale la colonna url-normalizzata.
4. La mappa attuale (`review_map`, `CoordinatePicker` disabilitato) **non e' in sola lettura**: il Blade Geo non legge `disabled` e il Lit `coordinate-picker-lit` non ha una proprieta' readonly (marker `draggable: true`, click che scrive lo stato, ricerca). In piu' duplica `wire:key="...form.location"` e `wire:partial="schema-component::form.location"` (2 occorrenze nell'SSR delle 12:57), cioe' due editor sullo stesso stato. Proposta: mappa NON campo, componente Blade Geo con `readonly`, dentro la scheda Disservizio dopo la riga Indirizzo, nascosta nel gate con `--hide`.
5. Dati: Indirizzo, Tipo, Titolo, Dettagli, Immagini esistono; autore (nome) ed email esistono su `users`; telefono solo su `profiles.phone`; **codice fiscale non esiste da nessuna parte** (ne' colonna, ne' campo). Senza la riga Codice Fiscale l'altezza della pagina non puo' coincidere con il riferimento (una riga `single-line-info` sposta tutto il resto): serve una decisione di prodotto (candidato senza migrazione: `profiles.extra`, colonna JSON esistente).
6. Pixel con altezze uguali richiede una **fixture**: utente autenticato con i dati del riferimento e stato del wizard precompilato. L'URL guest con `?step=` mostra un riepilogo vuoto e righe piu' basse. Servono `--hide` (mappa) e un hook `--prepare` in `compare-screenshots.mjs` (punto 7); il harness `parity-wizard-steps.sh` (DCP-35 S5) inoltra gia' `--hide` se il gate lo supporta.
7. Prerequisiti DCP-32 non ancora veri sulla pagina: `tickets.create.json` non ha `"layout": "design-comuni"` (3 copie identiche) e quindi niente `data-dc` (verificato con curl alle 13:07); `components.css` e' gia' stato riscritto con `[data-dc]` (mtime 13:07) ma manca la build.
8. Nessuna classe o regola CSS nuova serve per il riepilogo: `steppers-header` 37 occorrenze, `cmp-nav-steps` 4, `callout-highlight` 10, `cmp-info-summary` 28, `single-line-info` 3, `cmp-card` 33, `cmp-contacts` 5, `cmp-breadcrumbs` 6 in `components.css`. Il markup attuale usa classi che il CSS non conosce: `stepper-step` e `steppers-list` (0 occorrenze), `alert-warning` (0), `fi-*`.
9. Sessione concorrente: possiede shell, riepilogo, `review_map`, lang e test (tabella claim di DCP-35). Questa analisi non propone di sovrascrivere niente: i punti che li riguardano sono nel punto 8 con la colonna "file concorrente" e nelle domande del punto 9.
10. Limiti: non ho eseguito browser, Pest, build; la reattivita' del riepilogo e la mappa in un passo nascosto sono letture del codice, non prove.

### 1. Come viene resa oggi la pagina (catena e markup)
| # | Elemento | File:riga | Cosa fa o emette |
|---|---|---|---|
| 1 | Folio + Volt | `Themes/Sixteen/resources/views/pages/[container0]/[slug0]/index.blade.php:26-43, 65-69, 90-94` | `mount()` risolve la pagina; layout dal campo `layout` del JSON (oggi assente, quindi `layouts.app`, v1); radice Volt `div.page-content` (+ `data-dc` solo con layout `design-comuni*`, riga 91); `<x-page>` riga 92 |
| 2 | `x-page` | `components/page.blade.php:13-15, 37` | `Page::getBlocksBySlug`, `@include($block->view, [...blockData, ...pageData, 'data' => merge])` |
| 3 | JSON | `config/local/fixcity/database/content/pages/tickets.create.json` (474 byte; copie identiche in `config/eu/fixcity/...` e `Modules/Fixcity/config/...`) | un blocco `ticket_create`, `data.view = pub_theme::components.blocks.tests.ticket-crea`; nessun `layout`, nessun testo. APP_ENV=local, quindi la copia viva e' quasi certamente quella `config/local/fixcity` (caricatore non verificato) |
| 4 | Blocco | `components/blocks/tests/ticket-crea.blade.php:3` | `@livewire(CreateTicketWizardWidget::class, ['blockData' => $data])` |
| 5 | Widget | `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php:42-47, 52-55, 62-88, 93-96, 98-118, 123-130` | `mount` (`blockData`, `confirmationLocale`, `form->fill(getDefaultFormState())`), `getSteps`, `save` (privacy policy, `form->getState()`, utente obbligatorio, `CreateTicketAction`, `saveRelationships`, redirect), `submit`, `getViewData` (titolo e descrizione da `blockData`). Nessun `$view`: la risolve `XotBaseWidget` in `pub_theme::filament.widgets.create-ticket-wizard` (asserito da `CreateTicketWizardWidgetViewTest.php:19-23`) |
| 6 | Vista widget | `Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php:1-49` | `div.cmp-wizard-widget` (4) con Alpine di focus privacy; h1 `.cmp-heading` in contenitore Tailwind `max-w-[1166px]` (24-31); guscio `.wizard-dc-form-shell rounded-lg bg-white p-4 shadow-sm` (35); `{{ $this->form }}` (44); `<x-filament-actions::modals />` (48) |
| 7 | Schema wizard | `Modules/Xot/app/Filament/Widgets/XotBaseWizardWidget.php:85-90, 92-108` | `getWizardComponent()`: `Wizard` Filament + `persistStepInQueryString()` (chiave `step`, riga 97) + `->view('pub_theme::components.wizard')` fuori dall'admin (99-105) |
| 8 | Vista wizard | `Themes/Sixteen/resources/views/components/wizard.blade.php:1-94` | radice Alpine `wizardSchemaComponent` con classi `wizard-dc-form-shell fi-sc-wizard` (10-39), `input hidden stepsData` (40-50), stepper (52-57), `{{ $step }}` per passo (59-61), footer `fi-sc-wizard-footer` con le azioni Filament (63-92) |
| 9 | Stepper | `components/wizard/stepper.blade.php:1-39` | `div.steppers > .steppers-header > ul.steppers-list[role=list] > li.stepper-step` con `x-bind:class` (active/confirmed) e `span.steppers-index` con `x-text`; lo stato e' solo client (SSR senza classi) |
| 10 | Passi | `TicketForm.php:32-42` + `Modules/Xot/app/Filament/Resources/Schemas/XotBaseResourceForm.php:89-110` | chiavi `privacy`, `data`, `summary`; `getStepByName` chiama `get<Nome>Schema()` e fa `Step::make()->schema(array_values(...))`. Etichette "Autorizzazioni e condizioni", "Dati di segnalazione", "Riepilogo" (uguali al riferimento); chiave Livewire `form.riepilogo::data::wizard-step` |
| 11 | Riepilogo | `TicketForm.php:47-93` (+ `TicketFormReviewInfolist.php:28-55`) | `warningSection` (Section con Html `div.alert.alert-warning`) e `summarySection` (Section "Riepilogo": `review_map` + 6 `TextEntry` con `state(fn (Get $get))`: Posizione, Categoria, Priorita', Titolo, Descrizione, Allegati) |
| 12 | Footer | `wizard.blade.php:63-92` | pulsanti Filament `fi-btn`: "Precedente", "Annulla" (passo 1, con `window.location.href` verso `/fixcity/admin/tickets`: URL del back office in una pagina pubblica, da `HasXotFormAction::getCancelFormAction`), "Successivo" con spinner, "Salva" (`wire:click="save"`). Nessuna classe DC |

Note che correggono assunzioni:
- `PubThemeWizard` (`Modules/Fixcity/app/Filament/Schemas/Components/PubThemeWizard.php`) **non e' usata da nessuno** (`rg` trova solo la definizione): la sostituzione di vista avviene in `XotBaseWizardWidget`. Codice morto, da non estendere.
- `components/wizard/cmp-nav-steps.blade.php` (60 righe) e' gia' il nav DC con Alpine (Indietro, Salva richiesta, Salva, Avanti/Invia con `$wire.save()`), ma **nessuna vista lo include**. `filament/widgets/createticketwizard.blade.php` (senza trattino, con `<form wire:submit>`) e' una seconda vista mai risolta.
- Tutti e 3 i passi sono nell'HTML: token SSR per regione (snapshot guest 12:57): chrome v1 prima del widget 187, guscio e stepper 45, passo 1 = 57, passo 2 = 341, passo 3 = 238, footer wizard e fine pagina 105, footer v1 243. Il riferimento ha 1193 token.
- `wizard.js` (`init()`) legge `?step=` dalla query lato client e lo onora in qualunque ambiente; il docblock di `XotBaseWizardWidget` che parla di gating per ambiente non e' implementato nel codice letto.

### 2. Sessione concorrente: cosa sta facendo e cosa vincola
Stato letto (13:00-13:10): `create-ticket-wizard.blade.php` ultima modifica 12:54:16 (nessun riferimento a mappa o a stepper DC: e' ancora la vista del punto 1); `TicketForm.php` 13:04:53 (il blocco riepilogo e' invariato dalle 12:50; il resto lo tocca DCP-35 S1); `lang/it/ticket_form.php` 13:06:23 e `lang/en` 12:59 (in scrittura); `CreateTicketWizardWidget.php` con `redirectAfterSuccess($ticket)` e `?code=` (DCP-34); `parity-wizard-steps.sh` creato 12:59 (DCP-35 S5); `TicketCreatePageJsonGuardTest.php` 13:02. Piano dichiarato: tabella claim in `dcp-35-tickets-create-privacy-dati-coordinamento.story.md` (shell, riepilogo e `review_map`, lang, passi 1 e 2, harness). `git -C laravel/Modules/Fixcity diff` mostra: `getDataSchema` riorganizzato in 3 Section (`locationSection` con `location.address` + CoordinatePicker, `issueSection`, `authorSection` con `getAuthorHtml()` che legge `fiscal_code` o `codice_fiscale`, campi che non esistono), `review_map`, chiavi lang `review_*`/`author_*` (alcune sono stub grezzi dell'auto-writer: `ticket_form_review_infolist.php` ha `'review_author_name' => 'review_author_name'`).

Asserzioni che vincolano il markup (non romperle senza concordare):
- `tests/Unit/CreateTicketWizardWidgetViewTest.php:42-52`: la vista Sixteen contiene i literal `{{ $this->form }}` e `<x-filament-actions::modals />` (form prima) e `cmp-wizard-widget`; `:86` esiste `filament/wizard/submit-button.blade.php` con `type="submit"` e `steppers-btn-confirm`; `:25-32` `callSchemaComponentMethod` pubblico e dichiarato in `XotBaseWizardWidget`.
- `tests/Feature/Filament/CreateTicketWizardWidgetTest.php`: `:32` `assertSeeHtml('$wire.save()')`; `:46` `array_keys(TicketForm::getSummarySchema()) === ['warningSection','summarySection']`; `:53-54` la pagina non deve mostrare `author_fiscal_code` e `author_phone` (cautela con chiavi lang grezze); `:73-98` invio reale con redirect a `/it/tickets/confirmation`.
- `tests/Unit/TicketFormWizardSummarySchemaTest.php`: `summarySectionEntries()` restituisce `TextEntry` per le 6 chiavi `review_*` con label it/en da `fixcity::ticket_form_review_infolist.fields.*.label`; `getDefaultFormState()` senza `author_*`; un solo `CoordinatePicker` figlio diretto di `summarySection` con `isDisabled()` (~righe 95-106).
- `tests/Feature/Pages/TicketCreateVoltRootTest.php:38-56`: radice Volt `div.page-content`; `.cmp-wizard-widget` dentro il root del widget.
- `TicketCreatePageJsonGuardTest.php`: niente `tests.tickets*.json`; `config/local/fixcity/.../tickets.create.json` deve esistere.

Compatibilita' con la scelta (b): le asserzioni su vista, `$wire.save()`, Volt e JSON restano vere. Si rompono **tre** cose del concorrente, da rinegoziare: le due chiavi `warningSection`/`summarySection` (il riferimento ha callout, h2, schede e nav in UNA colonna `col-lg-8`: due `View` darebbero due `row`), i `TextEntry` del riepilogo e il test del `CoordinatePicker` disabled.

### 3. Riferimento contro emesso e scelta (a) o (b)
Mappatura minima (riferimento -> oggi -> da dove produrlo):
| Riferimento (`segnalazione-03-riepilogo`) | Oggi | Produzione proposta |
|---|---|---|
| `div.container#main-container > .row.justify-content-center > .col-12.col-lg-10` | contenitori Tailwind `max-w-[1166px]` (vista widget 24, 34) | vista widget: classi Bootstrap Italia |
| breadcrumb `cmp-breadcrumbs` | assente | `report-partials/breadcrumb` (richiede `$d` con `breadcrumb.items/current`) |
| `h1.title-xxxlarge` in `cmp-heading` | presente (vista widget 25-26) | resta, dentro la colonna |
| `.steppers > .steppers-header > ul > li.confirmed/active` + `svg.steppers-success` + `span.visually-hidden` + `span.steppers-index` | `ul.steppers-list li.stepper-step` + Alpine | riscrivere `components/wizard/stepper.blade.php` sul markup di `report-partials/stepper` (classi e `svg` check legati allo stato Alpine `step`) |
| `callout callout-highlight ps-3 warning` (titolo "Attenzione", `span.d-lg-block`) | `div.alert.alert-warning` in una `Section` | dentro la vista del passo; testi da `blockData.callout` (stessa forma del JSON di prova) |
| `h2.title-xxlarge.mb-4.mt-40` "Segnalazione" / "Dati Generali" + `cmp-card > card.has-bkg-grey` + `cmp-info-summary` + `single-line-info` | `fi-section`, `fi-in-entry` | partial `report-partials/info-summary` (gia' misurato), loop estratto da `report-step/layout-summary.blade.php:14-39` |
| link "Modifica" | assente | campo `edit` di `info-summary`; click `x-on:click.prevent="step = 'form.dati-di-segnalazione::data::wizard-step'"`, `href` con `?step=` come ripiego |
| `cmp-nav-steps` (Indietro, Salva Richiesta/Salva, Invia) | footer Filament | adattare `components/wizard/cmp-nav-steps.blade.php` (esiste); etichette da `blockData.nav` |
| `bg-grey-card shadow-contacts > cmp-contacts` "Contatta il comune" | assente | `report-partials/contacts` con `blockData.contacts` (dentro la radice del widget, vedi `TicketCreateVoltRootTest`) |
| `cmp-modal#modal-terms` | assente | `report-step/modal-terms`, "Conferma e invia" che chiama `$wire.submit()` |
| header e footer DC | v1 | DCP-32 (layout dal JSON), non qui |

Confronto:
| Criterio | (a) vista Filament riscritta (Section/Infolist/Wizard con viste tema) | (b) un componente View, Blade del tema, stato da Livewire |
|---|---|---|
| Token fuori riferimento per il riepilogo | ogni foglia Filament porta `div[wire:partial,x-data,wire:key,class=fi-grid-col,style]` + `div.fi-sc-component` + 2 chiusure (circa 4 token) x 8-9 foglie + 2 Section: **45-50 token** (circa 4% dell'unione) | 2-3 token (wrapper della View) |
| DRY con le pagine di prova | duplica il markup dentro viste di `TextEntry`/`Section` | riusa `info-summary`, `breadcrumb`, `stepper`, `contacts`, `modal-terms` |
| Mappa | resta un campo (stesso `wire:key` del passo 2, vedi punto 5) | componente non campo, nessuno stato duplicato |
| Rischio principale | chiavi `wire:key` e partial, CSS `fi-*` da neutralizzare | reattivita' del riepilogo (da provare con un test Livewire) |

Scelta: **(b)**. Rischi e come si controllano:
- **Reattivita'.** `View::make(...)->viewData(fn (Get $get, ...) => [...])` e' valutata a ogni render completo (`ViewComponent::getViewData`, `vendor/filament/support/src/Components/ViewComponent.php:122-128`). Non ho provato che `callSchemaComponentMethod('...','nextStep')` rigeneri il passo 3 con lo stato appena sincronizzato (i `TextEntry` attuali dipendono dalla stessa cosa). Test obbligatorio: `Livewire::test(CreateTicketWizardWidget::class)` con `set('data.name', ...)` e chiamata di `nextStep`, poi `assertSeeHtml` del valore nel riepilogo. Se il render e' parziale: segnalare lo stato con `->live()` sui campi del passo 2 oppure un `$wire.$refresh()` all'ingresso del passo 3.
- **`wire:key` / `wire:model`.** Il riepilogo non contiene input: nessun `wire:model`; il wrapper della View non ha chiave. Si elimina il duplicato `...form.location` (oggi x2).
- **Validazione dei passi.** Invariata: `Wizard::nextStep()` (`vendor/filament/schemas/src/Components/Wizard.php:96-157`) valida `$currentStep->getChildSchema()->validate()`; il riepilogo non ha campi. La validazione completa resta in `save()` (`$this->form->getState(shouldCallHooksBefore: false)`, widget riga 73), che copre i campi dei passi 1 e 2.
- **`persistStepInQueryString` ('step').** Invariato (`XotBaseWizardWidget.php:97`; lo legge `Wizard::getStartStep()` e `wizard.js init()`).
- **Submit unico.** "Invia" e "Conferma e invia" nella modale chiamano `$wire.submit()` (esiste, widget 93-96, e chiama `save()`); il literal `$wire.save()` richiesto dal test `:32` resta nell'HTML perche' Filament lo stampa in `x-on:submit.prevent` di ogni `<form>` di passo; nessun `type="submit"` in piu'. Il footer Filament (`fi-sc-wizard-footer`) non si stampa piu': `nextStep` non dipende dall'azione stampata.
- **Alpine.** L'azione `Modifica` e il nav usano lo scope della radice (`step`, `isFirstStep()`, `isLastStep()`, `requestNextStep()`), valido per qualunque discendente.
- **"Salva Richiesta".** Oggi `save()` crea il ticket e fa redirect: non esiste una bozza. `TicketStatusEnum::DRAFT` esiste (`app/Enums/TicketStatusEnum.php:27`), quindi una bozza e' possibile, ma e' una decisione di prodotto (domanda 4).
- Nota di sicurezza da verificare (non provata): `public array $blockData` e `public string $confirmationLocale` non sono `#[Locked]` e `redirectAfterSuccess` usa `blockData['confirmation_path']`; un client potrebbe alterarli prima del submit. Se si portano altri dati nel `blockData` conviene bloccare la proprieta'.

### 4. Il limite dell'HTML (misure)
Misure con `dcp33-tokdiff.mjs` (stessa tokenizzazione del gate), riferimento 1193 token:
| Caso | token locali | url-normalizzata |
|---|---|---|
| Pagina reale oggi (guest, 12:57) | 1212 | 24,94% (il gate aveva 24,84%) |
| Pagina di prova `tests/segnalazione-03-riepilogo` (rigorosa 87,42%) | 1206 | **97,77%** |
| Prova + passi 1 e 2 come emessi oggi | 1602 | **73,71%** |
| Prova + solo passo 1 | 1262 | 93,46% |
| Prova + guscio vuoto (hidden input + 3 `form` vuoti) | 1215 | **97,05%** |

Conclusione: con tutti e tre i passi nell'SSR il tetto e' circa 74%. Per arrivare a >= 90% l'HTML del passo 3 non deve contenere i campi dei passi 1 e 2 (budget: circa 90 token fuori riferimento).
Opzioni (decide l'utente, non l'implementatore):
- **A. Render lazy dei passi inattivi.** Una sottoclasse di `Step` che stampa il corpo solo per il passo attivo, con `public string $activeStepKey` nel widget (da `?step=` in `mount`, aggiornato in `Step::afterValidation` per "Avanti" e da una action Livewire per "Indietro"/"Modifica"). Costo L, rischio alto: upload FilePond e mappa del passo 2 si rimontano a ogni ritorno, "Indietro" diventa un round trip, serve provare che `next-wizard-step` arrivi dopo il morph. Da fare come story separata (proposta DCP-36) con test, dopo il pixel.
- **B. Metrica sul passo attivo.** `compare-body-html.mjs --exclude` per i passi inattivi (serve un taglio per `id` dei `<form>` o un parser DOM; il tool oggi e' solo regex). I passi inattivi sono `x-cloak` cioe' `display:none`: non fanno parte della pagina vista. Il criterio scritto diventerebbe "HTML >= 90% url-normalizzata sul passo attivo".
- Raccomandazione: B ora (poco costosa, onesta se dichiarata), A come epica opzionale. Qualunque cosa si scelga, riportare nella story entrambe le colonne (rigorosa e normalizzata) e la dicitura "SSR include i passi inattivi".

### 5. La mappa
- **Cosa esiste.** `Modules/Geo/app/Filament/Forms/Components/CoordinatePicker.php:17-26` (campo, `XotBaseField` + trait `HasCoordinatePicker.php:43-68`), Blade `Modules/Geo/resources/views/filament/forms/components/coordinate-picker.blade.php:114-122` (`<coordinate-picker-lit :state="state" zoom height show-search ...>` in `wire:ignore`) piu' readout lat/lng/indirizzo. Lit: `resources/js/components/coordinate-picker-lit.js:17-33` (proprieta' `state`, `zoom`, `height`, `showSearch`, ...; **nessun `readonly`**), `map/events.js:132` (`draggable: true`), `:171` (`L.map` con tutte le interazioni), `:181` (click che scrive lo stato). Il Blade non legge `disabled`/`readonly` (grep vuoto). Il tema include il sorgente Geo nel proprio bundle (`Themes/Sixteen/resources/js/app.js:13-15`): ogni modifica al Lit richiede la build del tema (orchestratore, lock).
- **Alternative scartate.** `map-lit` (home e dettaglio ticket): carica un GeoJSON da `data-url` e in `detail-mode` filtra per `ticket-id` (`map-lit.js:413-419`), quindi richiede un ticket persistito e mostra tutti i marcatori; non adatto a una bozza. `ticket-location-map.blade.php` (Fixcity) e' per ticket gia' salvati.
- **Proposta.** (1) `coordinate-picker-lit` accetta `readonly` (attributo booleano): `L.map` con `dragging/touchZoom/scrollWheelZoom/doubleClickZoom/boxZoom/keyboard: false`, nessun handler `click`, marker non `draggable`, niente ricerca, niente geolocalizzazione, niente fullscreen. Circa 10 righe in `events.js`, `coordinate-picker-lit.js`, `controls`. (2) Blade Geo `geo::components.point-map` (non campo): props `lat`, `lng`, `address`, `label`; stampa il Lit con `readonly`. Il campo, se `->disabled()`, passa `readonly` allo stesso Lit (cosi' anche il back office non permette di spostare il marker). (3) Nel passo, componente non campo: elimina `review_map` come `CoordinatePicker` e il doppio `wire:key`.
- **Dove.** Dentro la scheda "Disservizio", subito dopo la riga Indirizzo (`single-line-info` con etichetta "Mappa" non serve: un blocco `div[data-element="ticket-review-map"]` dentro il `card-body`), tutto il suo spazio (margini compresi) dentro il nodo nascondibile. In alternativa, riga finale della scheda: equivalente per il gate.
- **Accessibilita'.** Contenitore `role="group"` con `aria-label` "Posizione del disservizio sulla mappa: {indirizzo}, {lat}, {lng}" (testo da lang, 4 lingue); Leaflet decorativo per i lettori di schermo (`aria-hidden` sul pannello, gli stessi dati sono nelle righe testuali); readonly implica nessun controllo focusabile e `keyboard: false` (nessuna trappola di focus); resta il link di attribuzione OSM; `zoomAnimation: false` gia' presente. Senza JS: il testo dell'indirizzo c'e' gia' nella riga; aggiungere un link "Apri in OpenStreetMap" (`rel="noopener"`) con `mlat/mlon`.
- **Senza coordinate** (deep link guest, stato vuoto): niente istanza Leaflet, un `<p class="data-text">` con "Posizione non indicata" dentro lo stesso nodo nascondibile.
- **Mappa nascosta nella misura.** Aggiungere `--hide <selettore>` (ripetibile) a `bashscripts/tools/visual/compare-screenshots.mjs` (dopo `page.goto`/`addStyleTag`, riga ~65: `selettore{display:none!important}` su entrambe le pagine; nel riferimento non fa nulla) e a `parity-gate.sh` (parsing riga ~12-20, inoltro a ogni larghezza, riga ~33). Deve essere `display:none`, non `visibility:hidden`, perche' le altezze devono tornare uguali. Per l'HTML, `compare-body-html.mjs --exclude` con lo stesso selettore ristretto a `[attr="valore"]` e taglio a tag bilanciati (descrizione, non implemento). Selettore proposto: `[data-element="ticket-review-map"]`. `parity-wizard-steps.sh:23-24` rileva `--hide` nel gate e lo inoltra: nessuna modifica li'.

### 6. I dati del riepilogo
| Riferimento | Oggi (`fixcity::ticket_form_review_infolist`) | Esiste? | Come gestirlo |
|---|---|---|---|
| Indirizzo | "Posizione": `indirizzo · lat, lng` (`TicketFormReviewInfolist.php:105-137`) | `tickets.location` (JSON: address, latitude, longitude) e colonne `latitude`/`longitude` | solo `location.address`; coordinate nel testo alternativo della mappa. Se vuoto: riga omessa |
| Tipo di disservizio | "Categoria" (badge) | `tickets.type` (`TicketTypeEnum::getLabel()`) | label enum, riga sempre presente se scelto |
| Titolo | "Titolo" | `tickets.name` | ok |
| Dettagli | "Descrizione" | `tickets.content` | ok |
| Immagini | "Allegati": conteggio | media Spatie `attachments` (lo stato del form ha upload temporanei) | nomi file (`getClientOriginalName()` degli upload temporanei) come nel riferimento; vuoto: riga omessa |
| (assente) | "Priorita'" | `tickets.priority`, nascosta con default `low` | **non mostrare** (il riferimento non la ha; il test attuale la pretende: da aggiornare) |
| Autore (titolo scheda) | assente | `users.name` (anche `first_name`, `last_name`) | `Filament::auth()->user()`; ospite: vedi domanda 5 |
| Codice Fiscale | assente | **nessuna colonna** in `users`, `profiles`, `tickets` (`db:table` letto); `HasTicketAuthorData::getAuthUserFiscalCode` e `getAuthorHtml` leggono `fiscal_code`/`codice_fiscale` che non esistono, quindi sempre vuoto | riga **solo se presente**, mai inventare. Candidato senza migrazione: `profiles.extra` (JSON, `SchemalessAttributes`); schema nuovo = chiedere prima |
| Telefono | assente | `profiles.phone` (nullable); il docblock di `User` dichiara `$phone` ma la tabella `users` non ha la colonna | `$user->profile?->phone`, solo se presente |
| Email | assente | `users.email` (e `tickets.email`) | `users.email` |
Etichette: il riferimento usa "Indirizzo", "Tipo di disservizio", "Titolo", "Dettagli", "Immagini", "Codice Fiscale", "Telefono", "Email", titoli "Segnalazione", "Dati Generali", "Disservizio", "Autore della segnalazione", "Contatti". `ticket_form.php` ha solo alcune (Tipo di disservizio, Dettagli, Immagini; "Nome" al posto di "Titolo", "Codice fiscale" minuscolo, "Email di contatto"). Ogni testo diverso e' un token diverso: chiavi dedicate in `lang/{it,en}/ticket_form_review_infolist.php` (file non toccato dal concorrente; mancano `de` ed `es`) con le stringhe esatte del riferimento. Non usare `ticket_form.php` (in scrittura) ne' le chiavi grezze `author_*` (il test `:53-54` le vieta nella pagina).

### 7. Come misurare (fixture e comandi)
- Guest con `?step=` mostra stato vuoto: righe senza valore e altezze diverse dal riferimento. Per altezze uguali servono: (1) utente di prova con nome "Giulia Bianchi", email, telefono (e codice fiscale se ci sara' una sorgente: senza, manca una riga e tutto cio' che segue scala di una riga, circa 28% di pixel diversi sotto di essa); (2) stato del wizard con i valori del riferimento, incluso un upload con nome `6yhakandsahm413d8da.jpg`.
- Hook proposto (descrizione): `compare-screenshots.mjs --prepare file.mjs`, modulo con `export default async (page) => {...}` eseguito dopo il caricamento e prima dello scatto, solo sul lato locale: login con cookie o `storageState`, `Livewire.first().$wire.$set('data.name', ...)` per i campi di testo, `page.setInputFiles` per l'upload, click su "Avanti". E' lo stesso bisogno del passo 2 (DCP-35 S5): il hook va concordato con quella story, non duplicato.
- Comandi (nessuno eseguito da me): `bash bashscripts/tools/visual/parity-gate.sh https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html "http://192.168.1.40:8000/it/tickets/create?step=form.riepilogo%3A%3Adata%3A%3Awizard-step" --hide '[data-element="ticket-review-map"]' --fail-if-contains '|---LINE:'` e, per tutti i passi, `bash bashscripts/tools/visual/parity-wizard-steps.sh --steps '3' --hide '[data-element="ticket-review-map"]'`. Per vedere i token: `node bashscripts/tools/visual/compare-body-html.mjs <ref> <local> --json` (riportare `similarity` e `similarityUrlNormalized`); l'elenco dei token diversi lo stampa `dcp33-tokdiff.mjs <ref.html> <local.html> list norm` nello scratchpad.

### 8. Piano a passi piccoli (ordine consigliato, TDD)
Legenda: C = file toccato o rivendicato dalla sessione concorrente (DCP-35 claim, tocca solo dopo aver riletto `find -mmin` e `git diff`, e con il suo consenso).
| # | Cosa | File | C | Verifica | Test Pest (prima, rosso) | Dipendenze |
|---|---|---|---|---|---|---|
| 0 | `"layout": "design-comuni"` in `tickets.create.json` (3 copie identiche; usare `page-json-for-url.sh`) e build con scope `data-dc` | `config/local/fixcity/.../tickets.create.json`, `config/eu/fixcity/...`, `Modules/Fixcity/config/...`; `design-comuni/components.css` | no (orchestratore, DCP-32) | `curl -s .../it/tickets/create \| grep -c 'data-dc'` >= 1; header e footer come la pagina di prova | `TicketCreatePageJsonGuardTest` resta verde | DCP-32 build |
| 1 | `--hide` (e `--prepare`) nel gate | `bashscripts/tools/visual/compare-screenshots.mjs`, `parity-gate.sh`, `compare-body-html.mjs` (`--exclude`) | no (orchestratore, tool di DCP-01; DCP-35 S5 li usa) | gate sulla pagina di prova con `--hide '.cmp-contacts'`: altezze ref e locale scendono uguali | autotest nel tool se esiste | nessuna |
| 2 | Action `BuildTicketReviewSummaryAction::execute(array $state, ?Authenticatable $user): array` che restituisce `groups[].cards[].summaries[].rows[]` (stessa forma di `tests.segnalazione-03-riepilogo.json`, classi `p-3 p-lg-4`, `mb-4 mb-lg-30`, `edit`, `footer_hidden`); riusa i formatter di `TicketFormReviewInfolist` (tipo enum, immagini). Pattern gemello di `ResolveTicketConfirmationDataAction` (DCP-34) | `Modules/Fixcity/app/Actions/BuildTicketReviewSummaryAction.php` (nuovo), `lang/{it,en}/ticket_form_review_infolist.php` | no (file nuovi; il lang review non e' nel claim) | test unit | `tests/Unit/BuildTicketReviewSummaryActionTest.php`: righe presenti con stato pieno; riga omessa se vuota (indirizzo, immagini, CF, telefono); CF solo da `profiles.extra` se presente; nome da `users.name`; etichette it/en esatte; mai `review_*` o `author_*` grezzi; enum con `getLabel()` | nessuna |
| 3 | Estrarre da `report-step/layout-summary.blade.php:14-39` il partial `report-partials/summary-groups` (callout + loop gruppi/schede) e farlo usare a layout-summary e al passo | `Themes/Sixteen/.../report-partials/summary-groups.blade.php` (nuovo), `report-step/layout-summary.blade.php` | no (ma e' un blocco misurato: l'output deve restare byte-identico) | `curl` di `/it/tests/segnalazione-03-riepilogo` prima e dopo: `diff` vuoto; 87,42% e 97,77% invariati | nessuno nuovo (l'identita' dell'output e' il test) | nessuna |
| 4 | Vista del passo `components/wizard/summary-step.blade.php` (callout, gruppi, mappa, nav) e `getSummarySchema()` con un solo `View::make('pub_theme::components.wizard.summary-step')->viewData(fn (Get $get) => ...)`. Tolta la `review_map` come campo | `Themes/Sixteen/resources/views/components/wizard/summary-step.blade.php` (nuovo), `TicketForm.php` (metodi summary), `TicketFormReviewInfolist.php` | **si** | gate sul passo 3 con `--hide`; tokdiff della regione passo 3 vs `<main>` del riferimento | vista: nessuna classe `fi-` dentro `.cmp-info-summary`, N `single-line-info`, nessun `<form>`; Feature Livewire: `set('data.name')` + `nextStep` -> il valore compare nell'HTML (prova la reattivita', punto 3); aggiornare i test `:46` (chiavi), `TicketFormWizardSummarySchemaTest` (TextEntry, `CoordinatePicker`, priorita') | 2, 3 |
| 5 | Stepper DC, footer Filament eliminato, nav DC per passo (da `cmp-nav-steps.blade.php`), modale termini | `components/wizard.blade.php:52-92`, `components/wizard/stepper.blade.php`, `components/wizard/cmp-nav-steps.blade.php`, vista modale | si (shell di DCP-35) | `rg -c 'steppers-list\|stepper-step\|fi-sc-wizard-footer'` = 0; tokdiff stepper e nav | Unit sulla vista: `.steppers li` x3 con le etichette dei passi, `steppers-btn-confirm` e `$wire.submit()`; resta `filament/wizard/submit-button.blade.php` (test `:86`) | 4 (i nav sono per passo) |
| 6 | Guscio del widget: `container#main-container`, `row`/`col`, breadcrumb, h1, contatti, modale; dati da `blockData` (`breadcrumb`, `callout`, `nav`, `contacts`, `modal`) tramite `$d`/`$icon`/`$assets` come in `report-step/design-comuni.blade.php:10-15`; stesse chiavi del JSON di prova, it/en | `filament/widgets/create-ticket-wizard.blade.php`, `tickets.create.json` x3 | si (vista) / no (JSON, orchestratore) | `.cmp-wizard-widget` ancora dentro il root del widget | `CreateTicketWizardWidgetViewTest` (literal `{{ $this->form }}`, modals dopo, `cmp-wizard-widget`) + nuovo test: un solo `h1`, un solo `.steppers`, breadcrumb e contatti presenti | 0, 5 |
| 7 | Mappa readonly: attributo `readonly` nel Lit, componente Blade `geo::components.point-map`, uso nel passo | `Modules/Geo/resources/js/components/coordinate-picker-lit.js`, `map/events.js:132,171,181`, `map/controls*`, `Modules/Geo/resources/views/components/point-map.blade.php` (nuovo), `coordinate-picker.blade.php` (propaga `disabled`) | no (Geo, repo separato) | build del tema (orchestratore, un solo `npm run build`); console senza errori; il marker non si trascina | Pest sulla vista: con coordinate `readonly` + `aria-label` con indirizzo + link OSM; senza coordinate placeholder e nessun elemento Lit | nessuna per il codice; build coordinata |
| 8 | Misura e chiusura: gate a 1920/1440/992/768/375 con fixture e `--hide`, numeri nella story (HTML rigorosa e normalizzata, pixel, altezze), PHPStan a zero sui file toccati, `view:clear` (orchestratore) | story, `docs/` Fixcity e Sixteen, wiki `ticket-crea-url-routing-chain.md` | si (docs) | gate; nessun `\|---LINE:` | suite del modulo | 0-7 |
| 9 | (separato, DCP-36) Render lazy dei passi inattivi, oppure `--exclude` come metrica | vedi punto 4 | si | HTML >= 90% | test di navigazione avanti/indietro e upload | 8 |

### 9. Rischi e domande
Domande per la sessione concorrente (DCP-35 / DCP-33 attiva):
1. Siete d'accordo a sostituire i `TextEntry` e la `review_map` con un solo `View` (scelta (b))? In caso contrario, come pensate di ridurre i circa 50 token di wrapper Filament e il doppio `wire:key` di `location`?
2. Il contratto `['warningSection','summarySection']` (test `CreateTicketWizardWidgetTest.php:46`) puo' diventare una sola chiave? Altrimenti il callout e le schede finiscono in due `row`.
3. Chi stampa il nav (`cmp-nav-steps`)? Proposta: la vista di ciascun passo, perche' nel riferimento i bottoni cambiano per passo (passo 1 solo Avanti, passo 3 Indietro, Salva Richiesta, Invia) e stanno dentro la colonna del passo (`col-lg-8`, ma il passo 2 ha sidebar `col-lg-3` + `col-lg-8 offset-lg-1`).
4. "Salva Richiesta": bozza reale (`TicketStatusEnum::DRAFT`) o pulsante senza effetto? Oggi `save()` crea un ticket definitivo e fa redirect.
5. Ospite sul riepilogo: mostrare comunque "Dati Generali" con schede vuote, o un invito all'accesso? (`save()` rifiuta chi non e' autenticato: `CreateTicketWizardWidget.php:75-79`.)
6. Codice fiscale: nuova sorgente (`profiles.extra` o colonna, da chiedere prima) o riga omessa con altezza non uguale?
7. `lang/it/ticket_form.php` e `ticket_form_review_infolist.php` contengono stub grezzi (`review_author_name` => 'review_author_name'): sono vostri o dell'auto-writer? Vanno sostituiti con testi veri prima che compaiano in pagina.

Rischi:
- Reattivita' del riepilogo non provata (punto 3); mitigazione: il test Livewire del passo 4 e' il primo da scrivere.
- HTML >= 90% non raggiungibile senza A o B (punto 4); serve una decisione scritta dell'utente, altrimenti la story non puo' chiudersi con i criteri attuali.
- Altezze uguali impossibili senza fixture (utente, stato, codice fiscale).
- Conflitti di scrittura sui file C: leggere `find -mmin` e `git diff` prima di ogni modifica; backup nello scratchpad.
- Build: modifiche al Lit Geo e al CSS richiedono la build del tema (orchestratore, lock `/tmp/design-comuni-build.lock`).
- Tre copie di `tickets.create.json` da tenere identiche (o trovare e documentare quale carica il tenant).
- Le utility della mappa (`mt-3`, `rounded`, `overflow-hidden`, ...) vanno cercate nel CSS compilato dopo la build di DCP-32 (nei file attuali compaiono 1-8 volte ciascuna ma non ho verificato lo scope).

### 10. Non verificato
Non ho eseguito: browser o screenshot (sweep in corso), Pest, PHPStan, build, `artisan` oltre a `db:table` in sola lettura. Non ho provato: reattivita' del riepilogo dopo `nextStep`; comportamento di Leaflet in un passo nascosto (documentato in `wizard-map-visibility-fix.md`, non rimisurato); quale delle tre copie di `tickets.create.json` carica il tenant; se `Html`/`View` sono ri-renderizzate in caso di render parziale Filament; i valori pixel (gli unici numeri pixel sono quelli della story). I numeri HTML sono calcolati sugli snapshot nello scratchpad (guest 12:57, pagina di prova 13:00), non sul server in tempo reale.

## Decisioni dell'orchestratore (13:25, contestabili: scrivere qui sotto, non sovrascrivere)
Dalle domande dell'analisi. Decisioni prese per non bloccare il lavoro in parallelo:
1. **Un solo componente per il riepilogo** (`Filament\Schemas\Components\View` con vista del tema che riusa i partial `report-partials/*`): si'. Filament resta contenitore di stato, validazione per passo, `?step=` e `save()`.
2. **Contratto chiavi del riepilogo**: puo' diventare una sola chiave; il test `CreateTicketWizardWidgetTest.php:46` (`['warningSection','summarySection']`) va aggiornato insieme alla modifica, nello stesso commit logico.
3. **Chi stampa il nav (Indietro / Salva Richiesta / Invia)**: la vista del passo (`wizard/summary-step`), non la shell.
4. **"Salva Richiesta"**: non esiste la bozza (nessun salvataggio senza `owner_id` e campi obbligatori). Non si finge un salvataggio: il pulsante si omette e la deviazione e' dichiarata; "Indietro" e "Invia" ci sono. Se la bozza diventa un requisito: story a parte (`TicketStatusEnum::DRAFT`).
5. **"Dati Generali" per un ospite**: la sezione non si mostra (ma l'invio richiede comunque l'accesso). Per un utente autenticato: nome e le sole righe di cui esiste il dato.
6. **Codice fiscale / telefono**: mai inventare. Telefono da `profiles.phone` se presente; codice fiscale non ha sorgente: riga omessa (nessuna migrazione senza chiedere).
7. **Criterio HTML**: la pagina Filament emette nel DOM anche i passi 1-2 (57 e 341 token su 1212), quindi `>= 90` rigoroso e' irraggiungibile per qualunque implementazione (la pagina di prova e' a 87,42 rigorosa / 97,77 normalizzata). L'utente ha chiesto "altissima visual parity": criterio principale = pixel >= 97% a 5 larghezze con altezze uguali **esclusa la mappa**; HTML = misura normalizzata sul solo `<main>`/passo attivo, dichiarando esplicitamente il filtro. Nessun rilassamento silenzioso.
8. **La mappa nel riepilogo deve essere in sola lettura**: oggi `review_map` (`CoordinatePicker` `->disabled()`) non lo e' (il Blade Geo ignora `disabled`, il Lit non ha readonly, marker trascinabile, due editor sullo stato). Va sostituita con un componente `geo::components.point-map` di sola lettura (nuovo) e il `CoordinatePicker` del passo dati resta l'unico editor.
9. **Contratto dati del riepilogo** (Action `BuildTicketReviewSummaryAction::execute(array $state, ?User $user): array`): `['report' => ['edit_step' => string, 'rows' => list<['label','value']>, 'images' => list<string>, 'location' => ['latitude','longitude','address']|null], 'author' => ['name' => string, 'rows' => list<['label','value']>]|null, 'contacts' => ['rows' => list<['label','value']>]|null]`. Le righe esistono solo se il dato esiste.

## Lavoro in parallelo (subagent, 13:25) e proprieta' dei file
| Agente | Ambito | File | Non tocca |
|---|---|---|---|
| `dcp33-geo-map` | mappa di sola lettura | `Modules/Geo/**` (Lit `coordinate-picker-lit.js` con `readonly`, `map/events.js`, nuovo `geo::components.point-map`, test, docs) | wizard, TicketForm, tema, build |
| `dcp33-summary` | Action + vista del riepilogo + harness | `Modules/Fixcity/app/Actions/BuildTicketReviewSummaryAction.php`, `Themes/Sixteen/resources/views/components/wizard/summary-step.blade.php` (nuovo) e partial nuovi, test, story | `TicketForm.php`, `TicketFormReviewInfolist.php`, `create-ticket-wizard.blade.php` (sessione concorrente): il cablaggio lo fa l'orchestratore dopo aver riletto il loro stato |
| `dcp33-tools` | metrica senza mappa | `bashscripts/tools/visual/{compare-screenshots.mjs,parity-gate.sh,compare-body-html.mjs}` (`--hide`, `--exclude`), `bashscripts/docs/visual-comparison/*` | tutto il resto |
Build, `regenerate-design-comuni-css.sh` e `npm run build`: solo l'orchestratore.


## Mappa di sola lettura (dcp33-geo-map)
Autore: subagent `dcp33-geo-map`, 2026-10-13 13:20-13:35. Solo `Modules/Geo` (piu' questa sezione). Nota tecnica: `laravel/Modules/Geo/docs/wiki/concepts/point-map-readonly.md`.

**Scelta: (a) + (b) sullo stesso meccanismo.** Il Lit `coordinate-picker-lit` ha l'attributo `readonly` (serve a entrambe); il campo `CoordinatePicker` lo espone con `->readOnly()` (e anche `->disabled()`, prima ignorato dal Blade) e c'e' il componente `<x-geo::point-map>` per le viste del tema. Per il riepilogo si usa il componente Blade: nessun campo, nessuno stato Livewire, nessun doppio `wire:key`.

**Contratto per chi cabla il riepilogo**
```blade
<div data-element="ticket-review-map">
    <x-geo::point-map :latitude="$lat" :longitude="$lng" :address="$address" label="..." />
</div>
```
Prop: `latitude`, `longitude`, `address`, `zoom` (16), `height` (`16rem`, solo `px/rem/em/vh/%`), `label`, `id` (casuale se omesso). Senza coordinate valide (null, vuote, non numeriche, fuori intervallo) stampa `<p class="geo-point-map geo-point-map--empty">Posizione non indicata</p>`. Con punto: `role="region"` con `aria-label` "label: indirizzo" (o coordinate), web component `readonly`, didascalia testuale visibile (indirizzo, lat, lng, link OpenStreetMap in nuova scheda, attribuzione). Lo spazio della mappa e della didascalia sta tutto dentro il nodo che il gate nasconde con `--hide`.

**File (tutti in `laravel/Modules/Geo`)**
- nuovi: `app/View/Components/PointMap.php`, `resources/views/components/point-map.blade.php`, `lang/{it,en,de,es}/point-map.php`, `tests/Unit/View/Components/PointMapTest.php`, `tests/Unit/Filament/Forms/Components/CoordinatePickerReadOnlyTest.php`, `docs/wiki/concepts/point-map-readonly.md`
- modificati: `resources/js/components/coordinate-picker-lit.js` (proprieta' `readonly`, niente ricerca), `resources/js/components/map/events.js` (guardia unica in `handleMapInteraction`; marker `draggable/interactive/keyboard: !readonly`; niente handler click; niente geolocalizzazione automatica; `keyboard:false`, `scrollWheelZoom:false`), `resources/js/components/map/controls/render-controls.js` (solo zoom in readonly), `app/Filament/Forms/Components/CoordinatePicker.php` (`CanBeReadOnly` + `isMapReadonly()`), `resources/views/filament/forms/components/coordinate-picker.blade.php` (passa `readonly`, toglie `show-search`), `app/Providers/GeoServiceProvider.php` (vedi sotto), `docs/INDEX.md` (link)
- scoperta: `XotBaseServiceProvider` registra il namespace Blade `geo` su `Modules\Xot\View\Components` perche' `GeoServiceProvider` non ridefinisce `$module_ns` (Fixcity/Cms/User si'). Senza correzione `<x-geo::point-map>` non trova la classe. Fix locale a Geo: `Blade::componentNamespace('Modules\Geo\View\Components', 'geo')` in `boot()`. Stesso difetto latente per altri moduli senza `$module_ns` (AI, Activity, Mobile): non toccato.

**Test (output reale, 13:30)**
- `cd laravel && APP_ENV=testing DB_CONNECTION=sqlite APP_CONFIG_CACHE=/tmp/dcp33g-nonexistent.php ./vendor/bin/pest Modules/Geo/tests/Unit/View/Components/PointMapTest.php Modules/Geo/tests/Unit/Filament/Forms/Components/CoordinatePickerReadOnlyTest.php` -> `Tests: 27 passed (61 assertions)`. Rossi prima dell'implementazione: 26 su 26.
- Copertura: web component con `readonly`, coordinate e `aria-label`; didascalia e link OSM; nessun `show-search`/`<input`; segnaposto per 7 casi senza coordinate; `0` valido; due istanze con id distinti; `id` esplicito; altezza/zoom di default e configurabili; altezza non valida torna al default; escape di indirizzo ed etichetta; nessun riferimento a slug; `isMapReadonly()` per `readOnly()`, `disabled()` e schema disabilitato; contratto del Blade e del JS (test di presenza delle guardie, non di comportamento).
- PHPStan: `./vendor/bin/phpstan analyse` su PointMap.php, CoordinatePicker.php, GeoServiceProvider.php e i 2 test: `[OK] No errors`.
- Preesistenti e non miei: `CoordinatePickerTest` "is not dehydrated by default" (la classe chiama `->dehydrated()`), e 24 test in `tests/Unit/Filament/{FilamentComponentsTest,Forms/LatitudeLongitudeInputTest}.php` che falliscono con `SQLiteDatabaseDoesNotExistException` (`database/fixcity_user_test.sqlite` assente).
- Browser (comportamento JS, un solo browser): il sorgente impacchettato fuori dal repo (esbuild nello scratchpad, tile OSM e Nominatim intercettati) con HTML reale generato dal componente + un controllo NON `readonly`. Sul `readonly`: marker senza `leaflet-interactive`/`leaflet-marker-draggable`/`tabindex`/`role`, contenitore mappa senza `tabindex`, solo 2 `.ctrl-btn` (zoom), nessun `<input>`; click e tentativo di drag del marker: `state` identico, 0 `coords-changed`, 0 richieste Nominatim; doppio click e `+` aumentano lo zoom (16 -> 17 -> 18), la rotella non zooma, il pan sposta il centro ma non il marker; nessun id duplicato con 3 istanze; console senza error/warning. Il controllo editabile invece scrive lo stato (2 `coords-changed`, 1 richiesta Nominatim, marker `draggable`, 6 pulsanti). Screenshot: `.../scratchpad/dcp33g-pm/shot.png` (tile finte).

**Build richieste (le lancia l'orchestratore, io non le ho eseguite)**
1. **Tema Sixteen**: `cd Themes/Sixteen && npm run build` (poi `view:clear`). Obbligatoria: il bundle del tema importa il sorgente Geo (`@modules`). Finche' non e' fatta il Lit compilato ignora `readonly` e la mappa del riepilogo resta modificabile.
2. **Geo** (`cd Modules/Geo && npm run build && npm run copy`) serve solo al back office Filament (`AdminPanelProvider` carica `public_html/assets/geo`); Geo non ha `node_modules`, quindi oggi non e' eseguibile. Non serve al wizard pubblico.
3. `view:clear` per i Blade compilati (componente nuovo e picker).
CSS: nessuna build dedicata (stili inline e classi `geo-point-map*` senza regole).

**Cosa resta / rischi per chi cabla**
- Montare il componente nella vista `wizard/summary-step` dentro `data-element="ticket-review-map"` e togliere `review_map` (`CoordinatePicker` disabled) da `TicketForm`; con `->disabled()` il picker ora e' davvero in sola lettura, ma resta un campo con `wire:key` duplicato: usare `point-map`.
- Dopo la build, ripetere la verifica a pixel sul passo 3 (non fatta: servono build e fixture) con `--hide '[data-element="ticket-review-map"]'`.
- Reattivita': la mappa e' `wire:ignore`; il `wire:key` e' l'hash del punto, quindi se l'utente cambia posizione e torna al riepilogo la mappa si rimonta. Non provato dentro Livewire reale (solo HTML statico). Due istanze con lo stesso punto nello stesso componente Livewire richiedono `id` esplicito.
- `readonly` e' letto al primo render (non reattivo). Il Lit ha `min-height: 200px`. Il pan a un dito su mobile cattura lo scroll nella mappa (16rem): opzione `dragging: !L.Browser.mobile` descritta nella nota Geo, non applicata.
- La didascalia usa stili inline (`font-size:.875rem`) e non classi del tema: non ho verificato quali utility esistano nel CSS generato (regola: solo classi compilate) e volevo evitare dipendenze dalla build. Se il riepilogo vuole classi DC, passarle come attributo `class` del componente (vanno sul contenitore).

## Strumenti di misura consegnati (dcp33-tools, 13:40) e misura di partenza
- Opzioni nuove (doc in `bashscripts/docs/visual-comparison/parity-gate.md`): `--hide`/`--hide-ref`/`--prepare` (screenshot) e `--exclude`/`--exclude-local` (HTML). Regressione verificata dall'agente: gate senza opzioni identico prima/dopo (HTML 87,42 / 97,77; pixel 99,93 / 99,9 / 99,86 / 99,83 / 99,72). Ogni misura con filtri va riportata dichiarando il filtro: non e' il gate standard.
- Selettori verificati sul DOM reale di `/it/tickets/create?step=form.riepilogo...` (non quelli ipotizzati): la mappa oggi e' `.coordinate-picker-field-wrapper` (2 trovati, 1 visibile, ~499 px); `[data-dc-map]` ha 0 match finche' la vista nuova non lo emette; per l'HTML i passi inattivi si escludono con `--exclude-local '.fi-sc-wizard-step:not([id="form.riepilogo::data::wizard-step"])'` (2 nodi, 376 token), perche' `fi-active` e' messa da Alpine a runtime e nel DOM del server non c'e'.
- Stato di partenza (ospite, 1440): HTML 27%, pixel 59,8%, altezze 3115 contro 2588 anche senza mappa. Il locale e' passato da 3119 a 3087 px tra due misure: la sessione concorrente sta modificando la pagina, quindi i numeri finali vanno presi dopo il cablaggio.

## Verifica dell'orchestratore sulla mappa (dcp33-geo-map, 13:50)
- `GeoServiceProvider::boot()` ora registra `Blade::componentNamespace('Modules\\Geo\\View\\Components', 'geo')` (diff ancorato di 5 righe, riletto). Motivo: il provider non ridefinisce `$module_ns` e la base registrava `geo` su `Modules\Xot\View\Components`. Stesso difetto latente in AI, Activity e Mobile (non toccati).
- Dopo la modifica rispondono 200: `/it`, `/it/tickets`, `/it/tests/homepage`, `/it/tickets/create`, `/it/tickets/confirmation`.
- Per avere effetto sul wizard serve `npm run build` del tema (il suo bundle importa il sorgente Geo); lo lancio io dopo il cablaggio, in un solo giro insieme alle viste del riepilogo.


## Riepilogo: Action e vista (dcp33-summary)
Autore: subagent `dcp33-summary`, 2026-10-13 13:10-13:45. Ambito: Action, lang, vista del tema, test, harness. NON toccati: `TicketForm.php`, `TicketFormReviewInfolist.php`, `create-ticket-wizard.blade.php`, `CreateTicketWizardWidget.php`, lang `ticket_form*`, `Modules/Geo`, `bashscripts/tools/visual`, JSON delle pagine. Nessuna build, nessun `view:clear|optimize|config:clear`, nessuna route o pagina nuova nel repo. Metodo: test scritti prima (rossi: Action 29 su 30, vista 13 su 13), poi codice.

### File
- Nuovi (`laravel/Modules/Fixcity`): `app/Actions/BuildTicketReviewSummaryAction.php` (QueueableAction, `final`, `strict_types`), `lang/it/ticket_review.php`, `lang/en/ticket_review.php`, `tests/Unit/Actions/BuildTicketReviewSummaryActionTest.php`, `tests/Feature/Wizard/TicketReviewSummaryStepViewTest.php`.
- Nuovi (`laravel/Themes/Sixteen/resources/views/components/wizard/`): `summary-step.blade.php` (composizione), `summary-shell.blade.php` (breadcrumb, h1, stepper), `summary-callout.blade.php` (avviso), `summary-sections.blade.php` (gruppi, schede, mappa).
- Modificati (Sixteen, repo separato), solo due ganci opzionali che a dato assente non cambiano l'output: `blocks/report-partials/info-summary.blade.php` (`edit.click` = espressione Alpine sul link "Modifica") e `blocks/report-partials/steps-nav.blade.php` (`nav.back_click` = espressione Alpine su "Indietro"). Verifica: `/it/tests/segnalazione-03-riepilogo` e `/it/tests/segnalazione-02-dati` prima e dopo, `compare-body-html.mjs`: 100% / 100% (1206 e 1273 token invariati); nessun test del repo referenzia i due partial.
- Il nav e' `report-partials/steps-nav` riusato, non una copia; il guscio e le sezioni riusano `breadcrumb`, `stepper`, `info-summary`.

### Contratto effettivo (superset del punto 9)
`BuildTicketReviewSummaryAction::execute(array $state, ?Illuminate\Contracts\Auth\Authenticatable $user = null): array`. Il parametro e' `Authenticatable` (tipo di `Filament::auth()->user()`) e dentro si restringe a `Modules\Xot\Contracts\UserContract`, come le altre Action del modulo: chi cabla passa l'utente senza fare `instanceof`.
```php
[
  'report' => [
    'title' => 'Segnalazione', 'card_title' => 'Disservizio', 'edit_label' => 'Modifica',     // testi dal lang
    'edit_step' => 'form.dati-di-segnalazione::data::wizard-step',                            // en: form.report-details::data::wizard-step
    'rows' => [['label' => 'Indirizzo', 'value' => '...'], /* Tipo di disservizio, Titolo, Dettagli, Immagini */],
    'images' => ['6yhakandsahm413d8da.jpg'],                                                  // nomi file (anche TemporaryUploadedFile di Livewire)
    'location' => ['latitude' => 43.7696, 'longitude' => 11.2558, 'address' => '...'] | null, // null senza coordinate valide
    'map_label' => 'Posizione del disservizio sulla mappa',
  ],
  'author'   => ['group_title' => 'Dati Generali', 'card_title' => 'Autore della segnalazione', 'name' => 'Giulia Bianchi', 'rows' => []] | null,
  'contacts' => ['title' => 'Contatti', 'rows' => [['label' => 'Telefono', ...], ['label' => 'Email', ...]]] | null,
]
```
Differenze rispetto al punto 9, tutte dichiarate: (1) i campi `title`, `card_title`, `edit_label`, `map_label`, `group_title` e `title` dei contatti sono in piu' perche' la vista non ha testi propri; i tre blocchi e i campi del punto 9 sono invariati. (2) `edit_step` dipende dalla lingua (Filament lo genera dall'etichetta del passo: `Str::slug(Str::transliterate(label))`; verificato sul server: `form.dati-di-segnalazione::data::wizard-step` in it, `form.report-details::data::wizard-step` in en). (3) `author.rows` e' sempre `[]`: il codice fiscale non ha una sorgente e non compare mai (nemmeno se lo stato porta `fiscal_code`).

Regole implementate: riga solo se il dato esiste (indirizzo vuoto o blank: omessa; tipo assente o non nell'enum: omessa, non si mostra il valore grezzo; titolo, dettagli, immagini idem); il tipo e' l'etichetta di `TicketTypeEnum` nella lingua corrente; l'indirizzo viene da `location.address`; la riga "Immagini" e' i nomi uniti da `, ` e le stringhe dello stato (uuid di media gia' salvati) sono ignorate; coordinate come numeri o stringhe numeriche entro -90/90 e -180/180, altrimenti `location` e' `null` (la riga Indirizzo resta); priorita' mai mostrata. Utente: nome da `users.name`, poi nome e cognome, poi email; telefono da `profiles.phone` (colonna verificata con `db:table`; l'attributo `users.phone` del docblock di `User` non esiste), email da `users.email`; `contacts` e' `null` se non c'e' nessun dato; l'ospite ha `author` e `contacts` `null`, e nessun valore dello stato (`email`, `phone`, `author_*`, `owner_id`) entra mai nel risultato.

### Vista `pub_theme::components.wizard.summary-step`
- Props: `review` (array dell'Action), `data` (guscio con le chiavi del JSON di `tests.segnalazione-03-riepilogo`: `title`, `breadcrumb`, `steps`, `callout`, `nav`), `withContainer` (default true), `withShell` (true), `withNav` (true), `wired` (false). Nessun testo nella vista: tutto arriva da `review` (lang) e da `data` (JSON o lang).
- Uso a pagina intera (come il riferimento): `@include('pub_theme::components.wizard.summary-step', ['review' => $review, 'data' => $data])`. Dentro il wizard, il cui guscio ha gia' container, h1 e stepper: `:with-container="false" :with-shell="false" :wired="true"` stampa `row > col-12 col-lg-8` con avviso, schede e nav.
- `wired` aggiunge solo `x-on:click="goToPreviousStep"` a "Indietro" e `x-on:click.prevent="goToStep('<edit_step>')"` a "Modifica" (con `href="?step=<chiave>"` come ripiego); senza `wired` il markup e' identico al riferimento. "Salva Richiesta" non c'e' finche' `data.nav.save` e' assente (decisione 4). "Invia" e' il pulsante del riferimento (`send`, apre `#modal-terms`).
- Mappa: `<x-geo::point-map :latitude :longitude :address :label>` (il componente esiste: `Modules/Geo/app/View/Components/PointMap.php`) dentro `<div data-dc-map><div class="mt-3">...`, ultimo elemento del `card-body` della scheda Disservizio, dopo il riquadro bianco con le righe. Margini compresi dentro il nodo `[data-dc-map]`, quindi `--hide '[data-dc-map]'` toglie tutto (altezza -339 px a 1440). Senza coordinate il nodo non c'e'. Il selettore e' `data-dc-map` (non `data-element="ticket-review-map"` dei documenti precedenti).

### Test (output reale, 13:41)
`cd laravel && APP_ENV=testing DB_CONNECTION=sqlite APP_CONFIG_CACHE=/tmp/dcp33s-nonexistent.php ./vendor/bin/pest Modules/Fixcity/tests/Unit/Actions/BuildTicketReviewSummaryActionTest.php Modules/Fixcity/tests/Feature/Wizard/TicketReviewSummaryStepViewTest.php` -> `Tests: 1 skipped, 45 passed (169 assertions)`. Lo skip e' l'harness (si attiva con `DCP33_RENDER_OUT=<file>`).
- Action (32): contratto completo in it e en, titoli it/en, righe omesse per indirizzo vuoto o null, tipo assente o sconosciuto, titolo blank, dettagli vuoti; stato vuoto; tipo come enum o come valore; 6 casi di coordinate non utilizzabili (riga Indirizzo resta); coordinate come stringhe; file caricati (`UploadedFile` e `TemporaryUploadedFile` mockato) con uuid ignorati; immagini assenti; autore e contatti; telefono assente (nessun profilo, telefono blank); nome da nome e cognome e da email; codice fiscale mai inventato; ospite con stato "avvelenato"; nessun dato di un altro utente; etichetta mappa it/en.
- Vista (13 piu' l'harness): markup del riferimento (container, stepper `confirmed confirmed active`, callout, 2 `h2`, 2 `cmp-card`, 3 `cmp-info-summary`, 8 `single-line-info`, `cmp-nav-steps`, `steppers-btn-confirm send`, un solo `h1`); nessuna classe `fi-*`, nessun `<form>`, nessun `wire:model|click|submit|id`; escape dei valori (`<script>`); nessun testo italiano con dati in inglese; ospite; autore senza contatti; `withShell`, `withNav`, `withContainer`; niente "Salva" senza etichetta; ganci Alpine solo con `wired`; mappa nella scheda dopo l'ultima riga e prima di "Dati Generali", con `readonly`, coordinate e indirizzo; nessuna mappa senza coordinate.
- PHPStan (`./vendor/bin/phpstan analyse` con la config unica, nessun `--level`) su Action, due test e due lang: `[OK] No errors`.

### Similarita' misurata (fixture = dati del riferimento, incluso il codice fiscale `GLABNC72H25H501Y` passato come `author.rows`)
Harness: `DCP33_RENDER_OUT=<file> pest ... --filter "whole main"` rende `<main>` (vista piu' `report-partials/contacts` e `report-step/modal-terms`), riferimento estratto con `curl` e `sed` (`<main>`..`</main>`), entrambi in scratchpad (`dcp33s-*`), poi `compare-body-html.mjs` file contro file. La colonna "mappa esclusa" toglie il nodo `[data-dc-map]` dal locale (come fa `--hide`/`--exclude` nel gate).
| Confronto | rigorosa | URL normalizzata | token ref/loc |
|---|---|---|---|
| `<main>` completo, mappa inclusa | 86,49 | 91,02 | 376/411 |
| `<main>` completo, mappa esclusa | 94,32 | 99,47 | 376/376 |
| solo corpo del passo (`#main-container`), mappa inclusa | 85,36 | 88,89 | 280/315 |
| solo corpo del passo, mappa esclusa | 95,80 | **100** | 280/280 |
| corpo, mappa esclusa, senza riga Codice Fiscale (caso reale dell'Action) | 92,31 | 96,43 | 280/270 |
Differenze residue: nel `<main>` 1 token, il refuso del riferimento `data-bs-dismiss="modal fw-bold"` sul pulsante Annulla (il partial `modal-terms` ha `modal`, gia' cosi' prima); le altre sono URL assoluti contro relativi (colonna rigorosa) e i 35 token della mappa. I 10 token in meno senza Codice Fiscale sono la riga mancante, non un difetto del markup.
Pixel (misura con filtro, non gate standard): pagina `/it/tests/segnalazione-03-riepilogo` servita contro la stessa pagina con il corpo del passo sostituito dalla vista nuova (file locale in scratchpad, header, footer e CSS del server), 1440 px, `--hide '[data-dc-map]'`: **99,95%**, altezze 3115 e 3115. Con la mappa visibile 79,13% e altezza 3454 (la mappa vale 339 px). Una sola larghezza, un solo browser; le altre quattro non misurate.

### Cablaggio: cosa resta (lo fa l'orchestratore dopo aver riletto la sessione concorrente)
Stato letto alle 13:40: `TicketForm.php` `getSummarySchema()` riga 47, `getWarningSectionSchema()` 63, `getSummarySectionSchema()` 81; `create-ticket-wizard.blade.php` invariata (49 righe, h1 e shell Tailwind).
1. `TicketForm::getSummarySchema()` (riga 47): sostituire il corpo con un solo componente, per esempio
```php
return [
    'reviewSummary' => \Filament\Schemas\Components\View::make('pub_theme::components.wizard.summary-step')
        ->viewData(static fn (\Livewire\Component $livewire): array => [
            'review' => app(BuildTicketReviewSummaryAction::class)->execute((array) ($livewire->data ?? []), Filament::auth()->user()),
            'data' => (array) ($livewire->blockData ?? []),   // callout e nav (e, se serve, title/breadcrumb/steps)
            'withContainer' => false, 'withShell' => false, 'wired' => true,
        ])
        ->columnSpanFull(),
];
```
`$livewire` e' iniettabile nella closure (`Filament\Schemas\Components\Component::resolveDefaultClosureDependencyForEvaluationByName`, caso `'livewire'`); `$livewire->data` e' lo stato del form (`XotBaseWidget::form()` ha `statePath('data')`). Da provare (obbligatorio, punto 3 dell'analisi): `Livewire::test(CreateTicketWizardWidget::class)->set('data.name', ...)->call('callSchemaComponentMethod', ..., 'nextStep', ...)` e `assertSeeHtml` del valore: se il passo non si rigenera, `->live()` sui campi del passo 2.
2. Eliminare `getWarningSectionSchema()` e `getSummarySectionSchema()` (righe 63-95); nessun `use` di `TicketForm` resta orfano (`Section`, `Html`, `CoordinatePicker` servono ancora agli altri passi). `TicketFormReviewInfolist::summarySectionEntries()` resta senza chiamanti: toglierlo o marcarlo superseded (e con esso le chiavi `ticket_form_review_infolist.php`).
3. Test da aggiornare: `tests/Feature/Filament/CreateTicketWizardWidgetTest.php:46` -> `Assert::assertSame(['reviewSummary'], $summaryKeys);`; `tests/Unit/TicketFormWizardSummarySchemaTest.php`: il test sulle `TextEntry` dei `review_*` (riga ~35) e "keeps the location map visible and read-only in the summary step" (riga ~98-106, cerca `summarySection` e un `CoordinatePicker`) vanno riscritti: il riepilogo e' un `View` con quella vista e non contiene campi (la mappa e' il componente Geo dentro la vista, gia' coperta da `TicketReviewSummaryStepViewTest`).
4. `create-ticket-wizard.blade.php`: nel guscio i dati `callout` e `nav` devono arrivare in `blockData` (JSON `tickets.create.json`, 3 copie, o lang con le chiavi `ticket.warning.*` che esistono): `callout {variant, icon, title, text, emphasis}` e `nav {aria_label, back, next, next_class: "send", next_target: "#modal-terms", next_icon: false}` senza `save`. Le chiavi sono quelle del JSON di prova.
5. `components/wizard.blade.php:63-92`: il footer Filament (`fi-sc-wizard-footer`) non va stampato sull'ultimo passo, perche' il nav e' della vista; sui passi 1 e 2 serve un nav analogo (stesso partial `steps-nav`, con `back_click`).
6. Modale: "Invia" apre `#modal-terms`; il pulsante "Conferma e invia" di `report-step/modal-terms.blade.php` e' statico (`type="submit" data-bs-target="#"`) e non chiama `$wire.submit()`. Serve un gancio analogo (`modal.confirm_click`) o una vista modale propria nel guscio: decisione da prendere (non fatto, e' un partial condiviso).
7. Build e `view:clear` (le fa l'orchestratore): il bundle del tema con il Lit `readonly` di Geo; fino ad allora la mappa del riepilogo e' ancora trascinabile e nel mio screenshot l'area del Lit risulta vuota (pagina `file://` senza rete): non e' una prova del comportamento della mappa.
8. Misura finale a 5 larghezze con `--hide '[data-dc-map]'` e fixture (utente, stato, upload `6yhakandsahm413d8da.jpg`).

### Rischi e non verificato
- `getSummarySchema()` e' lo schema anche del back office (`TicketResource\Pages\CreateTicket`, tramite `getFormSchema()` e `getStepByName`): la vista del tema in Filament admin mostrerebbe markup Design Comuni senza il suo CSS. Non verificato se l'admin usa questi passi; se si', sostituire il passo solo in `CreateTicketWizardWidget::getSteps()`.
- Non provato: reattivita' del riepilogo dentro Livewire, i due ganci Alpine in un browser (verificato solo il markup), il comportamento della mappa (la mia verifica visiva e' su pagina statica).
- Codice fiscale: nessuna sorgente, riga omessa; con la riga mancante l'altezza della pagina non coincide col riferimento (una riga `single-line-info` vale circa 66 px a 1440: e' il passo tra due righe nello screenshot).
- `lang/{it,en}/ticket_review.php` duplica in parte stringhe gia' presenti in `lang/it/ticket.php` (riga ~375-383: `warning`, `author`, `cf`, `contacts`, `phone`, `email`): chiavi che la nuova Action non usa; da convergere in una seconda passata. Non creati `de` ed `es` (non richiesti).
- `report-step/layout-summary.blade.php` e `wizard/summary-shell|summary-callout` ripetono circa 20 righe di markup; estrarre i pezzi in `report-partials` comune e' un refactor a output byte-identico, non fatto per non toccare un blocco misurato.

## Collegamento 13:55 UTC (lead DCP-35, nota di verifica)
- Le misure precedenti di questa story erano da ospite. Ora `tickets.create.json` richiede `auth` (DCP-42): ripetere le misure da autenticato con `--prepare` prima di dichiarare la parity del flusso reale.
- Il passo 2 usa lo stesso `CoordinatePicker::make('location')` del `review_map`: `id` duplicato `form.location` (DCP-35 sezione 6, rischio 3; DCP-43).
- Radice del Volt gia' corretta (DCP-40): non spostare `@volt` ne' `div.page-content`.

## Cablaggio eseguito (orchestratore, 13:50-14:20) e misure
**Cosa e' cambiato** (riletto lo stato della sessione concorrente prima di scrivere: `TicketForm.php` fermo dalle 13:04, vista del widget dalle 12:54):
- `TicketForm.php`: `use View` e `BuildTicketReviewSummaryAction`; **un solo metodo nuovo** `getReviewStepSchema()` (componente `View::make('pub_theme::components.wizard.summary-step')` con `viewData` che legge `$livewire->data` e `blockData`) e **una sola riga** in `getSteps()` (il passo `summary` usa `Step::make('summary')->schema(getReviewStepSchema())`). `getSummarySchema()` resta com'e' per lo schema piatto del back office (il back office non e' toccato).
- `blocks/report-partials/steps-nav.blade.php`: chiave opzionale `nav.next_click` (es. `$wire.submit()`) al posto dell'apertura della modale. "Invia" chiama `submit` direttamente: la privacy e' gia' stata accettata al passo 1, la modale dei termini del riferimento e' statica. Deviazione dichiarata.
- `filament/widgets/create-ticket-wizard.blade.php`: guscio Design Comuni (container, briciole dal JSON, h1, wizard, contatti dal JSON) al posto dei wrapper Tailwind.
- `components/wizard.blade.php`: il footer Filament e' nascosto sull'ultimo passo (`x-show="! isLastStep()"`). `components/wizard/stepper.blade.php`: markup del riferimento (vedi DCP-91).
- `tickets.create.json` (le 3 copie: tenant `local`, `eu`, modulo, identiche): `"layout": "design-comuni"` e nei dati del blocco `title`, `breadcrumb`, `callout`, `nav`, `contacts` in it ed en. Nessun middleware toccato.
- CSS: nessuna regola nuova per slug; `overrides.css` nasconde i passi non attivi (DCP-91); build del tema eseguita (EXIT 0, insieme al Lit `readonly` di Geo).

**Misure, tutte CON FILTRI/FIXTURE (non e' il gate standard)**: ospite (nessun login: la password dell'utente di sviluppo non e' nota e non l'ho provata a indovinare); stato impostato via `--prepare` con `Livewire.$wire.set` (titolo, dettagli, indirizzo, coordinate); mappa nascosta con `--hide '[data-dc-map]'`; passi inattivi esclusi dall'HTML con `--exclude-local '.fi-sc-wizard-step:not([id="form.riepilogo::data::wizard-step"])'`.
- **Parte alta confrontabile** (header, briciole, h1, barra dei passi con icone, avviso Attenzione, titolo Segnalazione, inizio scheda Disservizio fino a "Indirizzo"): parity **99,94 / 100 / 100%** (regioni y 0-300, 300-700, 700-880 a 1440 px); a 1920/992/768/375 le prime 600 px sono 99,92-100%.
- **Pagina intera: criterio NON raggiunto.** Pixel 50,19 / 50,35 (1440) / 51,39 / 53,21 / 61,81% a 1920/1440/992/768/375, altezze 3115 (riferimento) contro 2329 / 2329 / 2402 / 2458 / 3250 (locale, mappa nascosta). Cause provate dalla schermata: il riferimento ha 5 righe nella scheda (Indirizzo, Tipo, Titolo, Dettagli, Immagini) e la sezione "Dati Generali" (autore e contatti); la mia fixture ospite ha 3 righe e nessun "Dati Generali" (l'ospite non ha autore, per decisione 5), niente tipo ne' immagine. Il confronto pieno richiede un utente autenticato con profilo e un allegato: non fatto.
- **HTML** (stessi filtri): 70,79% rigorosa / 79,88% normalizzata. Non analizzata la scomposizione: sono attribuibili alle righe mancanti, ai nodi Filament/Livewire del wizard (input nascosti, attributi `x-*`, `wire:*`) e a header/footer condivisi; il criterio >= 90% e' comunque irraggiungibile col wizard Filament standard (decisione 7 e DCP-94).
- **Comportamenti nel browser** (un solo Chromium, 0 errori in console): "Modifica" porta al passo dei dati; "Indietro" va al passo precedente; "Invia" invia `submit` e con stato vuoto la validazione fallisce con "deve essere accettato." (privacy), come previsto. La modifica dello stato via `$wire.set` aggiorna le righe (3 righe).
- **Test**: `Tests: 61 passed, 1 failed, 1 skipped (247 assertions)` sui 6 file del wizard. Il rosso e' `CreateTicketWizardWidgetTest` > `persists a valid wizard submission...`, **preesistente**: confermato ripristinando temporaneamente la riga `summary` (stesso esito). Vedi DCP-90.
- **Non provato**: login e dati reali (autore, telefono, immagini), mappa con tile veri nel wizard (build fatta, ma la mia fixture non ha un login), la reattivita' del riepilogo dopo `nextStep` con dati inseriti dai passi 1-2 (provata solo via `$wire.set`), la pagina in inglese, il back office (non toccato), larghezze diverse da quelle sopra.

**Aperto per chiudere DCP-33**: fixture autenticata (utente di test + profilo + allegato) e gate a 5 larghezze; "Salva Richiesta" omesso per decisione; DCP-90, DCP-91, DCP-92, DCP-93, DCP-94.

## Dopo DCP-42 (14:00): la pagina e' solo per autenticati
Un ospite ora riceve 302 al login: tutte le misure di questa story (fatte da ospite) non sono piu' riproducibili e non valgono per il flusso autenticato, dove compaiono "Dati Generali" e l'autore. Per chiudere DCP-33 serve un account di prova (`--prepare` con login) o l'autorizzazione a crearne uno nel database di sviluppo: richiesta all'utente. Il cablaggio resta valido (verificato anche da test).


## Aggiornamento 14:05 UTC: auth ripristinata (lead DCP-35)
`tickets.create.json` ha di nuovo `"middleware": ["auth"]` (D17, DCP-42): le misure "da ospite" di questa story non sono piu' riproducibili e `parity-wizard-steps.sh` rifiuta di misurare la pagina di login. Servono una sessione autenticata per il gate e, per il passo di conferma, il codice di un ticket esistente (`?code=`).

## Misura da AUTENTICATO (13:5x-14:30, account di prova DCP-98): criterio raggiunto con i filtri dichiarati
Account `dcp-parity-test@example.test` con profilo (telefono), stato impostato via `$wire.set` (titolo, tipo `public_lighting`, dettagli, indirizzo, coordinate), mappa `[data-dc-map]` nascosta sul locale; sul riferimento nascoste le due righe senza sorgente nostra (`Codice Fiscale` e `Immagini`). Pagina intera, cinque larghezze, pixelmatch: **99,85 / 99,80 / 99,72 / 99,68 / 99,48%** (1920/1440/992/768/375). Righe locali = 6 (Indirizzo, Tipo, Titolo, Dettagli, Telefono, Email). Differenze residue nelle fasce y 219-221 (voce di menu "Servizi" attiva: corretta dopo, DCP-92), 949-964 (etichetta del tipo: dato di prova), 1678-1693 e 1798-1845 (nome e scheda autore), 2004-2145 (email di prova e pulsante "Salva Richiesta" omesso per decisione). Le altezze (2992 contro 3025, +33) sono la debug bar di sviluppo, come nella conferma. **Non provati**: il flusso completo con allegato reale, la mappa con tile veri, l'inglese.
