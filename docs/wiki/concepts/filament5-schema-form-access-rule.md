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
- Il submit legge **`$this->form->getState()`** così come esposto dallo schema; eventuali trasformazioni strutturali vanno pianificate sullo **schema** (state path, component mapping), non con helper PHP post‑`getState()` nel widget, salvo decisione documentata di dominio.

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
