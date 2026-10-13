---
id: dcp-43
title: "Passo 2 del wizard (dati + mappa): viste Design Comuni non cablate"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [wizard, design-comuni, mappa, passo-2]
qmd: "dcp-43 Passo 2 del wizard (dati + mappa): viste Design Comuni non cablate"
depends_on: [Modules/Fixcity/docs/stories/2026-10-13-dcp-35-tickets-create-privacy-dati-coordinamento.story.md, Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-32-scope-data-dc-pagine-reali.story.md]
related: [Modules/Fixcity/docs/stories/2026-10-13-dcp-33-tickets-create-riepilogo-design-comuni-mappa.story.md, Modules/Fixcity/docs/stories/2026-10-13-dcp-42-tickets-create-auth-regressione.story.md, Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-33-wizard-step2-dati.story.md, Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-97-registro-flusso-tickets-numerazione-e-coordinamento.story.md]
reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html
local: http://192.168.1.40:8000/it/tickets/create?step=form.dati-di-segnalazione%3A%3Adata%3A%3Awizard-step
---

# DCP-43: passo 2 del wizard con la mappa

## Richiesta (2026-10-13)
Il passo 2 deve avere altissima parity visiva con `segnalazione-02-dati`, restando un Filament wizard, con in piu' la mappa per indicare dove sta il danno. Logica: il cittadino segnala un luogo, non un testo; la mappa e' dato per la squadra di intervento (D13).

## Stato misurato sulla pagina intera (guest, gate `parity-wizard-steps.sh`)
Alle 13:00 UTC (S5, gate, ospite): pixel 53,1% a 1440 e 52,2% a 768, altezze riferimento/locale 3064/4028; HTML 24,5%. Le viste sotto non sono collegate, ma la pagina e' cambiata dopo (layout `design-comuni` attivato tra le 13:50 e le 14:00, auth ripristinata alle 13:58): il numero e' STORICO e non e' stato rimisurato.

## Viste create (NUOVE, non cablate, nessun riferimento nel codice)
| Vista | File | Subagent | Misura dichiarata (iniettata nel riferimento) |
|---|---|---|---|
| Card di sezione | `Themes/Sixteen/resources/views/components/wizard/dc-section-card.blade.php` | P2-A | sezioni 255/654/349 a 1440 |
| Layout del passo (navscroll, sezioni, Indietro/Avanti) | `components/wizard/data-step.blade.php` | P2-A | `.steppers-content` 856x1477; blocco iniettato: 99,972% (1440), 99,974% (768), 99,958% (375); pagina viva con `data-dc` simulato e regola `.fi-sc` aggiunta: 99,959% / 99,96% / 99,92% (P2-A) |
| Autocomplete luogo + "Usa la tua posizione" | `Modules/Fixcity/resources/views/filament/forms/components/dc-place-autocomplete.blade.php` | P2-B | 808x113; 0 pixel diversi su 91304 (1440) |
| Select tipo, Titolo, Dettagli | `.../forms/components/dc-select.blade.php`, `dc-text-input.blade.php`, `dc-textarea.blade.php` | P2-C | 800x64, 800x88, 800x163; pixel >= 99,98% |
Le misure sono su blocchi sostituiti dentro il riferimento, non sulla pagina intera del wizard: non valgono come "fatto" (definition-of-done).

## Fatti emersi
- "Avanti" = `requestNextStep()` Alpine -> `$wire.callSchemaComponentMethod(key, 'nextStep', ...)` -> `Wizard::nextStep()`; con campo obbligatorio vuoto errore senza evento, con dati validi evento verso `form.riepilogo::data::wizard-step` (provato con `Livewire::test`).
- Geolocalizzazione: attiva solo in contesto sicuro; sul dev server HTTP il link mostra un messaggio dichiarato. Coordinate al picker con l'evento `coords-changed` (picker non `readonly`); modulo Geo non toccato.
- Suggerimenti d'indirizzo mentre si digita: NON implementati. Per P2-B `autocomplete-list-show` non ha regole nello scope e `.autocomplete-list` e' `display:none`; DCP-35 (Specifica passo 2, riga 8) prevede Alpine + `searchAddress` (>= 3 caratteri, Nominatim 1 richiesta/s senza debounce lato server): resta da fare, non e' un limite tecnico.
- Upload immagini: nessuna vista; proposta `fieldWrapperView()` + `placeholder()` HTML; il CSS FilePond e' escluso sotto `data-dc` (blocco 173,7 px contro 217).
- Il solo `data-dc` sul markup Filament attuale portava la pagina da 4060 a 5477 px a 1440 (S2, Playwright, ospite, prima del cablaggio di shell/stepper: altro metodo e altra ora dei 4028 del gate). Il layout `design-comuni` e' stato poi attivato da un altro agente: rimisurare.

