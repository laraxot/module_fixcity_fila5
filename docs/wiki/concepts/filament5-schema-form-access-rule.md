# Filament 5 Schema Form Access Rule

## Regola Fixcity

`CreateTicketWizardWidget` non deve chiamare `getForm('form')`.

Il widget eredita da `XotBaseWizardWidget` / `XotBaseWidget`, che usano Filament Schemas:

```php
$this->form->getState();
```

## Errore evitato

```text
BadMethodCallException: Method Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget::getForm does not exist.
```

## Pattern

- Render Blade: `{{ $this->form }}`
- Validazione/lettura stato: `$this->form->getState()`
- Normalizzazione wizard: `normalizeWizardFormState($this->form->getState())`

## Caso submit riepilogo

Nel riepilogo segnalazione il bottone `wire:click="submit"` chiama `CreateTicketWizardWidget::submit()`.

Regola:

- il bottone puo' essere markup Design Comuni;
- il metodo Livewire deve leggere stato e validation dal schema `$this->form`;
- non usare `getForm('form')`, perche' appartiene a un contratto Forms non attivo in `XotBaseWidget`.

Questo evita il fatal sul POST Livewire:

```text
Method Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget::getForm does not exist.
```
