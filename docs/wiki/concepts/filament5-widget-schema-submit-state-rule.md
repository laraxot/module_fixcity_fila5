# Filament 5 Widget Schema Submit State Rule

## Regola

Nei widget Laraxot che estendono `XotBaseWidget` / `XotBaseWizardWidget` lo stato del form si legge dal property schema esposto dalla base:

```php
$state = $this->form->getState();
```

Non usare:

```php
$this->getForm('form')
```

`XotBaseWidget` usa `Filament\Schemas\Concerns\InteractsWithSchemas` e dichiara `@property Schema $form`; non usa `InteractsWithForms` come contratto primario. Per questo `getForm()` non e' un metodo disponibile sul widget e genera `BadMethodCallException`.

## Errore osservato

URL di partenza:

`/it/tests/segnalazione-crea?step=form.riepilogo::data::wizard-step`

Evento:

click sul submit custom `wire:click="submit"` nello step riepilogo.

Fatal:

```text
Method Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget::getForm does not exist.
```

Root cause:

- `CreateTicketWizardWidget::validateWizardSubmission()` chiamava `$this->getForm('form')->validate()`.
- `CreateTicketWizardWidget::prepareTicketData()` chiamava `$this->getForm('form')->getState()`.
- Il widget eredita il contratto schema da `XotBaseWidget`, quindi il form e' `$this->form`.

## Pattern corretto

```php
protected function validateWizardSubmission(): void
{
    $this->form->getState();
}

protected function prepareTicketData(): array
{
    /** @var array<string, mixed> $state */
    $state = $this->form->getState();

    // ...
}
```

`getState()` e' il punto di accesso unico allo stato corrente validato dello schema. Mantiene DRY + KISS: niente wrapper legacy, niente doppio contratto Forms/Schemas.

## Implicazioni per il wizard segnalazione

- Il submit Design Comuni puo' restare un bottone Blade custom solo se chiama metodi Livewire che rispettano il contratto schema.
- Il submit Filament centralizzato resta in `XotBaseWizardWidget::getWizardSubmitAction()`.
- Se la view tema/modulo aggiunge bottoni extra, non deve introdurre API diverse da quelle della base.

## Checklist

- [ ] Nei widget schema-based cercare `getForm('form')` e sostituire con `$this->form`.
- [ ] Validare con `php -l` sul widget modificato.
- [ ] Verificare POST Livewire dopo click submit, non solo GET della pagina.
- [ ] Documentare eventuali bottoni custom come bridge visuali, non come nuovo owner del form.

