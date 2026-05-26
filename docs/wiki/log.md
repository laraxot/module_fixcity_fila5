


## [2026-05-26] fix | Wizard step 3 (`Salva/Invia`) non esegue submit — rimosso form wrapper esterno

- **Sintomo**: nello step finale di `segnalazione-crea` il click su `Salva/Invia` non produceva alcuna azione.
- **Causa**: nella view tema del widget era stato reintrodotto un `<form wire:submit="submit">` attorno a `{{ $this->form }}`; con gli step Filament (`fi-sc-wizard-step`) questo crea annidamento form e rompe la submit chain.
- **Fix applicato**: in [`laravel/Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php`](../../../../Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php) rimosso il wrapper `<form ...>` esterno, lasciando solo `{{ $this->form }}` e il blocco errori.
- **Regola confermata**: seguire `wizard-step-visibility.md` (nessun form esterno sul widget wizard tema; submit gestito dal wizard Filament + metodi Livewire `save/submit` del widget).

## [2026-05-26] docs | verifica sorgente — BaseModel già su XotBaseModel

- **`fixcity-cross-module-duplicate-surfaces.md`**: aggiornato dopo lettura [`app/Models/BaseModel.php`](../../app/Models/BaseModel.php): niente più claim “extends Model”; resta cluster **`BasePivot`** vs **`XotBasePivot`** e Blade dashboard **byte-identico** con Rating.

## [2026-05-25] docs | redundanza superfici cross-modulo — Fixcity ⇄ Rating

- Nuovo indice modulo: [`wiki/redundancy/fixcity-cross-module-duplicate-surfaces.md`](./redundancy/fixcity-cross-module-duplicate-surfaces.md); hub trasversale [`audit-profondo-ridondanze-holistic.md`](../../../Xot/docs/wiki/redundancy/audit-profondo-ridondanze-holistic.md).

## [2026-05-22] refactor | Blade `wizard/steps/summary` — lingua `ticket` solo

- [`resources/views/filament/widgets/wizard/steps/summary.blade.php`](../../resources/views/filament/widgets/wizard/steps/summary.blade.php): rimossi **`fixcity::segnalazione.*`**; chiavi **`fixcity::ticket.sections.summary_*`**, **`fields.*`**, **`sections.contacts.edit_action`** (`lang/it|en/ticket.php`).

## [2026-05-22] docs | second brain — `ticket` vs `segnalazione` per heading `TicketForm`

- Nuova synthesis: [`concepts/fixcity-ticket-vs-segnalazione-lang.md`](./concepts/fixcity-ticket-vs-segnalazione-lang.md); puntatori in [`filament-summary-infolist-guidance.md`](../filament-summary-infolist-guidance.md), root [`translation-namespace-religion.md`](../../../../../docs/translation-namespace-religion.md).

## [2026-05-22] fix | Wizard summary — autore/contatti come TextInput + recap Infolist

- **Regression**: aver messo **`TextEntry`** anche su nome/CF/email/telefono bloccava la compilazione nello step finale.
- **Fix**: recap dati **`TicketFormReviewInfolist`** (`TextEntry` + `badge` tipo/priorità, **`TicketPriorityEnum::label()`**, luogo **indirizzo · coordinate**); **`TicketForm::getAuthorSectionSchema` / `getContactsSectionSchema`** = **`TextInput`** (+ `email()` sul contatto).
- **Test Pest**: [`TicketFormWizardSummarySchemaTest.php`](../../tests/Unit/TicketFormWizardSummarySchemaTest.php).

## [2026-05-22] refactor | Wizard riepilogo — `TicketFormReviewInfolist` (Filament TextEntry)

- **`TicketFormReviewInfolist`**: definisce **`TextEntry`** con `state(Get)` sullo stato wizard (`review_*`), enum formattati, riepilogo allegati tramite **`fixcity::ticket_form.summaries.*`**; `review_content` in **`prose()`**.
- **`TicketForm`** consuma liste ordinate **`array_values(...)`** per compatibilità `Section::schema()` / PHPStan.
- **Playwright**: URL canonico **`form.summary::data::wizard-step`** + assert `.fi-in-entry`.
- **Qualità (follow-up codice-style)**: niente **`mixed`** esplicito nelle firme (phpinsights Slevomat); **Yoda** evitati; coercion tipo/priorità in **`coerceTicketTypeValue` / `coerceTicketPriorityValue`** (`@param mixed` solo in PHPDoc). **phpinsights** su singolo file: usare **`--no-interaction -s`** e **`--min-complexity=-100`** perché il punteggio *Complexity* per file isolato può essere negativo anche con media cicli accettabile.
- **Doc/module**: [`filament-summary-infolist-guidance.md`](../filament-summary-infolist-guidance.md) · tema [`wizard-review-parity.md`](../../../../Themes/Sixteen/docs/wiki/design/wizard-review-parity.md)

## [2026-05-22] decision | Heading wizard riepilogo — namespace `fixcity::ticket.sections.summary`

- **Regola**: in `TicketForm::getSummarySchema()`, heading sezione summary (e correlate author/contacts nello schema Resource) usa **`__('fixcity::ticket.sections.summary.label')`**, **`author`**, **`contacts`** definite in **`lang/*/ticket.php`**, non `fixcity::segnalazione.sections.summary`.
- **Perché**: `segnalazione.php` serve al frontoffice pubblico / copy pagine; **`ticket.php`** è il file lingua del **modello/resource Filament Ticket** — stesso bounded context degli infolist/read-only wizard.
- Riferimento: [`filament-summary-infolist-guidance.md`](../filament-summary-infolist-guidance.md) § namespace traduzioni.

## [2026-05-23] troubleshooting | Wizard segnalazione-crea — step 1 assente / `?step=form.privacy`
- **500 / pagina Laravel**: se nel widget compare **`use Modules\Xot\Filament\Traits\NormalizesWizardFormState`** (o storici alias), **elimina la riga**: quel trait **non fa parte del progetto e non deve esistere**. Poi **`composer dump-autoload`** da `laravel/` e verifica che **`submit()`** usi solo **`$this->form->getState()`** (senza normalizzatori).
- **`?step=form.privacy` non deeplinka Filament**: `persistStepInQueryString` usa l’ **`id`/key canonico Filament**, es. `form.privacy::data::wizard-step` — vedi campo nascosto `stepsData` + doc [`ticket-wizard-frontoffice.md`](../ticket-wizard-frontoffice.md) (sezione query `step` aggiornata).

