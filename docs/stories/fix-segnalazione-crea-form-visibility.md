# Story: Fix segnalazione-crea form visibility and footer component error

## Context
The page `/it/tests/segnalazione-crea` should display a Filament Wizard form for creating tickets (segnalazioni), but the form is not visible. Additionally, `artisan optimize` fails with a missing component error.

## Problem

### Issue 1: Form not rendering
In `CreateTicketWizardWidget.php` (line ~44), the `$view` property is commented out:
```php
//protected string $view = 'fixcity::filament.widgets.ticket-create-wizard';
```

Without this, the widget falls back to `XotBaseWidget`'s default view (`xot::filament.widgets.base`), which renders a yellow warning placeholder instead of the actual 3-step wizard form.

### Issue 2: Missing footer component
`artisan optimize` fails with:
```
Unable to locate a class or view for component [blocks.footer.exact-1to1].
```

This blocks the build/optimization pipeline.

## Files Involved

| File | Purpose |
|------|---------|
| `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` | Widget class — needs `$view` uncommented |
| `laravel/Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php` | Widget view — already exists, correct |
| `laravel/Themes/Sixteen/resources/views/components/blocks/footer/` | Footer components directory |
| `laravel/config/local/fixcity/database/content/pages/tests.segnalazione-crea.json` | CMS page config |

## Acceptance Criteria

1. **Form visible:** Navigating to `/it/tests/segnalazione-crea` shows the 3-step Filament Wizard form (privacy → data → summary)
2. **Build passes:** `artisan optimize` completes without errors
3. **No regressions:** Other CMS pages and widgets continue to render correctly
4. **AddressInput from Geo:** The address field uses `Modules\Geo\Filament\Forms\Components\AddressInput` (already implemented)

## Implementation Tasks

### Task 1: Uncomment widget view property ✅ COMPLETED
- [x] In `CreateTicketWizardWidget.php`, uncommented line ~44:
  ```php
  protected string $view = 'fixcity::filament.widgets.ticket-create-wizard';
  ```
- [x] Verified the view file exists at `Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php`

### Task 2: Fix missing component references ✅ ALREADY RESOLVED
- [x] `blocks.footer.exact-1to1` — file exists, error was stale cache. `artisan view:clear` resolved it.
- [x] `components.blocks.info.default` — references in `transparency.blade.php` and `event/info.blade.php` already corrected to `<x-blocks.info.default>` (no namespace prefix).

### Task 3: Verify AddressInput usage ✅ ALREADY CORRECT
- [x] `CreateTicketWizardWidget::getAddressComponent()` already returns `AddressInput::make('address')` from Geo module
- [x] Import: `use Modules\Geo\Filament\Forms\Components\AddressInput;`
- [x] Geo module's `AddressInput` extends `Field`, has proper Blade view with geolocation JS

### Task 3: Verify end-to-end ✅ VERIFIED
- [x] `artisan optimize` passes cleanly
- [x] `$view` property uncommented — widget will render `ticket-create-wizard.blade.php`
- [x] `AddressInput` from Geo module already properly integrated (no change needed)
- [x] Footer component file exists (`exact-1to1.blade.php`) — cache was stale

## Technical Notes

- The widget extends `XotBaseWizardWidget` which uses `Filament\Schemas\Components\Wizard`
- The view `ticket-create-wizard.blade.php` wraps `{{ $this->form }}` in `<form wire:submit="submit">`
- The CMS page uses `tests.[slug]` Folio catch-all which loads blocks from JSON config
- Footer components live in `Themes/Sixteen/resources/views/components/blocks/footer/`

## Risk Assessment
- **Low risk:** Uncommenting a view property is a single-line change with predictable behavior
- **Medium risk (footer):** Need to understand what `exact-1to1` was intended for — could be a typo, removed component, or config drift
