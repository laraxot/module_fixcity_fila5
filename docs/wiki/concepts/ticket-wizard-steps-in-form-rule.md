---
title: "Ticket Wizard Steps in TicketForm Rule"
type: concept
confidence: high
created: 2026-05-12
updated: 2026-05-12
tags: [filament, wizard, ticket, ticketform, haswizard, step]
related:
  - concepts/filament5-schema-form-access-rule.md
  - concepts/filament5-widget-schema-submit-state-rule.md
---

# Regola: Gli step del wizard Ticket stanno in TicketForm — metodo getSteps()

## Status: ✅ VERIFICATO (2026-05-12)

## Regola

Il metodo SI CHIAMA `getSteps()` — allineato allo standard `HasWizard::getSteps()` di Filament.
**`getSteps()` è ABOLITO** — era il vecchio nome non standard.

Gli step del wizard `CreateTicketWizardWidget` DEVONO essere definiti in:

```
Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm::getSteps()
```

Il widget **delega** — non ridefinisce:

```php
// ✅ CORRETTO
class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    public function getSteps(): array
    {
        return TicketForm::getSteps();
    }
}
```

```php
// ❌ VIETATO — inline nel widget, e nome sbagliato
public function getSteps(): array  // NOME SBAGLIATO!
{
    return [
        Step::make('privacy')->schema([...]),
        Step::make('dati')->schema([...]),
    ];
}
```

## Struttura step corretta in TicketForm

```php
class TicketForm extends XotBaseResourceForm
{
    // Step 1: Privacy
    // Step 2: Dati segnalazione (usa getFormSchema() in auto-spread)
    public static function getSteps(): array
    {
        return [
            Step::make('step-1')
                ->label(__('fixcity::fixcity.ticket.steps.auth.label'))
                ->icon('heroicon-o-shield-check')
                ->schema([
                    RichEditor::make('privacy_notice')->disabled(),
                    Checkbox::make('accept_terms')->required()->rules(['accepted']),
                ]),
            Step::make('step-2')
                ->label(__('fixcity::fixcity.ticket.steps.data.label'))
                ->icon('heroicon-o-document-text')
                ->schema([
                    ...self::getFormSchema(),
                ]),
        ];
    }
}
```

## Perché questa regola

| Motivo | Dettaglio |
|--------|-----------|
| **DRY** | Un solo posto per i campi del ticket |
| **Testabilità** | `TicketForm::getSteps()` testabile senza widget |
| **Riuso** | Stesso form usato da Resource admin e wizard frontoffice |
| **Separazione** | Widget = orchestrazione, Form = definizione campi |

## Errori comuni

| Errore | Causa | Fix |
|--------|-------|-----|
| `getSteps() does not exist` | Form non ha il metodo | Aggiungere a TicketForm |
| `Cannot redeclare getSteps()` | Metodo definito due volte | Rimuovere duplicato |
| `getSubmitFormAction() does not exist` | HasWizard usato senza override | Override getWizardComponent() in XotBaseWizardWidget |

## File coinvolti

- `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`
- `Modules/Xot/app/Filament/Widgets/XotBaseWizardWidget.php`

## Riferimenti

- [[concepts/filament5-widget-schema-submit-state-rule.md]]
- `Modules/Xot/docs/wiki/XotBaseWizardWidget-HasWizard-refactor.md`
