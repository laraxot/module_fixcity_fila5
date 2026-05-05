# Wizard Zen Philosophy - "Same Wizard, Different Dresses"

**Date:** 2026-05-05
**Type:** architecture-concept
**Sources:** XotBaseWizardWidget.php, Wizard.php, CreateTicketWizardWidget.php
**Confidence:** verified
**Tags:** wizard, zen, philosophy, theme-boundary, fixcity
**Related:** xotbase-wizard-architecture.md, ticketform-pattern-reference.md

## The Core Insight

```
SAME Wizard Component ≠ SAME Visual Presentation

┌─────────────────────────────────────────┐
│   Filament Wizard (vendor)               │
│   - Alpine.js state                       │
│   - Step navigation                     │
│   - Query string persistence             │
└─────────────────────────────────────────┘
           │                              │
           ▼                              ▼
┌──────────────────────┐      ┌──────────────────────┐
│  Frontoffice       │      │  Admin Panel      │
│  pub_theme::      │      │  filament.wizard  │
│  components.wizard │      │  (default)       │
│  (Sixteen theme)  │      │  (admin panel)   │
└──────────────────────┘      └──────────────────────┘
           │                              │
           ▼                              ▼
/it/tests/segnalazione-crea      /fixcity/admin/tickets/create
```

## What This Means in Practice

### 1. Module Owns Logic (CreateTicketWizardWidget)

```php
// ✅ CORRECT - Module handles business logic ONLY
class CreateTicketWizardWidget extends XotBaseWizardWidget {
    // Business rules
    public function getWizardSteps(): array {
        return TicketForm::getWizardSteps();
    }
    
    // State management
    protected function initWizardState(): void {
        $this->wizardStartStep = $this->resolveInitialStepFromQuery();
    }
    
    // Submission logic
    public function submit(): void {
        $data = $this->form->getState();
        Ticket::create($data);
        $this->redirectAfterSuccess();
    }
}
```

**Module does NOT own:**
- ❌ CSS styling
- ❌ Blade templates for presentation
- ❌ JavaScript for UI interactions

### 2. Theme Owns Presentation (Sixteen)

```blade
{{-- ✅ CORRECT - Theme handles visual presentation --}}
{{-- laravel/Themes/Sixteen/resources/views/components/wizard.blade.php --}}

<div x-data="wizardSchemaComponent({...})">
    {{-- Stepper with Design Comuni styling --}}
    <ol class="fi-sc-wizard-header">
        <li class="fi-sc-wizard-header-step">
            {{-- Custom CSS makes this look like Design Comuni --}}
        </li>
    </ol>
    
    {{-- Wizard content rendered by Filament --}}
    @foreach ($steps as $step)
        {{ $step }}
    @endforeach
</div>
```

**Theme does NOT own:**
- ❌ Business logic
- ❌ Schema definitions
- ❌ Form validation rules

### 3. Filament Owns Core (vendor)

```php
// ✅ CORRECT - Filament handles core functionality
// vendor/filament/schemas/src/Components/Wizard.php

class Wizard extends Component {
    // Alpine.js state management
    // Step navigation (nextStep, previousStep)
    // Query string persistence
    // Skippable behavior
}
```

## Visual Parity: Frontoffice vs Admin

### Frontoffice (`/it/tests/segnalazione-crea`)

**URL:** `http://127.0.0.1:8000/it/tests/segnalazione-crea`
**Purpose:** Citizen-facing ticket creation
**Theme:** Sixteen with `pub_theme::components.wizard`
**Visual Requirements:**
- Stepper visible with step names (not just "1/3")
- "Avanti" button (green, Design Comuni style)
- Checkbox: "Ho letto e compreso l'informativa sulla privacy"
- Font: Titillium Web (via CSS)
- Background: White content area, green header

### Admin (`/fixcity/admin/tickets/create`)

