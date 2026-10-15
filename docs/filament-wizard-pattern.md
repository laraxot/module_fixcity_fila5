---
title: "filament wizard pattern"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-10-13
qmd: "filament wizard pattern"
issues: []
discussions: []
status: draft
---

# Filament Schema Wizard Pattern

## Overview

Laraxot usa **Filament Schema Wizard** (v5) per tutti i wizard multi-step. Il pattern segue il principio **Single Responsibility**: widget wizard estendono `XotBaseWizardWidget`, non `XotBaseWidget`.

---
## 🏛 Filosofia e Governance

Per comprendere il "Perché", la "Religione" e lo "Zen" dietro questa scelta, consulta i documenti canonici:

1. **[Wizard Governance Philosophy](./wizard-governance-philosophy.md)**: Confini modulo-base-tema, anti-duplicazione e gestione errori.
2. **[Infolists for Summary](../../Xot/docs/filament/widgets/infolists-for-summary.md)**: Perché lo step finale DEVE usare Infolist components invece di Blade partials.
3. **[XotBaseWizardWidget Philosophy](../../Xot/docs/filament/widgets/xot-base-wizard-widget-philosophy.md)**: La logica cross-cutting della base class.

---

## 🔴 Regole Mandatorie

1. **Extend XotBaseWizardWidget**: Garantisce gestione automatica di `?step=`, normalizzazione stato e azioni consistenti.
2. **No explicit `->label()`**: Tutte le traduzioni devono venire dai file lang via `AutoLabelAction`.
3. **Infolist for Summary**: Lo step di riepilogo non deve avere input, ma solo visualizzazione dati via `TextEntry`, `ImageEntry`, ecc.
4. **Logic in Module, Dress in Theme**: La logica del wizard (campi, submit) sta nel modulo. Il tema Sixteen applica lo stile Design Comuni via CSS scoped.

---

## 🏗 Implementazione di Riferimento

Il widget principale di questo modulo è:
**[CreateTicketWizardWidget Documentation](./CreateTicketWizardWidget.md)**

### Esempio Step Riepilogo (estratto reale, verificato 2026-10-13)

Il vecchio esempio con `makeStepSummary()` non corrispondeva a nessun metodo (nessuna `function makeStepSummary` in `Modules/Fixcity/app` e `Modules/Xot/app`). Il passo reale e' in `TicketForm` (`Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`):

```php
// TicketForm::getSteps(): la riga del riepilogo
'summary' => Step::make('summary')
    ->schema(array_values(static::getReviewStepSchema()))
    ->label((string) __('fixcity::fixcity.ticket.steps.summary.label')),

// TicketForm::getReviewStepSchema() (DCP-33): un solo componente View alimentato dallo stato del form
return [
    'reviewSummary' => View::make('pub_theme::components.wizard.summary-step')
        ->viewData(static fn (object $livewire): array => [
            'review' => app(BuildTicketReviewSummaryAction::class)->execute(
                (array) data_get($livewire, 'data', []),
                Filament::auth()->user(),
            ),
            // + 'data' (blockData), 'withContainer', 'withShell', 'wired'
        ])
        ->columnSpanFull(),
];
```

Lo schema piatto con `Section` e `TextEntry` (`TicketForm::getSummarySchema()`, via `TicketFormReviewInfolist::summarySectionEntries()`) resta per il back office (`getFormSchema()`).

---

## 📚 Altri Riferimenti

- [Fixcity Documentation Index](./INDEX.md)
- [Geolocation AddressInput Component](../../Geo/docs/address-input-component.md)
- [Design Comuni - Ticket Creation Wizard](../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)

--- BMAD + Second Brain ---
- Scopo: documentazione tecnica del modulo.
- Utilità: riferimento sviluppatori/audit.
- Funzionalità: struttura, dipendenze, convenzioni (no date .md, git avanti).
