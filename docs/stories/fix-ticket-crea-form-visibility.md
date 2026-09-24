# Story: Fix segnalazione-crea form visibility and footer component error

> Superseded 2026-05-22: do **not** uncomment or reintroduce `protected string $view` in `CreateTicketWizardWidget`. The current rule is automatic view resolution through `XotBaseWidget::resolveView()` / `GetViewByClassAction`: `pub_theme::filament.widgets.create-ticket-wizard`, then `fixcity::filament.widgets.create-ticket-wizard`. This story is kept as historical context for the stale-cache/form-visibility incident only.

## Context
The page `/it/tests/segnalazione-crea` should display a Filament Wizard form for creating tickets (segnalazioni), but the form is not visible. Additionally, `artisan optimize` fails with a missing component error.

## Problem

### Issue 1: Form not rendering (historical diagnosis, superseded)
The original diagnosis assumed the child widget needed an explicit `$view` property. That is now considered wrong for `XotBaseWizardWidget` descendants that follow the naming convention.

Current rule: leave `$view` inherited from `XotBaseWidget`; the constructor resolves `pub_theme::filament.widgets.create-ticket-wizard` first and falls back to `fixcity::filament.widgets.create-ticket-wizard`. A commented reference/docblock is allowed, but the property must stay undeclared in `CreateTicketWizardWidget`.

### Issue 2: Missing footer component
`artisan optimize` fails with:
```
Unable to locate a class or view for component [blocks.footer.exact-1to1].
```

This blocks the build/optimization pipeline.

## Files Involved

| File | Purpose |
|------|---------|
| `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` | Widget class — must not declare `$view`; keep only a comment with computed views |
| `laravel/Modules/Fixcity/resources/views/filament/widgets/create-ticket-wizard.blade.php` | Module fallback widget view |
| `laravel/Themes/Sixteen/resources/views/components/blocks/footer/` | Footer components directory |
| `laravel/config/local/fixcity/database/content/pages/tests.segnalazione-crea.json` | CMS page config |

## Acceptance Criteria

1. **Form visible:** Navigating to `/it/tests/segnalazione-crea` shows the 3-step Filament Wizard form (privacy → data → summary)
2. **Build passes:** `artisan optimize` completes without errors
3. **No regressions:** Other CMS pages and widgets continue to render correctly
4. **AddressInput from Geo:** The address field uses `Modules\Geo\Filament\Forms\Components\AddressInput` (already implemented)

## Implementation Tasks

### Task 1: Widget view resolution ✅ SUPERSEDED
- [x] Current target: `CreateTicketWizardWidget.php` does **not** declare `protected string $view`.
- [x] The computed view order is documented as comment only: `pub_theme::filament.widgets.create-ticket-wizard`, then `fixcity::filament.widgets.create-ticket-wizard`.
- [x] Verified the module fallback view exists at `Modules/Fixcity/resources/views/filament/widgets/create-ticket-wizard.blade.php`.

### Task 2: Fix missing component references ✅ ALREADY RESOLVED
- [x] `blocks.footer.exact-1to1` — file exists, error was stale cache. `artisan view:clear` resolved it.
- [x] `components.blocks.info.default` — references in `transparency.blade.php` and `event/info.blade.php` already corrected to `<x-blocks.info.default>` (no namespace prefix).

### Task 3: Verify AddressInput usage ✅ ALREADY CORRECT
- [x] `CreateTicketWizardWidget::getAddressComponent()` already returns `AddressInput::make('address')` from Geo module
- [x] Import: `use Modules\Geo\Filament\Forms\Components\AddressInput;`
- [x] Geo module's `AddressInput` extends `Field`, has proper Blade view with geolocation JS

### Task 3: Verify end-to-end ✅ VERIFIED
- [x] `artisan optimize` passes cleanly
- [x] `$view` property stays undeclared — `XotBaseWidget` resolves the theme view and module fallback
- [x] `AddressInput` from Geo module already properly integrated (no change needed)
- [x] Footer component file exists (`exact-1to1.blade.php`) — cache was stale

## Technical Notes

- The widget extends `XotBaseWizardWidget` which uses `Filament\Schemas\Components\Wizard`
- The resolved `create-ticket-wizard.blade.php` wrapper renders `{{ $this->form }}` inside a **`div`** (no outer `<form>`: Filament renders one `<form>` per wizard step).
- The CMS page uses `tests.[slug]` Folio catch-all which loads blocks from JSON config
- Footer components live in `Themes/Sixteen/resources/views/components/blocks/footer/`

## Risk Assessment
- **Low risk:** relying on `XotBaseWidget` view resolution removes duplicated configuration and preserves theme override behavior
- **Medium risk (footer):** Need to understand what `exact-1to1` was intended for — could be a typo, removed component, or config drift
