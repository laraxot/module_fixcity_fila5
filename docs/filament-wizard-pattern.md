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

### Esempio Step Riepilogo (Zen)

```php
private function makeStepSummary(): Step
{
    return Step::make('3')
        ->schema([
            Section::make('Riepilogo')
                ->schema([
                    TextEntry::make('review_title')
                        ->state(fn (Get $get) => $get('title')),
                    // ...
                ]),
        ]);
}
```

---

## 📚 Altri Riferimenti

- [Fixcity Documentation Index](./INDEX.md)
- [Geolocation AddressInput Component](../../Geo/docs/address-input-component.md)
- [Design Comuni - Ticket Creation Wizard](../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
