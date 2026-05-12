---
title: "Wizard Theme Integration Contract"
type: concept
sources: []
confidence: high
created: 2026-05-04
updated: 2026-05-04
tags: [wizard, filament, theme-integration, contract, api, fixcity]
related:
  - ../../../../Themes/Sixteen/docs/wiki/concepts/filament-wizard-custom-component.md
  - ../../../../docs/wiki/concepts/laraxot-theme-module-separation.md
---

# Wizard Theme Integration Contract

> **Principle**: Fixcity Module owns the business logic. Sixteen Theme owns the presentation.
>
> **Contract**: Module exposes Wizard via `$this->form`. Theme renders via `<x-pub_theme::wizard />`.

## The Contract

### Module Responsibilities (Fixcity)

```php
// CreateTicketWizardWidget.php
class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    public function getFormSchema(): array
    {
        return [
            Wizard::make($this->getSteps())
                ->key('wizard')  // Key for theme to access
                ->startOnStep(fn () => $this->wizardStartStep)
                ->skippable($this->hasSkippableWizardSteps()),
        ];
    }
    
    public function getSteps(): array
    {
        return [
            Step::make(__('fixcity::ticket_wizard.steps.privacy.label'))
                ->description(__('fixcity::ticket_wizard.steps.privacy.description'))
                ->schema(TicketForm::getFrontofficePrivacySchema('#')),
            
            Step::make(__('fixcity::ticket_wizard.steps.data.label'))
                ->description(__('fixcity::ticket_wizard.steps.data.description'))
                ->schema(TicketForm::getDataSchema()),
            
            Step::make(__('fixcity::ticket_wizard.steps.summary.label'))
                ->description(__('fixcity::ticket_wizard.steps.summary.description'))
                ->schema(TicketForm::getSummarySchema()),
        ];
    }
}
```

**Module provides:**
1. `getFormSchema()` - Returns form with Wizard component
2. `getSteps()` - Returns array of Step components
3. `$this->form` - Livewire property available in Blade
4. `wizardStartStep` - Current step index (1-based)
5. Translations via `fixcity::ticket_wizard.steps.{name}.label`

### Theme Responsibilities (Sixteen)

```blade
{{-- create-ticket-wizard.blade.php --}}
<x-filament-widgets::widget>
    {{-- Wrapper markup --}}
    <div class="container">
        <x-pub_theme::wizard :form="$this->form" />
    </div>
</x-filament-widgets::widget>
```

**Theme provides:**
1. `pub_theme::wizard` component - Receives `$this->form`
2. Stepper UI - Design Comuni style
3. CSS styling - Tailwind + Design Comuni tokens
4. Responsive behavior - Mobile adaptations

## Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│  1. FIXCITY WIDGET                                           │
│  CreateTicketWizardWidget::getFormSchema()                    │
│  └── Returns: [Wizard::make([Step, Step, Step])]            │
└────────────────────────┬──────────────────────────────────────┘
                         │ Livewire renders form
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  2. LIVEWIRE STATE                                           │
│  $this->form → contains Wizard component                     │
│  $this->wizardStartStep → current step (1, 2, 3)            │
└────────────────────────┬──────────────────────────────────────┘
                         │ Passed to Blade
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  3. THEME VIEW                                               │
│  create-ticket-wizard.blade.php                              │
│  └── <x-pub_theme::wizard :form="$this->form" />           │
└────────────────────────┬──────────────────────────────────────┘
                         │ Component receives Wizard
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  4. THEME COMPONENT                                          │
│  pub_theme::wizard.blade.php                                  │
│  └── Extracts: $form->getComponent('wizard')                  │
│  └── Calls: $wizard->getSteps()                              │
│  └── Renders: Design Comuni stepper                          │
│  └── Outputs: {{ $wizard }} (native Filament wizard)         │
└─────────────────────────────────────────────────────────────┘
```

## API Reference

### What Module Exposes to Theme

| Property/Method | Type | Description |
|----------------|------|-------------|
| `$this->form` | `Form` | Livewire form containing wizard |
| `getFormSchema()` | `array` | Form schema definition |
| `getSteps()` | `Step[]` | Array of wizard steps |
| `wizardStartStep` | `int` | Current step (1-based) |
| `wizardMaxStep()` | `int` | Total number of steps |

### What Wizard Component Provides

```php
// From Filament\Schemas\Components\Wizard
$wizard->getSteps(): array;           // Step[]
$wizard->getStartStep(): int;
$wizard->isSkippable(): bool;
$wizard->getKey(): string;            // 'wizard'
$wizard->getStatePath(): string;      // Livewire property path