## Richieste aperte (tabella in DCP-35)
1. `overrides.css` (sessione shell): `body:has(.page-content[data-dc]) .fi-sc { font-size: var(--bs-body-font-size); line-height: var(--bs-body-line-height); }`.
2. Cablaggio in `TicketForm` (orchestratore): `native()` sulla select, `required()` su `location.address`, `data-element="location-map"`, viste P2.
3. Lingue: chiavi proposte nelle sezioni "Chiavi lingua proposte (P2-A/B/C)" di DCP-35: `required_notice`, `navscroll.title`, `nav.aria_label`, `nav.back`, `nav.next`, `fields.location.geolocation_insecure`, `hints.max_length`, `fields.name.label` = Titolo.

## Criteri di accettazione
Quelli numerici della sezione 7 di DCP-35 (pixel >= 97% a 1440/768/375, altezze entro 1% con mappa nascosta, 0 chiavi grezze in it/en/de/es, PHPStan a zero, admin invariato).

## Aperto
Tutto il cablaggio e la misura sulla pagina intera. `id` duplicato `form.location` con il riepilogo (DCP-33).

## Contratti in conflitto (segnalato dal lead, 2026-10-13 14:15 UTC)
- `Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-33-wizard-step2-dati.story.md` (altro agente) vieta le classi Bootstrap Italia (righe 54-55, AC7) e possiede `components/wizard/**` (riga 312). Le viste P2-A (`data-step`, `dc-section-card`) stanno in quella cartella e usano le classi del markup di riferimento (`cmp-card`, `steppers-content`, ...), come prevede la decisione dell'orchestratore in DCP-33 (Fixcity): emettere lo stesso markup che il CSS generato gia' conosce. I due contratti non sono stati riconciliati: decide l'orchestratore o l'utente prima del cablaggio. Registro: DCP-97.
- Il lavoro misurato e le misure sono precedenti al layout `design-comuni` e all'auth ripristinata: per rimisurare serve una sessione autenticata (D17; `parity-wizard-steps.sh` rifiuta la pagina di login).

## Test di regressione delle viste (VIEWTESTS-TEMA, 16:31 UTC)
File:
- `laravel/Themes/Sixteen/tests/Unit/DcWizardViewsTest.php` (NUOVO): contratto di `dc-section-card` e `data-step`. Livewire::test con Section Filament reali e stato in memoria, asserzioni sul DOM (`Dom\HTMLDocument`, PHP 8.4), nessun database (`XotBaseTestCase`: `Tests\TestCase` usa `RefreshDatabase`), nessun file di lingua del repo toccato (il namespace `fixcity` punta a una cartella temporanea con le 5 chiavi di P2-A, o vuota per provare il ripiego italiano).
- `laravel/Modules/Fixcity/tests/Unit/DcFieldViewsTest.php` (campi, P2-B/C): corretti i 3 errori PHPStan senza ignore, cast o assert: `check()` usa `getSchema('form') ?? throw new LogicException` (tipato `?Schema`) al posto di `$this->form`, che passa dal `__get` dinamico di Filament ed e' `mixed`; l'opzione ostile si ricava una volta sola, tolto il `??` ridondante. Stesse asserzioni.

