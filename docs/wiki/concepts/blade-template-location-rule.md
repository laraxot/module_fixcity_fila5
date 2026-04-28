---
name: blade-template-location-rule
description: Prevent stray Blade files for dynamic wizard pages; enforce Livewire component + JSON config.
type: concept
---

# Blade Template Location Rule

## Why the Blade file `resources/views/pages/segnalazione-crea.blade.php` **must not exist**

1. **Dynamic wizard pages are rendered by Livewire widgets** – the `CreateTicketWizardWidget` builds the UI from a JSON configuration located at `config/local/fixcity/database/content/pages/tests.segnalazione-crea.json`.  The Blade view is only a thin wrapper used by the widget; having a full Blade file duplicates the markup and bypasses the widget logic.
2. **Routing expects JSON‑driven content** – the route `/it/tests/segnalazione-crea` loads the JSON and passes it to the Livewire component. If a Blade file with the same name is present, Laravel may resolve the view first, causing the JSON never to be consulted, leading to stale UI and `BadMethodCallException` errors when the widget expects methods that the Blade file does not provide.
3. **Maintainability** – all wizard steps share a common layout supplied by the theme. Adding a dedicated Blade file fragments this layout and breaks the **DRY** principle enforced by the `blade-component-extraction` rule.
4. **Testing & CI** – static analysis tools (phpstan, phpmd, phpinsights) only scan PHP files. A stray Blade file is invisible to these tools, allowing hidden bugs to slip in.

## Correct placement

- **Livewire Widget** – create/keep the widget class in `Modules/Fixcity/app/Filament/Widgets/` (e.g., `CreateTicketWizardWidget`).
- **JSON configuration** – keep the page‑specific data in `config/local/fixcity/database/content/pages/tests.segnalazione-crea.json`.
- **Blade wrapper (optional)** – a generic wrapper such as `themes/Sixteen/resources/views/pages/tests/wizard.blade.php` that simply includes the Livewire component:
  ```blade
  @livewire('fixcity::create-ticket-wizard')
  ```
  This wrapper is shared across all test pages; no per‑slug Blade files.

## Enforcement

- **Pre‑commit hook** – a script that searches for `resources/views/pages/**/*.blade.php` matching a test slug and fails if found.
- **CI test** – add a PHPUnit test that asserts `!file_exists(resource_path('views/pages/segnalazione‑crea.blade.php'))`.
- **Documentation** – keep this rule in the LLM‑Wiki (see this file) and reference it from `docs/wiki/index.md`.

---

**Related Rules**
- `blade-component-extraction-rule` – ensures reusable components are extracted.
- `no-page-specific-css` – avoid CSS scoped to a single Blade file.

---