// From Filament\Schemas\Components\Wizard\Step
$step->getLabel(): string;
$step->getDescription(): ?string;
$step->getIcon(): ?string;
$step->getSchema(): array;            // Form fields for this step
```

## Theme Access Patterns

### Pattern 1: Via Form Prop (Recommended)
```blade
<x-pub_theme::wizard :form="$this->form" />
```

Component extracts wizard internally:
```php
$wizard = $form?->getComponent('wizard');
```

### Pattern 2: Via Wizard Prop
```blade
@php
$wizard = $this->getForm()?->getComponent('wizard');
@endphp
<x-pub_theme::wizard :wizard="$wizard" />
```

### Pattern 3: Direct Access (Not Recommended)
```blade
{{-- Don't do this - bypasses component abstraction --}}
@php
$wizard = $this->getForm()?->getComponent('wizard');
$steps = $wizard?->getSteps() ?? [];
@endphp
@foreach($steps as $step)
    {{ $step->getLabel() }}
@endforeach
```

## Testing the Contract

### Verify Module Provides Correct Data
```php
// In CreateTicketWizardWidgetTest.php
public function test_wizard_has_three_steps()
{
    $widget = new CreateTicketWizardWidget();
    $steps = $widget->getSteps();
    
    $this->assertCount(3, $steps);
    $this->assertEquals('privacy', $steps[0]->getId());
    $this->assertEquals('data', $steps[1]->getId());
    $this->assertEquals('summary', $steps[2]->getId());
}

public function test_step_labels_are_translated()
{
    $widget = new CreateTicketWizardWidget();
    $steps = $widget->getSteps();
    
    $this->assertEquals(
        __('fixcity::ticket_wizard.steps.privacy.label'),
        $steps[0]->getLabel()
    );
}
```

### Verify Theme Receives Data
```blade
{{-- Debug in theme blade --}}
@php
$wizard = $this->getForm()?->getComponent('wizard');
$steps = $wizard?->getSteps() ?? [];
@endphp

<!-- Should output dynamic data from module, not hardcoded -->
@foreach($steps as $index => $step)
    <div>Step {{ $index + 1 }}: {{ $step->getLabel() }}</div>
@endforeach
```

## Anti-Patterns to Avoid

### ❌ Hardcoded Steps in Theme
```blade
{{-- WRONG: Theme decides what steps exist --}}
@php
$steps = ['Privacy', 'Data', 'Summary']; // ❌
@endphp
```

### ❌ Duplicated Logic
```blade
{{-- WRONG: Logic duplicated from module --}}
@if($currentStep === 1)
    {{ __('fixcity::steps.privacy') }} {{-- ❌ Hardcoded key --}}
@endif
```

### ❌ Bypassing Component
```blade
{{-- WRONG: Direct access instead of component --}}
<div class="custom-stepper">
    @foreach($this->getSteps() as $step) {{-- ❌ --}}
        ...
    @endforeach
</div>
{{ $this->form }}
```

### ✅ Correct Usage
```blade
{{-- RIGHT: Use component, delegate to module --}}
<x-pub_theme::wizard :form="$this->form" />
```

## Integration Checklist

### Module Side
- [ ] `getFormSchema()` returns Wizard with key 'wizard'
- [ ] `getSteps()` returns translated Step components
- [ ] `wizardStartStep` tracks current step
- [ ] Translations in `fixcity::ticket_wizard.steps.{name}.label`

### Theme Side
- [ ] `pub_theme::wizard` component exists
- [ ] Component extracts wizard from `$form` prop
- [ ] Uses `$wizard->getSteps()` for dynamic steps
- [ ] Renders native `{{ $wizard }}` at end
- [ ] CSS for Design Comuni stepper

### Integration
- [ ] Theme view uses `<x-pub_theme::wizard :form="$this->form" />`
- [ ] No hardcoded step data in theme
- [ ] Adding step in module auto-shows in UI
- [ ] Step labels update when translations change

## References

- **Theme Doc**: `Themes/Sixteen/docs/wiki/concepts/filament-wizard-custom-component.md`
- **Architecture Doc**: `docs/wiki/concepts/laraxot-theme-module-separation.md`
- **Story 8-111**: `_bmad-output/implementation-artifacts/8-111-fixcity-wizard-theme-component-architecture.md`
- **Filament Wizard**: https://github.com/filamentphp/filament/blob/5.x/packages/schemas/src/Components/Wizard.php

---

**Golden Rule**: Module defines WHAT steps exist. Theme defines HOW they look.
