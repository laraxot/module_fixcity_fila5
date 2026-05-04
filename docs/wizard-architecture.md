# Wizard Architecture

The Wizard system in Fixcity follows the modular pattern established in Laraxot, utilizing Filament v5 native components while maintaining Design Comuni visual parity.

## Core Components

- **Widget**: `Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget`
- **Base Class**: `Modules\Xot\Filament\Widgets\XotBaseWizardWidget`
- **Form Schema**: `Modules\Fixcity\Forms\TicketForm`
- **Theme Template**: `Themes\Sixteen\resources\views\components\wizard.blade.php`

## Philosophy

1. **Native over Custom**: We use Filament's `HasWizard` trait to handle the multi-step lifecycle, state management, and validation.
2. **Modular Schema**: Steps and fields are defined in the `TicketForm` class within the module, allowing for reuse across different widgets or resources.
3. **Visual Parity**: The theme-level `wizard.blade.php` component acts as a wrapper around Filament's wizard logic, injecting Design Comuni (Italia.it) styles and stepper behavior.
4. **State Management**: All wizard state is stored in a public `$data` array on the widget, synced via Livewire.

## View Management
  
Following the Laraxot standardization rules:
- **Automatic Calculation**: `CreateTicketWizardWidget` does **not** hardcode the `$view` property. It is automatically resolved to `fixcity::filament.widgets.create-ticket-wizard`.
- **Naming Convention**: Blade templates must match the kebab-case version of the widget class name.
- **Documentation**: The calculated view name must be documented in the class docblock using the `@view` annotation.

## State Pathing

The wizard is bound to the `data` state path. This ensures that all fields defined in the schema (e.g., `name`, `type_id`, `content`) are automatically mapped to `$this->data['name']`, etc.

## Navigation

Navigation is handled by the `Wizard` component's `nextStep()` and `previousStep()` methods. Custom actions are used to override labels and behaviors while remaining within the Filament action lifecycle.
