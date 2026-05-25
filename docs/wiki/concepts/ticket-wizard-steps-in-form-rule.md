---
title: "Ticket Wizard Steps in TicketForm Rule"
type: concept
confidence: high
created: 2026-05-12
updated: 2026-05-23
tags: [filament, wizard, ticket, ticketform, haswizard, step]
related:
  - concepts/filament5-schema-form-access-rule.md
  - concepts/filament5-widget-schema-submit-state-rule.md
---

# Regola: Gli step del wizard Ticket stanno in TicketForm

## Status: ✅ VERIFICATO (2026-05-12 · correzione naming widget 2026-05-23)

## Regola

- **SSoT degli step Filament**: metodo **`TicketForm::getSteps()`** (statico nello schema resource) — stesso contenuto riusabile dal widget e dalla pagina pannello.
- **Contratto sulla base wizard**: solo **`getSteps()`** — stesso nome del trait **`HasWizard`** Filament. **Bandito:** qualsiasi alias storico con altro nome.

Il widget **`CreateTicketWizardWidget`** **delega** e non deve ridefinire a mano gli `Step::make()`:

```php
// ✅ CORRETTO
class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    /** @return array<string, Step> */
    public function getSteps(): array
    {
        return TicketForm::getSteps();
    }
}
```

```php
// ❌ VIETATO — inline nel widget dominio Step::make
public function getSteps(): array // ok come nome ma body vietato inline
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
| Metodo pubblico wizard col nome sbagliato | Copia incolla da snippet vecchi | Solo **`getSteps()`** sulla base (**widget** + `*ResourceForm`) |

## File coinvolti

- `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`
- `Modules/Xot/app/Filament/Widgets/XotBaseWizardWidget.php` (wizard + policy `step` in URL; nessun helper di normalizzazione stato sulla base dopo `getState()`)
- `Modules/Xot/app/Filament/Traits/DelegatesFilamentWizardSchemaMethods.php`

## Riferimenti

- [[concepts/filament5-widget-schema-submit-state-rule.md]]
- `Modules/Xot/docs/wiki/filament-wizard-refactoring.md` (SSoT; alias stub: `…/XotBaseWizardWidget-HasWizard-refactor.md`)
