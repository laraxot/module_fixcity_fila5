# Filament Wizard Architecture & Theme Boundary

## Philosophy: Separation of Concerns (Filament v5)

### Core Principle
- **Module owns schema/logic**: Wizard steps, form fields, validation rules, business logic
- **Theme owns appearance**: CSS classes, visual styling via `pub_theme::components.wizard`

### Why This Matters

1. **DRY + KISS**: Don't recreate Filament's Wizard component - use it
2. **Theme Override Pattern**: Filament looks for `pub_theme::components.wizard` before falling back to default
3. **No Inline Styles**: Module blade files must not contain `<style>` blocks or inline Tailwind classes

## Filament Wizard Architecture (v5)

### Source Files
- **Blade**: `vendor/filament/schemas/resources/views/components/wizard.blade.php`
- **PHP**: `vendor/filament/schemas/src/Components/Wizard.php`

### Key Methods
```php
Wizard::make($steps)
    ->startOnStep(int)      // Initial step
    ->columnSpanFull()      // Full width
    ->skippable(bool)       // Allow skipping
    ->persistStepInQueryString('step')  // Query param persistence
    ->view('pub_theme::components.wizard')  // Theme override point
```

## Theme Boundary Implementation

### 1. Module Widget (Schema/Logic)
```php
// XotBaseWizardWidget or widget in your module
public function getFormSchema(): array
{
    return [
        Wizard::make($this->getSteps())
            ->view('pub_theme::components.wizard'),  // Theme view injection
    ];
}
```

### 2. Theme Blade (`pub_theme::components.wizard`)
Override CSS classes only:
- Do NOT modify structure or logic
- Only change CSS class names
- Reference: `filament::schemas.components.wizard`

## Common Mistakes to Avoid

### ❌ Wrong: Custom Blade Wrapper
```blade
{{-- DON'T: Custom wrapper with inline styles --}}
<div class="my-custom-wrapper">
    {{ $this->form }}
</div>
```

### ✓ Correct: Minimal Wrapper
```blade
{{-- DO: Just render the form, let Filament handle wizard --}}
<x-filament-widgets::widget>
    {{ $this->form }}
</x-filament-widgets::widget>
```

### ❌ Wrong: Reinventing Wizard Logic
```php
// DON'T: Reimplement nextStep/previousStep in widget
public function nextStep(): void { ... }
```

### ✓ Correct: Use Filament's HasWizard
```php
// DO: Let Filament handle via Wizard component
// Widget extends XotBaseWizardWidget which uses Wizard component
```

## Visual Parity Strategy

### Admin vs Public URLs
- `/fixcity/admin/tickets/create` → Admin theme (default Filament)
- `/it/tests/segnalazione-crea` → Public theme (Sixteen + custom CSS)

Both use the SAME widget/schema. Only CSS differs via `pub_theme::components.wizard`.

## Checklist for Wizard Implementation

- [ ] Widget extends proper base (XotBaseWizardWidget recommended)
- [ ] No custom nextStep/previousStep methods (Filament handles)
- [ ] No `<style>` blocks in blade
- [ ] `pub_theme::components.wizard` exists and provides CSS only
- [ ] Both admin and public URLs render same schema
- [ ] Query string `?step=` persists correctly

## References

- [Filament Wizard Source](https://github.com/filamentphp/filament/blob/5.x/packages/schemas/resources/views/components/wizard.blade.php)
- [Filament Wizard PHP](https://github.com/filamentphp/filament/blob/5.x/packages/schemas/src/Components/Wizard.php)