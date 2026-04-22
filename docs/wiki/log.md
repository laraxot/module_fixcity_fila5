# 2026-04-22

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
  - `database/migrations/2026_04_20_000009_create_profiles_table.php`
- pages:
  - `concepts/profiles-uuid-contract.md` (new)
- summary:
  - la migrazione owner `create_profiles_table` di Fixcity ora dichiara `uuid` nello schema base
  - aggiunto anche guard idempotente in `tableUpdate()` per installazioni legacy con tabella `profiles` senza colonna `uuid`
  - timestamp migrazione riallineato per mantenere la regola "1 modello = 1 migrazione"

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