**URL:** `http://127.0.0.1:8000/fixcity/admin/tickets/create`
**Purpose:** Operator ticket creation in Filament panel
**Theme:** Filament admin (default rendering)
**Visual Requirements:**
- Standard Filament wizard styling
- Admin panel layout (sidebar, topbar)
- Same `TicketForm::getWizardSteps()` logic
- Different visual presentation (admin vs frontoffice)

## Key Implementation Details

### How to Switch "Dresses" in Code

```php
// XotBaseWizardWidget.php
protected function makeWizard(array $steps): Wizard {
    $wizard = Wizard::make($steps)
        ->startOnStep(fn (): int => $this->wizardStartStep)
        ->columnSpanFull()
        ->skippable($this->hasSkippableWizardSteps());
    
    // KEY: Switch "dress" based on context
    if (! inAdmin()) {
        // Frontoffice: use theme "dress"
        $wizard = $wizard->view('pub_theme::components.wizard');
    }
    // Admin: uses default Filament rendering (no custom view)
    
    return $wizard;
}
```

### Safe Functions Are FUNDAMENTAL

```php
// ✅ CORRECT - Safe functions MUST be used
use function Safe\file_put_contents;  // Handles permission errors
use function Safe\json_encode;        // Handles JSON errors
use function Safe\preg_match;         // Handles regex errors

// Example usage in CreateTicketWizardWidget:
$slug = SafeStringCastAction::cast(
    $this->blockData['confirmation_slug'] 
    ?? config('fixcity.wizard.confirmation_slug', 'segnalazione-04-conferma')
);
```

**Never remove Safe functions** - they provide error handling that vanilla PHP lacks.

## Quality Gates (Run After EVERY Change)

### Command Summary

```bash
# 1. PHPStan (Level 5)
cd laravel && php vendor/bin/phpstan analyse Modules/Fixcity/app/Filament/Widgets/ --level=5

# 2. PHPMD (.phar)
php /home/zorin/.local/bin/phpmd.phar laravel/Modules/Fixcity/app/Filament/Widgets/ text cleancode

# 3. PHP Insights
cd laravel && php vendor/bin/phpinsights analyse --no-interaction

# 4. Pint
cd laravel && php vendor/bin/pint Modules/Fixcity/app/Filament/Widgets/ --format=agent

# 5. Pest Tests
cd laravel && php artisan test --compact --filter=Wizard

# 6. Puppeteer (Visual)
# Check frontoffice: http://127.0.0.1:8000/it/tests/segnalazione-crea

# 7. Playwright (Visual)
# Check admin: http://127.0.0.1:8000/fixcity/admin/tickets/create
```

## Documentation Contract

After implementation, update:

1. `laravel/Themes/Sixteen/docs/wiki/concepts/wizard-zen-philosophy.md` ✅
2. `laravel/Modules/Fixcity/docs/wiki/concepts/wizard-frontoffice-dress.md`
3. Update all `docs/wiki/index.md`
4. Update all `docs/wiki/log.md` with 2026-05-05 entry
5. Run QMD ingest when available

## Anti-Patterns to Avoid

❌ **Reinventing HasWizard trait** - use existing Filament traits
❌ **Removing Safe functions** - they are mandatory for error handling
❌ **Module CSS/JS** - theme owns presentation
❌ **Inline `<style>` in module Blade** - use theme CSS
❌ **Skipping quality gates** - run ALL checks after every change
❌ **Same "dress" for admin and frontoffice** - they serve different users

## Related Documentation

- [XotBaseWizardWidget Architecture](xotbase-wizard-architecture.md)
- [TicketForm Pattern Reference](../Fixcity/docs/wiki/concepts/ticketform-pattern-reference.md)
- [Theme Dress Pattern](../../Themes/Sixteen/docs/wiki/concepts/theme-dress-pattern.md)
- [Safe Functions Importance](../../Xot/docs/wiki/concepts/safe-functions-mandatory.md)
