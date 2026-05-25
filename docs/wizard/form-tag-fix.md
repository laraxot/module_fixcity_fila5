# Form Tag Fix for Create Ticket Wizard

**Issue**: Missing `<form wire:submit="submit">` wrapper in wizard view

**Root Cause**: The `create-ticket-wizard.blade.php` view was missing the required form wrapper that enables Livewire form submission functionality.

**Impact**:
- Forms in wizard steps were not submitting properly
- Wizard step navigation may have been affected
- Form validation and submission logic was not working as expected

**Solution**:
Fixed the view by adding the required form wrapper:

```blade
<form wire:submit="submit">
    {{ $this->form }}
</form>
```

**Correct Implementation Pattern** (allineato a `vendor/filament/forms/stubs/LivewireFormView.stub`):

```blade
<x-filament-widgets::widget>
    <form wire:submit="{{ $this->getFormSubmitAction() }}">
        {{ $this->form }}
    </form>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
```

`getFormSubmitAction()` è definito su `XotBaseWizardWidget` (default `submit`; `save()` resta alias Filament).

**Key Rules**:
1. **Must have**: `<form wire:submit="{{ $this->getFormSubmitAction() }}">` (equivalente stub `submitAction`)
2. **Must render**: `{{ $this->form }}` — mai `getWizardComponent()` (inesistente)
3. **Must include**: `<x-filament-actions::modals />` **fuori** dal tag `<form>` (dialog Filament ≠ input del form)
4. **Must preserve**: Widget structure and Design Comuni styling
5. **Cannot have**: Custom form submission logic in the view

**Related Files**:
- `Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php` - Fixed
- `vendor/filament/forms/stubs/LivewireFormView.stub` - Vendor template
- `app/Filament/Widgets/CreateTicketWizardWidget.php` - Widget logic (unchanged)

**Verification**:
- [x] Form submits properly via Livewire
- [x] Wizard step navigation works
- [x] Design Comuni styling preserved
- [x] PHPStan validation passes

**Date**: 2026-05-14  
**Status**: Resolved  
**Module**: Fixcity  
**Component**: Ticket Wizard