## [2026-05-23] architecture | Wizard frontoffice — persistenza stato form senza `TicketResource::prepareFormDataBeforePersist()`

- **Scopo**: il widget pubblico deve salvare ciò che esce dal form (dopo dehydrate Filament), non passare dall’helper della Resource pensata per la create nel pannello.
- **`CreateTicketWizardWidget::save()` / `submit()`** (stesso corpo **`persistFromFormState()`**) : **`$this->form->getState()`** senza normalizzatori; merge **`owner_id`** solo se auth (`??=`); `Ticket::create($data)`. Il wizard Filament invoca Alpine **`$wire.save()`** sull’ultimo step (`getSubmitFormLivewireMethodName()` dal trait Xot). La pipeline `PrepareTicketFormDataForPersistAction` resta usata da `CreateTicket::mutateFormDataBeforeCreate` nel backoffice.

## [2026-05-22] refactor | Ticket — pipeline persistenza Resource condivisa (wizard + Filament create)

- **Prima**: `CreateTicketWizardWidget::submit()` chiamava direttamente `NormalizeTicketLocationDataAction` + riconciliazione `type_id`/`owner_id` nella UI del wizard.
- **Dopo**: `PrepareTicketFormDataForPersistAction` + `TicketResource::prepareFormDataBeforePersist()` su **`CreateTicket::mutateFormDataBeforeCreate`** nel pannello; il **wizard frontoffice** (`CreateTicketWizardWidget::submit`) usa **`$this->form->getState()`** + `Ticket::create` senza quella pipeline — vedi voce architettura **Wizard frontoffice** sopra.
- Percorsi: [`PrepareTicketFormDataForPersistAction`](../app/Actions/PrepareTicketFormDataForPersistAction.php), [`TicketResource`](../app/Filament/Resources/TicketResource.php), [`CreateTicket`](../app/Filament/Resources/TicketResource/Pages/CreateTicket.php), [`CreateTicketWizardWidget`](../app/Filament/Widgets/CreateTicketWizardWidget.php).

## [2026-05-23] docs | wizard Fixcity — niente `$view` sul widget + docblock tolto errore modulo

- `CreateTicketWizardWidget`: **no** proprietà `$view`; docblock per pigrizia leggibile espone solo la catena risolta (`pub_theme::…`, `fixcity::…`). Aggiornato [`ticket-wizard-frontoffice`](../ticket-wizard-frontoffice.md): rimossa ricetta sbagliata `protected static string $view` / only-fixcity fisso senza tema.
- Concept modulo: [`xotbasewidget-child-no-explicit-widget-view`](concepts/xotbasewidget-child-no-explicit-widget-view.md).

## [2026-05-22] troubleshooting | debugbar iniettata, errore reale in asset Sixteen/Alpine

