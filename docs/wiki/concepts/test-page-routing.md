---
name: test-page-routing
description: Maps URL slugs like `/it/tests/segnalazione-crea` to Blade views and JSON configuration files.
type: concept
---

# Test Page Routing Overview

## How `/it/tests/segnalazione-crea` resolves

1. **URL → Slug Mapping**  
   The Fixcity module defines a `routes.php` mapping where each test slug (`segnalazione-crea`, `altro-test`, …) points to a Blade view located at  
   ```
   laravel/Themes/Sixteen/resources/views/pages/tests/{slug}.blade.php
   ```  
   The routing controller reads the slug from the request path and selects the corresponding view.

2. **View Selection Logic** (simplified):
   ```php
   $slug = $request->route('slug'); // e.g. "segnalazione-crea"
   $view = "pages.tests.$slug";   // resolves to "pages/tests/segnalazione-crea"
   return response()->view($view);
   ```

3. **JSON Configuration Inclusion**  
   Inside the selected Blade view the following directive loads localized JSON data:
   ```blade
   @php
       $jsonPath = config('fixcity.test_pages.' . $slug . '.path');
       $jsonContent = file_get_contents(resource_path($jsonPath));
  @endphp
   ```
   The resolved path lives under `laravel/config/local/fixcity/database/content/pages/`.  
   Example for *segnalazione-crea*:
   ```
   laravel/config/local/fixcity/database/content/pages/tests.segnalazione-crea.json
   ```

4. **JSON Data Usage**  
   The parsed JSON typically provides:
   - Translation keys (`title`, `description`, etc.)
   - Component layout hints
   - Validation rules for the test form
   This data is passed to Livewire components rendered within the Blade layout.

## Why the Error Happens

- **Missing or misnamed JSON file** – If the file `tests.segnalazione-crea.json` is absent, mis‑spelled, or excluded by `.gitignore`, `file_get_contents()` returns `false`, causing a PHP warning and breaking the view.
- **Incorrect config key** – The `config('fixcity.test_pages.{slug}.path')` entry must exactly match the filename (including the `.json` extension). A typo prevents the correct path from being built.
- **Cache stale after file addition** – Adding a new JSON file without running `php artisan config:cache` leaves the cached config outdated, so the resolved path differs from the real file location.

## Preventing the Issue

| Step | Action |
|------|--------|
| **1. Consistent Namespace** | Keep all test JSON files under `laravel/config/local/fixcity/database/content/pages/` with the naming convention `tests.{slug}.json`. |
| **2. Config Synchronization** | After creating or renaming a JSON file, run `php artisan config:cache` to refresh the `fixcity.test_pages` config array. |
| **3. Validation Hook** | Add a pre‑commit hook (e.g., via `git` `pre-commit` script) that greps for `tests.*.json` references in Blade files and verifies existence of the corresponding file. |
| **4. LLM‑Wiki Documentation** | Keep this routing description in `docs/wiki/concepts/test-page-routing.md` and update the wiki log whenever the convention changes. |
| **5. CI Guard** | Include a simple test case in the test suite: `assert(file_exists(resource_path(config('fixcity.test_pages.{slug}.path')));` to catch missing JSON files early. |

## Documentation Ingestion Checklist

- [x] Create `test-page-routing.md` (completed)  
- [x] Add entry to `docs/wiki/concepts/index.md` – `- [Test Page Routing](concepts/test-page-routing.md) — maps slugs to Blade views`  
- [x] Update `docs/wiki/log.md` with a timestamped entry: `2026-04-23: Added routing guide for test pages`  
- [x] Index new doc: `ctx_batch_execute commands: [{label:"Concept – Test Page Routing",command:"qmd index --path Modules/Fixcity/docs/wiki/concepts/test-page-routing.md"}]`  

By keeping the mapping logic centralized, validating file existence, and documenting every step in the LLM‑Wiki, future changes stay traceable and the `BadMethodCallException` / missing‑JSON errors are eliminated. 

Sources:  
- `Modules/Fixcity/routes.php` (routing mapping)  
- `Modules/Fixcity/config/test_pages.php` (slug‑to‑path config)  
- Blade view template `pages/tests/{slug}.blade.php`  
- QMD indexing workflow  
