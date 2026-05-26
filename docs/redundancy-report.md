# Redundancy Report – Fixcity Module

## 1. Translation Namespace Inconsistency
- **Namespace `fixcity::ticket`** – Used throughout backend code, Filament resources, enums, and internal forms (e.g., `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`, `summary.blade.php`).
- **Namespace `fixcity::segnalazione`** – Used in front‑office Blade components under `Themes/Sixteen` (e.g., `resources/views/components/blocks/tests/segnalazione-01-privacy.blade.php`) and some legacy wizard steps.
- **Impact:** Two parallel namespaces for essentially the same domain lead to duplicated key files, potential drift, and extra translation maintenance.
- **Recommendation:** Decide on a single source of truth:
  - Keep `fixcity::ticket` for all backend and shared UI components.
  - Either migrate front‑office templates to `fixcity::ticket` or maintain a thin mapping layer that forwards `fixcity::segnalazione` keys to the `ticket` equivalents.
  - Update documentation to reflect the chosen namespace and remove obsolete keys.

## 2. Duplicate Blade Shim for Alpine Global Functions (related to Geo)
- The Blade shim defined in `Themes/Sixteen/resources/views/partials/alpine-livewire-bootstrap-header.blade.php` duplicates the `geoMapPickerField` registration performed in `Modules/Geo/resources/js/filament/map-picker.js`.
- **Impact:** Redundant global function definitions increase bundle size and risk inconsistent behavior.
- **Recommendation:** Remove the shim after confirming the shared JS module is loaded before any markup that calls the function.

*Other minor redundancies (e.g., repeated comment blocks) were not deemed critical for this analysis.*