- Aggiornata [segnalazione-runtime-asset-integrity](concepts/segnalazione-runtime-asset-integrity.md): su `/it/segnalazione-crea` Debugbar era presente (`phpdebugbar-id`, `_debugbar`, `window.phpdebugbar`), ma il bundle Sixteen vecchio rompeva il runtime con `geoMapPickerField is not defined`.
- Regola operativa: prima di toccare Composer/debugbar, verificare HTML reale + manifest runtime + `public_path()`.
- Issue: [#115](https://github.com/laraxot/base_fixcity_fila5/issues/115).

## [2026-05-08] architecture | segnalazioni-elenco map-lit canonical

- Aggiornata architettura mappa/lista: la vista pubblica usa `<map-lit>`, non `<ticket-map-lit>` e non `<geo-map-lit>`.
- Confermato boundary: Fixcity genera `/data/tickets.json`, Geo renderizza il componente, Sixteen monta layout e filtri.
- Corretto falso storico: Leaflet arriva dal bundle npm/Vite del modulo Geo, non da CDN nel Blade.

## [2026-05-05] architecture | filament v5 hybrid pattern - complete schema stack

- **TicketInfolist** evolved to Filament v5 Hybrid Pattern: `configure(Schema $schema): Schema` + `getInfolistSchema(): array` dual API.
- **TicketsTable** created with Hybrid Pattern: `configure(Table $table): Table` + `table(Table $table): Table` dual API.
  - Columns: ID, name, status, priority, type, owner, assignee, dates
  - Filters: Status, priority, type
  - Auto-resolved by XotBaseResource (no manual wiring needed)
- **TicketForm** already followed pattern (wizard-based with steps)
- Complete schema stack now follows Filament v5 Demo structure:
  - `Schemas/TicketForm.php` ✅
  - `Schemas/TicketInfolist.php` ✅
  - `Tables/TicketsTable.php` ✅ (NEW)
- Documentation created:
  - `concepts/ticket-infolist-filament-v5-pattern.md` (Infolist guide)
  - `concepts/tickets-table-filament-v5-pattern.md` (Table guide - NEW)
  - `concepts/ticketinfolist-pattern-reference.md` (updated)
  - Theme reference: `Themes/Sixteen/docs/wiki/concepts/filament-v5-hybrid-pattern-reference.md`
- All patterns follow Filament v5 Demo: https://github.com/filamentphp/demo/tree/5.x/app/Filament/Resources
- Maintains XotBase extension for auto-label via LangServiceProvider (NO `->label()` calls).

## [2026-05-05] governance | wizard vendor parity + safe + quality gates story

- Added BMAD story artifact: `_bmad-output/implementation-artifacts/8-124-wizard-vendor-parity-theme-vestito-safe-quality-gates.md`.
- Scope formalized: vendor `HasWizard` parity, frontoffice/admin shared wizard engine with different skins, skiplink/next-button visual checks.
- Reinforced contract: preserve `Safe\...` imports where required and run full quality gates after changes.

## [2026-05-05] architecture | ticket resource infolist xotbase pattern

- Added `TicketInfolist` in `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketInfolist.php`.
- Pattern adopted: Filament demo structure (`Schemas/<Model>Infolist`) with Laraxot extension `XotBaseResourceInfolist`.
- Runtime wiring remains DRY via `XotBaseResource::infolist()` auto-resolution.
- Documentation aligned on existing reference: `concepts/ticketinfolist-pattern-reference.md`.
- Theme boundary documented in Sixteen: `concepts/fixcity-ticket-infolist-theme-boundary.md`.

## [2026-05-04] architecture | XotBaseWizardWidget View Calculation Rule

- **Story**: `_bmad-output/implementation-artifacts/8-115-xotbasewizardwidget-view-calculation-rule.md`
- **Rule**: Sottoclassi di `XotBaseWizardWidget` (come `CreateTicketWizardWidget`) NON devono definire `$view` property
- **View Resolution**: La view viene calcolata automaticamente:
  - Admin: `filament/components/wizard` (default Filament)
  - Frontoffice: `pub_theme::components.wizard` (Design Comuni styled)
- **Audit**: `CreateTicketWizardWidget` compliant - nessuna `$view` property definita ✅
- **Docs**: Xot module wiki + regola Windsurf aggiornati

## [2026-05-04] architecture | Wizard Filament Theme Boundary - Fixed BadMethodCallException

- **Problem**: `BadMethodCallException: getWizardDisplayStep does not exist` on `/it/tests/segnalazione-crea`
- **Root Cause**: Blade template called `$this->getWizardDisplayStep()` which already existed at line 100, but the widget had duplicated code from earlier edits
- **Fix**: Method already exists properly implemented - removed duplicate declaration
- **Architecture**: Documented Filament wizard architecture boundary in `concepts/wizard-architecture-filament-theme-boundary.md`
- **Key Learning**: Theme should override `pub_theme::components.wizard` for CSS parity, not create custom blade wrappers in module

- **Prima**: «non usare inAdmin() in TicketForm» e solo `PubThemeWizard` nello schema.
- **Dopo**: admin → `Wizard` Filament; frontoffice → `PubThemeWizard`; link a `filament-admin-pub-theme-wizard-boundary` (root wiki) e `theme-owned-wizard-css-parity-rule`.
- **Motivo**: `PubThemeWizard` nel backoffice è errato; il vestito `pub_theme` vale solo sul sito pubblico.

## [2026-05-04] Story 8-114: XotBaseWizardWidget vs Filament HasWizard Parity

- **Story**: `_bmad-output/implementation-artifacts/8-114-xotbasewizard-filament-haswizard-parity.md`
- **Problem**: `XotBaseWizardWidget` reinventa logica già presente in Filament Concerns (`HasWizard` Actions + Pages)
- **Visual Issues**: ~~Frontoffice manca tasto "Avanti"~~ ✅ **FIXED** - Architettura corretta:
  - `XotBaseWizardWidget` configura `->view('pub_theme::components.wizard')`
  - View tema renderizza: stepper + contenuto + azioni
  - `create-ticket-wizard.blade.php` wrapper solo titolo/container
- **Architecture Docs**: `Themes/Sixteen/concepts/wizard-custom-view-architecture.md`
- **URLs**: `/it/tests/segnalazione-crea` vs `/fixcity/admin/tickets/create`
- **Widget**: `CreateTicketWizardWidget` extends `XotBaseWizardWidget`
- **Status**: in-progress

## [2026-05-04] Story 8-113: Correct Wizard Implementation Approach

- **Story**: `_bmad-output/implementation-artifacts/8-113-correct-wizard-implementation-approach.md`
- **Fix**: Rimosso `PubThemeWizard` - violava separazione modulo/tema
- **TicketForm**: Ora usa `Wizard::make()` standard (no condizionale, no PubThemeWizard)
- **Philosophy**: Modulo crea componenti standard, Tema gestisce presentazione via CSS/Blade
- **Status**: done

## [2026-05-04] Story 8-112: Wizard Custom View Pattern

- **Story**: `_bmad-output/implementation-artifacts/8-112-wizard-view-custom-theme-pattern.md`
- **Pattern**: `PubThemeWizard` estende Wizard e imposta `view('pub_theme::components.wizard')`
- **TicketForm**: Condizionale `inAdmin() ? Wizard : PubThemeWizard`
- **Location**: `TicketForm::getFormSchema()` linee 42-48
- **Files**: `PubThemeWizard.php` + `TicketForm.php` (già implementati)
- **Status**: done - documenta pattern esistente

## [2026-05-04] Story 8-111: Wizard Theme Component Architecture

- **Story**: `_bmad-output/implementation-artifacts/8-111-fixcity-wizard-theme-component-architecture.md`
- **Architecture Fix**: Corregge errore architetturale in 8-110 (logica hardcoded nel tema)
- **New Pattern**: `pub_theme::wizard` Blade component - tema riceve wizard da modulo via props
- **Contract**: Fixcity espone `CreateTicketWizardWidget::getFormSchema()`, Sixteen wrappa con Design Comuni
- **Docs**: `concepts/wizard-theme-integration.md` - API contract, data flow, anti-patterns
- **Philosophy**: Modulo = Logica (PHP), Tema = Vestito (Blade/CSS)

## [2026-05-04] Story 8-110: Segnalazione-Crea Step 1 Privacy Parity

- **Story**: `_bmad-output/implementation-artifacts/8-110-segnalazione-crea-step1-privacy-design-comuni-parity.md`
- **Module**: Fixcity (wizard) + Sixteen (CSS/theme)
- **Scope**: Step 1 privacy del wizard segnalazione - stepper, checkbox phrase, font parity
- **Translation Keys Verified**:
  - `create_ticket_wizard.fields.privacyAccepted.label` = "Ho letto e compreso l'informativa sulla privacy" (IT)
  - `create_ticket_wizard.fields.privacyAccepted.label` = "I have read and understood the privacy policy" (EN)
- **Widget**: `CreateTicketWizardWidget` (Filament v5 + Livewire)
- **Form Schema**: `TicketForm::getFrontofficePrivacySchema()`
- **Sprint Status**: `ready-for-dev` in epic-8

## [2026-05-04] optimization | AI Directory Optimization & Second Brain Sync
- Project-wide cleanup of `bashscripts/ai/.agents` and `.claude`.
- Local module knowledge (memories, guidelines, docs) migrated to the modular Wiki.
- Improved agent reactivity and search performance.

## [2026-05-04] bmad-create-story | 7-105 — inventario classi Bootstrap Italia (7 pagine DC) → Tailwind Sixteen

- Story BMAD: `_bmad-output/implementation-artifacts/7-105-design-comuni-segnalazione-static-pages-bootstrap-to-tailwind-class-map.md` (`ready-for-dev`).
- Sette URL Design Comuni statici (dettaglio, wizard 01–04, area personale, elenco): estrazione classi verso tabella wiki + implementazione in `Themes/Sixteen/resources/css/app.css`.
- Backlink: `concepts/segnalazione-design-comuni-comparison.md`, `comparisons/segnalazione-01-privacy-design-comuni-vs-local-wizard.md`; tema `segnalazione-visual-parity-correction-plan.md`.

## [2026-05-04] bmad-create-story | 7-110 step1 — stepper, checkbox frase ufficiale, font

- Story: `_bmad-output/implementation-artifacts/7-110-segnalazione-01-privacy-stepper-checkbox-typography-parity.md`
- Sprint: `7-110-segnalazione-01-privacy-stepper-checkbox-typography-parity` → ready-for-dev

## [2026-05-04] visual-diff | segnalazione-crea step 1 — screenshot Playwright + story 7-77
- screenshot comparativo Playwright: ref `segnalazione-01-privacy.html` vs locale `segnalazione-crea`.
- identificate 4 DIFF attive: stepper orizzontale, bottone colore/larghezza, checkbox label, bottoni spurii.
- header colori confermati OK (slim `#00402b`, navbar `#007a52`).
- creata entry `concepts/segnalazione-crea-step1-diff-2026-05-04.md`.
- aggiornata `concepts/segnalazione-design-comuni-comparison.md` con dati reali screenshot.
- story 7-77 creata in `_bmad-output/implementation-artifacts/`, sprint-status aggiornato.

## [2026-05-04] wiki | segnalazione-01-privacy — confronto + link index
- Creato `comparisons/segnalazione-01-privacy-design-comuni-vs-local-wizard.md` (stub locale + backlink Sixteen; story 7-103 audit HTML/Tailwind/Lit).
- Corretti path relativi verso `_bmad-output`, Sixteen e theme correction plan (`index.md` riga `segnalazione-visual-parity-correction-plan`: da `docs/wiki/` = `../../../../Themes/Sixteen/...`).
- Aggiornati `docs/wiki/index.md` (tabella + `Ultimo aggiornamento: 2026-05-04`).

## [2026-05-04] documentation | Segnalazione vs Design Comuni — Full Comparison + Correction Plan
- Studied: `https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html` (Step 1-4, elenco)
- Created: `concepts/segnalazione-design-comuni-comparison.md` (full diff matrix: visual, technical, HTML vs Blade)
- Created: theme-side correction plan `../../../../Themes/Sixteen/docs/wiki/concepts/segnalazione-visual-parity-correction-plan.md`
- Key findings: elenco layout (side-by-side→stacked), map height (400px), stepper labels, CTAs missing
- Strategy: **visual parity** e **HTML parity** dove fattibile, con **Tailwind + Alpine + Lit** (no Bootstrap Italia come UI base)
- Rules: NO blocchi `<style>` in Blade, CSS/JS nel tema, `npm run build && npm run copy`
- Updated: index.md, log.md (this file)

## [2026-04-29] governance | TicketForm SSoT senza firme schema parametrizzate
- decisione: `TicketForm` resta il provider canonico degli schema del ticket wizard, consumabile dal widget frontoffice quando possibile.
- implementazione: `CreateTicketWizardWidget` delega privacy/data/summary a `TicketForm`; il link privacy CMS passa da `TicketForm::getFrontofficePrivacySchema($privacyLink)`, lasciando `getPrivacySchema()` senza argomenti.
- memoria Geo: i picker sibling commentati con `NON CANCELLARE QUESTO` restano nello schema owner `TicketForm`, non nel widget.
- anti-pattern respinto: cambiare firme convenzionali come `getPrivacySchema()` in `getPrivacySchema(?HtmlString $privacyNotice = null)`. Anche con default, rompe la pulizia del contratto `get{Name}Schema()` usato da Xot/Filament e accoppia schema statico a valori runtime.
- anti-pattern respinto: helper cosmetici per tre `TextEntry` autore. DRY non significa nascondere dichiarazioni semplici dietro micro-metodi; significa eliminare duplicazione reale mantenendo leggibile il comportamento.
- regola operativa: differenze runtime del widget (`blockData`, auth user, redirect, submit, normalizzazione payload) restano nel widget; lo schema condiviso e deterministico resta in `TicketForm`.

## [2026-04-29] fix | admin create ticket non deve persistere `address` top-level
- errore target: `SQLSTATE[HY000]: table tickets has no column named address` su route admin `fixcity/admin/tickets/create`.
- root cause: nella pipeline create resource poteva arrivare payload con `address` top-level; su schema sqlite `xot` la colonna non esiste.
- fix owner-side applicato:
  - `app/Filament/Resources/TicketResource/Pages/CreateTicket.php`: `mutateFormDataBeforeCreate()` ora usa `NormalizeTicketLocationDataAction` e rimuove sempre `address` top-level, mantenendo `location` canonica;
  - `app/Actions/NormalizeTicketLocationDataAction.php`: normalizzazione unica `location` (`lat/lng`, `address`, `street`, `street_number`, `zip`, `postcode`, `city`, `province`, `state`, `country`, `country_code`, `suburb`, `address_details`) e `unset($state['address'])`;
  - `app/Models/Ticket.php`: mutator `location()` hardenizzato con guardie schema (`latitude`, `longitude`, `location`) + split componenti indirizzo da `addressdetails/address_components`.
- decisione business: il blocco geografico vive in `location` (source of truth), con mirror retrocompatibile su `latitude`/`longitude`.
- riferimento story: `../../../../_bmad-output/implementation-artifacts/8-73-ticket-location-address-column-mismatch-and-structured-components.md`.

## [2026-04-28] sync | geo controls visibility hardening for admin map
- recepito fix owner-side Geo sui controlli mappa mancanti in admin rispetto al frontoffice.
- il wizard/frontoffice resta invariato funzionalmente; lato admin viene hardenizzata la visibilita' dei controlli (`fullscreen`, `zoom`, `current position`) tramite fallback e z-index robusti.
- riferimento owner-side: `../../Geo/docs/wiki/log.md`.

## [2026-04-28] fix | ticket location payload compatibile con sqlite senza colonna `location`
- errore runtime su submit wizard/admin: `SQLSTATE ... table tickets has no column named location`.
- root cause: mutator `Ticket::location()` serializzava sempre il campo DB `location`, ma su connessione `xot` sqlite la tabella `tickets` non ha quella colonna.
- fix applicato in `app/Models/Ticket.php`:
  - aggiunta guardia schema `hasLocationColumn()` con cache per connessione/tabella;
  - il mutator salva sempre `latitude`/`longitude`, e salva `location` solo se la colonna esiste.
- verifica tecnica:
  - `php -l Modules/Fixcity/app/Models/Ticket.php` OK;
  - tinker: `fill(['location'=>...])` produce payload con sole `latitude`/`longitude` su sqlite.

## [2026-04-28] fix | ticket create schema - closure summary immagini senza contesto oggetto
- errore runtime su `/fixcity/admin/tickets/create`: `Using $this when not in object context`.
- root cause in `TicketForm::getSummarySchema()`: closure di `ImageEntry::state()` usava `$this` dentro contesto statico.
- fix applicato in `app/Filament/Resources/TicketResource/Schemas/TicketForm.php`:
  - `->state(static fn (Get $get): array => self::normalizeSummaryImages(...))`
  - `normalizeSummaryImages()` resa `protected static`.
- verifica: `php -l Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php` OK.

## [2026-04-28] recheck | screenshot after fix step dati (sidebar/search/map)
- eseguito recheck visuale con Playwright CLI sulla stessa URL/step del bug report.
- evidenza aggiornata: `../../assets/segnalazione-step-dati-after-fix-2026-04-28-full-recheck.png`.
- esito: overlap search risolto, overlay testuale mappa risolto, opacita' mappa coerente; sidebar migliorata ma ancora comprimibile.
- pagina aggiornata: `comparisons/segnalazione-crea-step-dati-screenshot-audit-2026-04-28.md`.

## [2026-04-28] fix | step dati segnalazione — sidebar accordion state semplificato
- source: `../../../resources/views/filament/widgets/ticket-create-wizard.blade.php`
- rimosso doppio controllo stato (`collapse` Bootstrap + `x-show` Alpine) nella sidebar `Informazioni richieste`.
- obiettivo: evitare rendering ambiguo/box vuoto e mantenere comportamento deterministico nello step dati.
- pagina aggiornata: `comparisons/segnalazione-crea-step-dati-screenshot-audit-2026-04-28.md`.

## [2026-04-28] tooling | playwright mcp verification + screenshot audit refinement
- verificata disponibilita' Playwright MCP in runtime locale con `npx -y @playwright/mcp@latest --help`.
- aggiornato audit screenshot step `Dati della segnalazione` con evidenza file immagine e piano fix owner-side modulo.
- pagina aggiornata: `comparisons/segnalazione-crea-step-dati-screenshot-audit-2026-04-28.md`.

## [2026-04-28] audit | screenshot runtime segnalazione-crea step dati
- documentato audit da screenshot utente sulla URL `http://127.0.0.1:8001/it/tests/segnalazione-crea?step=form.dati-della-segnalazione%3A%3Adata%3A%3Awizard-step`.
- rilevati: box laterale `Informazioni richieste` vuoto ma ingombrante, spacing verticale eccessivo, clipping del campo search, mappa con testo grezzo sovrapposto e controlli non leggibili.
- nuova pagina: `comparisons/segnalazione-crea-step-dati-screenshot-audit-2026-04-28.md`.

## [2026-04-28] governance | second brain bootstrap recepito nel modulo
- recepito il bootstrap operativo root: `../../../../bashscripts/docs/second-brain-session-bootstrap.sh`.
- aggiunto backlink in index locale verso `../../../../docs/wiki/concepts/second-brain-session-bootstrap.md`.
- regola applicata: prima retrieval wiki/QMD, poi analisi e fix runtime del modulo.

## [2026-04-27] analysis | admin tickets create map runtime asset chain
- verificata route reale `http://127.0.0.1:8000/fixcity/admin/tickets/create` con login operatore.
- evidenziata catena asset incoerente: `map-picker.js`/`map-picker.css` `200`, ma `geo-map-widget.js` e `geo.js` `404`.
- identificato fallback runtime verso `/themes/Geo/js/map-picker-component.js` come percorso legacy non equivalente alla catena canonica.
- creata story operativa: `../stories/wizard-map-runtime-asset-chain.md`.
- collegamenti cross-owner confermati: `../../Geo/docs/wiki/index.md`, `../../../Themes/Sixteen/docs/wiki/index.md`, `../../../../docs/wiki/index.md`.

## [2026-04-28] fix | story 8-64 — admin ticket location non persistita (mutator mancante)
- **diagnosi precedente errata**: il doc originale indicava `location` mancante da `$fillable` — SBAGLIATO. `location` era già in `$fillable` ma i dati venivano persi lo stesso.
- **root-cause reale**: `CoordinatePicker::make('location')` invia array composito `{latitude, longitude}` a `Ticket::fill(['location' => [...]])`, ma il DB ha colonne separate `latitude`/`longitude` senza colonna `location`. Il cast `'location' => 'array'` tentava di salvare su una colonna inesistente.
- **fix**: aggiunto `location(): Attribute` (mutator Eloquent multi-colonna) in `Ticket.php`; rimosso `'location' => 'array'` da `casts()`.
- **pattern di riferimento**: `laravel/Modules/Geo/docs/wiki/concepts/coordinate-picker-filament5-save-pattern.md`.
- **regola permanente**: `bashscripts/ai/.claude/rules/coordinatepicker-multi-column-save.md`.
- aggiornata pagina: `troubleshooting/ticket-location-not-saved-mass-assignment.md` con root-cause corretto.

## [2026-04-27] governance | obsidian + skills + ingest discipline
- aggiunta checklist stabile `concepts/obsidian-skills-and-ingest-checklist.md`.
- formalizzata routine: update docs modulo/tema + rules/memory/skills + `qmd update` + query smoke.
- ingest eseguito con `qmd update` dopo creazione nuovi documenti.

## [2026-04-27] root-cause | admin map asset registry mismatch
- documentato mismatch tra registry asset panel (`/modules/geo/*`) e file deployati reali.
- evidenza: `public_html/modules/geo/geo-map-widget.js` assente, mentre `map-picker.css/js` presenti.
- evidenza: `geo.js` presente in `public_html/themes/Geo/js/`, quindi catena parzialmente su path differente.
- nuova pagina: `concepts/admin-map-asset-registry-mismatch.md`.
- fix runtime applicato nel loader `public_html/modules/geo/map-picker.js`: registrazione alias `map-picker-lit` anche nel ramo `resp.ok === false`.
- verifica visuale reale effettuata su route admin con step wizard `form.data::data::wizard-step`.
- riscontro network: `geo-map-widget.js` e `geo.js` su `/modules/geo/` in 404, fallback a `themes/Geo/js/map-picker-component.js` attivo.
- hardening applicato dopo analisi browser automation + ricerca tecnica: rimossa registry asset inesistente, loader allineato a `themes/Geo/js/geo.js`, fallback Leaflet prioritizzato su asset locali.
- recheck visuale+network ok: `HEAD/GET /themes/Geo/js/geo.js` 200, nessun 404 sulla vecchia chain.

# 2026-04-22

## [2026-04-27] analysis | scopo e utilita' mappa admin ticket create
- analizzato il ruolo della mappa nello step `data` della route admin `fixcity/admin/tickets/create`.
- chiarito il fine business: ridurre ambiguita' della segnalazione, accelerare dispatch operativo, aumentare qualita' dati territoriali.
- allineato il boundary owner: dominio `location` in Fixcity, runtime picker in Geo, parity visuale nel tema Sixteen.
- aggiornata pagina di riferimento: `concepts/location-capture-map-wizard.md`.

- Ingestita regola `fix-complete-only-after-target-route-recheck`: un fix Fixcity e' concluso solo dopo recheck della URL finale reale con step/query corretti e controllo del componente coinvolto.
- Ingestito contratto route admin `fixcity/admin/tickets/create`: nuova pagina `concepts/admin-ticket-create-map-visual-contract.md` con boundary tra owner form/resource e runtime picker Geo.
- Tracciato blocker operativo: verifica browser automatica non disponibile in sessione corrente, da eseguire nel ciclo dev della story.

- Recepito runbook context-mode/QMD per `/bmad-create-story`: in caso di errore `maximum context length is 131072 tokens`, usare retrieval selettivo e sintesi wiki invece di rilanciare prompt massivi. Riferimenti: `docs/wiki/concepts/context-mode-mcp.md`, `docs/wiki/concepts/context-compression-discipline.md`, `bashscripts/docs/wiki/concepts/bmad-context-compression-operations.md`.

## [2026-04-21] story | 8-40 segnalazione dati — mappa Livewire + header parity
- **artifact:** `_bmad-output/implementation-artifacts/8-40-segnalazione-dati-map-header-parity.md`
- **Geo:** `map-picker.blade.php` usa `$wire.entangle` + `map-picker-lit` (stesso pattern di `coordinate-picker`); Lit: `IntersectionObserver` visibilità + sync props `latitude`/`longitude`; attributo `geolocate-when-empty`; traduzioni `geo::map-picker.status.*`; `map-picker-styles.js` — min-height su `.leaflet-container` dentro `map-picker-lit`.
- **Fixcity widget:** `ticket-create-wizard.blade.php` — rimossi selettori errati `.page-content … .it-header-*` e duplicati CSS header (owner: tema); rimosso JS inline colori header.
- **Tema Sixteen:** `header/v1.blade.php` — classe BI **`theme-light-desk`** su `.it-header-navbar-wrapper` per `segnalazione-crea` (default CDN BI 2.18: `background:#06c`); `layouts/main.blade.php` — `<style>` fine `<head>` per link/hover/toggler; `app.css` — catena verde `.page-content` solo slim; `app.js` — niente `filament/map-picker.js` duplicato.
- **verifica:** `curl` step dati wizard HTTP 200; `npm run build` tema OK.

## [2026-04-21] fix | CreateTicketWizardWidget import `Action` duplicato
- **Errore**: `FatalError: Cannot use Filament\Actions\Action as Action because the name is already in use` (route es. `/it/tests/segnalazione-crea`).
- **Cause possibili**:
  - due righe identiche `use Filament\Actions\Action;`, oppure
  - mix `Filament\Actions\Action` + `Filament\Forms\Actions\Action` (stesso alias `Action`).
- **Fix consolidato**: un solo `use Filament\Actions\Action;` allineato a `XotBaseWizardWidget`; type hint `configureWizardNextAction` / `configureWizardPreviousAction` con `Action` (nessun alias necessario).
- **Verifica**: `php -l app/Filament/Widgets/CreateTicketWizardWidget.php` (pass).

## [2026-04-21] refactor | Geo Unified Architecture & LLM Wiki Adoption
- **Objective**: Unify map components (`CoordinatePicker`, `MapPicker`, `LatitudeLongitudeInput`) into a single, robust architecture based on Lit Web Components.
- **Implementation**:
  - Web Component: `coordinate-picker-lit` (JS-only, Light DOM, Leaflet-based) in `coordinate-picker-field.js`.
  - PHP: Refactored `CoordinatePicker` as the master component, with `MapPicker` and `LatitudeLongitudeInput` as thin wrappers.
  - State: Unified `{ latitude, longitude }` state with `CoordinatePicker::extractCoordinates` utility for DB mapping.
  - UI/UX: Integrated "Expanded" (fullscreen) mode and "Mia posizione" (geolocation) with mobile-first focus.
- **LLM Wiki**:
  - Adopted Karpathy LLM Wiki pattern in `Modules/Geo/docs/wiki/`.
  - Created concept: `concepts/coordinate-picker-architecture.md`.
  - Unified root `docs/` as the "Raw" layer.
- **Asset Integrity**: Asset pipeline synchronized with `npm run build && npm run copy` in `Themes/Sixteen`.
- **Fixcity Integration**: `CreateTicketWizardWidget` refactored to use the new `MapPicker` with unified state.

## [2026-04-20] fix | profiles.uuid riportato nella migrazione owner
- sources:
  - `database/migrations/2026_04_27_190000_create_profiles_table.php`
- pages:
  - `concepts/profiles-uuid-contract.md` (new)
- summary:
  - la migrazione owner `create_profiles_table` di Fixcity ora dichiara `uuid` nello schema base
  - aggiunto anche guard idempotente in `tableUpdate()` per installazioni legacy con tabella `profiles` senza colonna `uuid`
  - timestamp migrazione riallineato per mantenere la regola "1 modello = 1 migrazione"

## [2026-04-27] fix | profiles.credits nullable per create profilo minimale
- sources:
  - `database/migrations/2026_04_27_190000_create_profiles_table.php`
- pages:
  - `concepts/profiles-uuid-contract.md` (updated)
- summary:
  - `credits` e' opzionale e quindi nullable nel contratto schema
  - evitato blocco su insert con soli `user_id`, `uuid` e timestamps

## [2026-04-21] ui | wizard segnalazione cta unica (avanti)
- Identificata duplicazione CTA nello step privacy/data: footer wizard Filament (`Successivo`) + nav custom (`Avanti`).
- Applicata regola `single-next-cta`: nascosti tasti nativi Filament via widget PHP (`configureWizardActions`) per garantire che resti una sola CTA primaria (`Avanti`).
- Owner tecnico: `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` e `fixcity::filament.widgets.ticket-create-wizard`.
- Nuovo concept: `concepts/wizard-single-next-cta-rule.md`.
- Refinement parity: classi CTA aggiornate con `fw-bold`, `btn-next`, `btn-prev` per coerenza visiva Design Comuni.

## [2026-04-21] audit | segnalazione-privacy parity multi-breakpoint
- Audit visuale eseguito su mobile/tablet/desktop contro reference Design Comuni.
- Colori header verificati e riallineati: barra slim `rgb(0, 64, 43)`, barra nav `rgb(0, 122, 82)`.
- Rimossa duplicazione azione di avanzamento (`Successivo`), mantenuto `Avanti`.
- Bottone `Accedi all'area personale` riallineato al verde istituzionale.
- CTA `Avanti` riposizionata sotto checkbox privacy su mobile/tablet/desktop.
- Concept: `concepts/segnalazione-privacy-parity-audit.md`.

## [2026-04-21] fix | livewire queryexception cache table mancante
- Errore gestito: `SQLSTATE[42S02]` su `cache` durante `POST /livewire-*/update`.
- Causa: `RateLimiter` Livewire richiede backend cache coerente; in presenza di store DB servono tabelle cache.
- Correzione: eseguita migrazione mirata `database/migrations/2026_04_21_111944_create_cache_table.php`.
- Verifica: `cache` e `cache_locks` presenti.
- Concept: `concepts/livewire-cache-table-rate-limiter.md`.
- Hardening: resa idempotente la migrazione duplicata `2026_04_21_112114_create_cache_table` e marcata `Ran` senza collisioni.
- Check runtime: smoke test `GET /it/tests/segnalazione-crea` = 200 e `RateLimiter` operativo in tinker.

## [2026-04-21] fix | segnalazione-crea asset/runtime bootstrap chain
- Rimossi riferimenti hardcoded a CSS non deployati nel layout tema (`header-fix.css`, `mobile-header-fix.css`, `mobile-map-fix.css`).
- Ripristinato asset Geo in webroot runtime: `public_html/themes/Geo/js/geo.js`.
- Aggiornati asset frontend con `livewire:publish --assets`, `filament:assets`, `optimize:clear`.
- Resa robusta registrazione Alpine `geoMapPickerField` con init immediata + fallback `alpine:init`.
- Concept: `concepts/segnalazione-runtime-asset-integrity.md`.

## [2026-04-15] init | wiki bootstrap
- Struttura wiki/log.md inizializzata.
- Layer raw: tutti i file in `docs/` (eccetto `wiki/`).
- Layer wiki: `docs/wiki/` — LLM-maintained, sintesi ad alto riuso.
- Schema: `docs/.schema/WIKI_SCHEMA.md`
- Adozione moduli: `docs/project/llm-wiki-module-adoption.md`
# 2026-04-22

- Ingestita decisione `wizard-summary-infolist-runtime-fix-2026-04-22`: per `CreateTicketWizardWidget::getSummarySchema()` usare entry Infolist (`TextEntry`, `ImageEntry`) dentro layout schema, non `SchemaView` e non `Livewire\Forms\Form`.
- Ingestita nota `context-compression-plugin-runtime`: evitare caricamenti massivi di docs/debug HTML; OpenRouter context-compression e' configurazione client API, non codice Fixcity.

## [2026-04-22] fix | getSummarySchema implementato con pattern Infolist (story 8-41)
- **Problema**: `getSummarySchema()` aveva corpo commentato con `SchemaView` (pattern errato).
- **Errori PHP**: story 8-41 auto-applicata aveva introdotto `use` duplicati (TextEntry×2, ImageEntry×2), `use Livewire\Forms\Form`, `use Filament\Infolists\Components\Infolist` — tutti rimossi.
- **Fix**: implementato pattern `TextEntry::make()->state(fn(Get $get): string => ...)` con `Get` da `Filament\Schemas\Components\Utilities\Get`.
- **Namespaces corretti**: `TextEntry`/`ImageEntry` ← `Filament\Infolists\Components\*`; `Section`/`Grid`/`Get` ← `Filament\Schemas\Components\*`.
- **Regola permanente**: `bashscripts/ai/.claude/rules/filament5-infolist-wizard-summary.md`.
- **Concetto wiki**: `concepts/filament5-schema-namespaces-and-wizard-summary.md`.
- **Verifica**: HTTP 200 su `http://127.0.0.1:8000/it/tests/segnalazione-crea`.

## [2026-04-22] rule | Design Comuni CSS solo nel tema
- **Problema**: CSS inline nel widget wizard Fixcity rompe la parity HTML e duplica responsabilita' del tema.
- **Regola**: `ticket-create-wizard.blade.php` espone markup/classi stabili; le regole visuali vivono in `Themes/Sixteen/resources/css/`.
- **Build**: dopo CSS tema eseguire `npm run build` e `npm run copy` da `laravel/Themes/Sixteen`.
- **Concetto wiki**: `concepts/design-comuni-theme-css-only-rule.md`.

## [2026-04-22] fix | Filament Section namespace corretto
- **Problema**: `Filament\Infolists\Components\Section` non esiste nel runtime Filament 5 installato.
- **Regola**: layout `Section`/`Grid` da `Filament\Schemas\Components`; read-only entries `TextEntry`/`ImageEntry` da `Filament\Infolists\Components`.
- **Fonti**: Filament 5 `schemas/sections` e `components/form#using-multiple-forms`.
- **Concetto wiki**: `concepts/filament5-schema-section-namespace-rule.md`.

## [2026-04-22] ui | mappa step dati e spacing Disservizio
- **Problema**: nello step dati la mappa puo' essere inizializzata mentre lo step wizard non e' ancora visibile; lo spacing fra `Disservizio` e `Tipo di disservizio` e' eccessivo.
- **Owner**: logica Leaflet nel modulo Geo; spacing/z-index/parity visuale nel tema Sixteen.
- **Regola**: niente CSS inline nel widget Fixcity; usare classi e `data-step-section`.
# 2026-04-22 - Wizard Fixcity markup-only

- Aggiunta `concepts/theme-owned-wizard-css-parity-rule.md`.
- Regola: `resources/views/filament/widgets/ticket-create-wizard.blade.php` non deve contenere `<style>` o `style=""` per parity visuale.
- Owner CSS: tema Sixteen; owner markup/stato/schema: modulo Fixcity.

# 2026-04-23

- Ingestita regola `phpstan-runtime-priority-rule`: quando il wizard `segnalazione-crea` e' in errore runtime, la priorita' e' ripristinare la URL reale e solo dopo affrontare i cluster PHPStan non bloccanti.

## [2026-04-27] governance | profiles owner rule reinforced after User additive migration
- rilevata e rimossa migrazione errata nel modulo User: `add_credits_to_profiles_table`.
- ribadito che `profiles` e' caso particolare con owner migration unica nel modulo Fixcity.
- riferimento operativo aggiornato: `concepts/profiles-uuid-contract.md`.

## [2026-04-27] fix | executed profiles migration to resolve credits NOT NULL violation
- eseguita migrazione `2026_04_27_190000_create_profiles_table.php` su connessione `fixcity`.
- risolto errore SQLSTATE 23000 su insert profilo senza credits.
- verificata nullabilita' colonna `credits` tramite tinker e reproduction script.
- infrastruttura LLM Wiki (Karpathy pattern) configurata in tutti i moduli e temi.

# 2026-04-28

## [2026-04-28] cms | draft ticket confirmation page
- Aggiunta pagina CMS `tests.segnalazione-bozza-salvata` per il redirect dopo `saveDraft()`.
- Il wizard `segnalazione-crea` ora puo' usare `draft_confirmation_slug` distinto da `confirmation_slug`.
- La pagina bozza riusa il blocco tema `pub_theme::components.blocks.flow.segnalazione.04-conferma` con copy dedicato, senza nuova route Laravel e senza duplicare Blade.

## [2026-04-28] fix | story 8-59 — ticket location JSON canonica
- Creata e validata story BMAD `8-59-ticket-location-json-persistence`.
- Aggiunta colonna JSON nullable `tickets.location` nella migrazione canonica Fixcity.
- Aggiornato `CreateTicketWizardWidget::prepareTicketData()` per mantenere `location` nel payload di draft e submit, senza estrarre solo `latitude` / `longitude`.
- Aggiornato `Ticket::location()` per salvare il JSON `location` e mantenere `latitude` / `longitude` come mirror legacy.
- Aggiornata troubleshooting page `ticket-location-not-saved-mass-assignment.md`: la regola "nessuna colonna location" e' ora solo contesto storico, non contratto corrente.
- 2026-04-28: Added troubleshooting note `ticket-location-column-mismatch` after ticket create failed on SQLite because the active schema had no `location` column. Persist location payload through `latitude`/`longitude`/`address` instead.
## [2026-04-29] story | segnalazione-crea map fullscreen refinement
- Creata story BMAD `8-74-segnalazione-crea-map-fullscreen-refinement` per migliorare fullscreen mappa sull'URL esatto con `step=form.dati-della-segnalazione`.
- Aggiornati contratto fullscreen Fixcity e story docs modulo.
- Boundary confermato: Fixcity verifica wizard, Geo possiede runtime Lit/Leaflet, Sixteen possiede CSS/parity.

## [2026-04-29] feature | story 8-75 — segnalazioni-elenco mappa Lit + lista live
- Implementato `GenerateTicketsJsonAction`: scrive `public_html/data/tickets.json` (GeoJSON FeatureCollection)
- Creato `ticket-map-lit.js`: Lit Web Component (HTMLElement puro, no LitElement dep) con Leaflet + MarkerCluster CDN
- Aggiornato `layout.blade.php` Sixteen: filtri dinamici da `TicketTypeEnum::cases()`, tab mappa → `<ticket-map-lit>`, tab lista → ticket reali DB
- HeaderAction "Esporta JSON mappa" aggiunto a `ListTickets.php` nel pannello admin
- Pattern: farmshops.eu — file JSON statico fetch dal componente Lit, no API controller, no Livewire
- Regola rispettata: `class="map-container"` mai `id="map"` nel componente Lit

## [2026-05-04] comparison | segnalazione-01-privacy delta modulo — HTML parity audit
- Aggiornato `comparisons/segnalazione-01-privacy-design-comuni-vs-local-wizard.md` (modulo) con 5 delta critici operativi
- Critico: `create-ticket.blade.php` ha `<style>` inline massivo — da rimuovere e portare in tema
- Etichette stepper: verificare `lang/it/segnalazione.php` step keys vs reference Design Comuni
- Larghezza form body: da `col-lg-10` a Tailwind `lg:w-2/3` (parity `col-lg-8` reference)
- Sezione "Contatta il comune" mancante: azione = CMS block nel JSON tests.segnalazione-crea
- Story BMAD: 7-103 (ready-for-dev)
