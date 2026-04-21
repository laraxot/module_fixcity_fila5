# Fixcity Wiki Log

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
