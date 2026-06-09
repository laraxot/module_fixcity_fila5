# Fixcity Activity Log

> **Module**: Fixcity
> **Purpose**: Append-only chronological record of wiki activity
> **Created**: 2026-04-15

---

## [2026-04-15] maintenance | Initial wiki setup
- Created: llm-wiki/ directory structure
- Created: AGENTS.md (agent instructions)
- Created: index.md (content catalog)
- Created: log.md (this file)
- Directories initialized:
  - raw/{decisions,patterns,troubleshooting,articles}
  - concepts/, entities/, sources/, comparisons/
  - decisions/, troubleshooting/, _archive/, _templates/
- Commit: docs: initialize Fixcity module wiki

---

---

## [2026-04-21] audit | Green Branding and Wizard Alignment
- **Decision**: Adopted Green Branding Strategy (Comune Logo Green #007A52) over standard Design Comuni Blue.
- **Header Parity**: Forced Green/Dark-Green backgrounds for Slim, Center, and Navbar modules.
- **Wizard UX**: Implemented `flex-column` vertical stack for navigation to ensure "Avanti" button is directly under form content (checkboxes) and provides large touch targets for mobile.
- **Docs**: Created `concepts/header-green-branding-rule.md`, updated `concepts/visual-parity-report.md`.
- **Refactoring**: Consolidated `!important` overrides in `app.css`.

## [2026-04-21] fix | Livewire cache table missing on update
- **Incident**: `SQLSTATE[42S02]` on `POST /livewire-*/update` for table `cache`.
- **Cause**:
  Livewire checksum failure limiter uses Laravel `RateLimiter`;
  with DB-backed cache runtime, `cache` storage tables are mandatory.
- **Fix**: executed targeted migration `database/migrations/2026_04_21_111944_create_cache_table.php`.
- **Verification**: `cache` and `cache_locks` tables exist.
- **Knowledge update**:
  linked concept `../wiki/concepts/livewire-cache-table-rate-limiter.md`.
- **Hardening**:
  duplicate migration `2026_04_21_112114_create_cache_table`
  made idempotent (`hasTable` guard) and executed safely.

## [2026-04-21] fix | Segnalazione runtime asset integrity
- **Incident**: 404 su `header-fix.css`, `mobile-map-fix.css`, `themes/Geo/js/geo.js` + errori Alpine/Livewire a cascata.
- **Fix**:
  rimosse inclusioni CSS hardcoded nel layout tema,
  ripristinato `public_html/themes/Geo/js/geo.js`,
  riallineati asset `livewire:publish --assets` + `filament:assets`.
- **Stability**:
   registrazione `geoMapPickerField` resa robusta (init immediata + fallback `alpine:init`).
- **Knowledge update**:
   linked concept `../wiki/concepts/segnalazione-runtime-asset-integrity.md`.

---

## [2026-06-02] visual-parity | /it/# Design Comuni alignment improvements

- **heading.blade.php**: changed column from col-lg-10 to col-lg-8 for better side margins
- **Built theme assets**: with markercluster support for proper map visualization
- **home.json**: updated to use asset() helper for tickets.json path
- **SSoT architecture**: LoadTicketsGeoJsonAction → BuildSegnalazioniFilterAggregateAction → SegnalazioniFilterViewModel
- **Documentation**: rules/no-services-rule.md created
