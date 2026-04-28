# Filament Multiple Forms

## Overview
Filament 5 introduces the ability to manage **multiple independent forms** within a single Livewire component. Each form has its own state, validation rules, and submission handling, identified by a unique form name.

## Key Concepts
- **Form IDs** – Pass a string to `Form::make('myForm')` (or `->name('myForm')`) to separate the data payload.
- **State Isolation** – Livewire stores data per‑form, preventing field name collisions.
- **Validation** – Call `$this->getForm('myForm')->validate()` to run validation only on that form.
- **Submission** – Use `$this->getForm('myForm')->submit()` or invoke a custom action that works on the specific form’s state.
- **Rendering** – In Blade, reference the form with `{{ $this->form('myForm') }}` or `$this->form('myForm')` inside a Filament widget.

## Typical Usage Pattern
```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Form;

public function getForms(): array
{
    return [
        Form::make('contact')
            ->schema([
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('message')
                    ->required(),
            ]),
        Form::make('subscribe')
            ->schema([
                TextInput::make('newsletter_email')
                    ->email()
                    ->required(),
            ]),
    ];
}

public function submitContact(): void
{
    $this->getForm('contact')->validate();
    // handle contact data
}

public function submitSubscribe(): void
{
    $this->getForm('subscribe')->validate();
    // handle subscription
}
```

## Integration with Wizards
When a wizard step needs its own isolated form (e.g., a **data** step and a **summary** step), declare each step’s schema inside a separate `Form::make('stepX')`. The wizard’s navigation can then call `$this->getForm('stepX')->validate()` before advancing.

## Resources
- Official docs: https://filamentphp.com/docs/5.x/components/form#using-multiple-forms
- Example implementation in Fixcity: see `CreateTicketWizardWidget` where each wizard step is a distinct component; you can convert steps to independent forms if needed.