Output reale (`heavy-slot.sh` + `pest-scratch-db.sh`, DB di prova usa-e-getta, `APP_CONFIG_CACHE=/nonexistent/config.php`):
- `DcFieldViewsTest`: 49 test, 222 asserzioni, verdi (come prima della correzione).
- `DcWizardViewsTest`: 20 test, 122 asserzioni, verdi (13,3 s). Copre: struttura `section.it-page-section[id] > .cmp-card > .card.has-bkg-grey`, `h2.title-xxlarge` con `icon-required` solo se richiesto, `p.subtitle-small` assente senza descrizione, modificatori di `cardClass` e nient'altro, ordine e id delle sezioni, `mb-40` su tutte tranne l'ultima VISIBILE (e override con `last`), gap/grid spenti (anche con la scheda da sola), navscroll in `wire:ignore` con le schede fuori, voci generate dalle sezioni visibili (una nascosta sparisce anche dall'indice), `.steppers-content[aria-live=polite]` con solo `.it-page-sections-container` e `.cmp-nav-steps`, Indietro (`goToPreviousStep()`) e Avanti (`requestNextStep()`), `type="button"`, nessun `data-bs-*`, nessun Salva / `$wire.save`, ripiego italiano senza chiavi, uscita escapata (titoli, descrizioni, id, 5 testi di lingua con `<script>` e virgolette), sorgente senza `!important`, `{!! !!}`, `<?=`, `echo` (commenti Blade esclusi).
- PHPStan (`cd laravel && php -d memory_limit=2G vendor/bin/phpstan analyse <file> --no-progress --error-format=raw`) su entrambi i file: exit 0, nessun errore.

Prova di efficacia: copie rotte delle viste in `<scratchpad>/viewtests/broken/<mutazione>/components/wizard/` (mai il file di produzione), passate al test con `DC_WIZARD_VIEWS_OVERRIDE=<cartella>` (le viste di quella cartella passano davanti a quelle del tema). Sul file reale 20/20; sulle copie rotte:
| Mutazione | Test che fallisce |
|---|---|
| data-step senza `wire:ignore` sul navscroll | `keeps the navscroll in wire:ignore and the fields outside of it` |
| Avanti con `$wire.save()` al posto di `requestNextStep()` | `renders Indietro and Avanti wired to the wizard, with no Salva Richiesta` |
| indice con `getComponents(withHidden: true)` | `drops a hidden section from the navscroll as well` |
| `mb-40` sempre acceso | 3 test: `gives mb-40 ... except the last visible one`, `moves mb-40 off the previous card ...`, `lets the caller override ... last` |
| `{!! $getHeading() !!}` nella card | `escapes headings, descriptions, ids and language texts` e `keeps the view source free of important and unescaped output` (dc-section-card) |
| `icon-required` sempre acceso | `marks the title with icon-required only when the card is required` |
| pulsante "Salva Richiesta" aggiunto alla nav | `renders Indietro and Avanti ... with no Salva Richiesta` |
| card senza `gap(false)` | `turns off the Filament gap and grid wrappers inside the card body, also without the step view` |
| data-step senza `gap(false)` sul contenitore | `turns off the gap and the grid of the schema that holds the cards in data-step` |
Nota: la prima versione del test del gap NON vedeva la mutazione 8 (`gap(false)` della card tolto: 19/19 verdi) perche' dentro `data-step` il gap e' ereditato dal contenitore gia' spento; aggiunta la modalita' `standalone` della sonda (Section direttamente nel form) e il test ora la rileva.

Difetti trovati (non corretti, nessuna vista toccata):
- `Modules/Fixcity/resources/views/filament/forms/components/dc-place-autocomplete.blade.php:89` (merge con `escape: false` alla riga 97): `'placeholder' => $placeholder` esce non escapato, un segnaposto con `"` esce dall'attributo e inietta markup. Il segnaposto viene da `->placeholder()` o dalla lingua (modificabile da admin), non dal cittadino. Fissato in `DcFieldViewsTest` con `->fails()` (test `escapes the placeholder attribute of dc-place-autocomplete`): il giorno in cui la vista e' corretta il test FALLISCE e va tolto `->fails()`. Nelle due viste del tema nessun difetto trovato.
- Osservazione, non difetto: Filament ripulisce `->id()` (`getId()` toglie `"<>`), quindi l'id ostile non apre attributi; il test lo fissa (nessun `"<>` nell'id, ancora dell'indice = id della scheda).

Non coperto: Alpine reale e `requestNextStep()` eseguito nel browser, `dcNavscroll` dopo un morph di Livewire, geometria e CSS (restano al gate pixel), testi delle 5 chiavi nelle lingue vere (le chiavi non esistono ancora in `lang/*/ticket_form.php`: il test usa un namespace temporaneo), round-trip server di `nextStep` (provato da P2-A con `roundtrip.php`, non portato nel test). I test fissano le classi Bootstrap Italia del riferimento: se l'orchestratore decide per il contratto DCP-33 (nessuna classe BI, registro DCP-97) vanno rivisti insieme alle viste.
