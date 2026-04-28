---
name: wizard-riepilogo-parity-audit
description: Audit visual parity step riepilogo (step 3) vs Design Comuni segnalazione-03-riepilogo.html
type: concept
---

# Wizard Riepilogo — Visual Parity Audit

Reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html

## Fix critico già applicato [2026-04-22]

**Problema**: tasto "Invia" non appariva nello step riepilogo.

**Root cause**: `$isSummaryStep` nel Blade era calcolato da `request()->query('step')` — statico al render iniziale. Quando Livewire naviga tra step senza reload, la variabile restava stale (sempre step 1).

**Fix**:
- `XotBaseWizardWidget::nextStep()` ora incrementa `$wizardStartStep`
- `XotBaseWizardWidget::previousStep()` ora decrementa `$wizardStartStep`
- Blade usa `$this->wizardStartStep` (property Livewire reattiva) invece della URL

**Regola**: MAI usare `request()->query()` nel Blade Livewire per stato che cambia durante la navigazione. Usare sempre property Livewire pubbliche.

## Gap parity vs Design Comuni

### 1. Breadcrumbs [MANCANTE]
Reference: `cmp-breadcrumbs` con Home → Servizi → Segnalazione disservizio  
Nostro: assente nello step riepilogo  
**Piano**: aggiungere `<div class="cmp-breadcrumbs">` nel Blade sopra lo stepper header

### 2. Modal "Termini e condizioni" prima dell'invio [MANCANTE]
Reference: tasto "Invia" apre modal con "Termini e condizioni" → "Conferma e invia"  
Nostro: submit diretto senza conferma  
**Piano**: aggiungere modal Bootstrap Italia con `data-bs-toggle="modal"` sul tasto Invia; conferma chiama `wire:click="submit"`

### 3. Layout nav buttons step riepilogo [PARZIALE]
Reference: `cmp-nav-steps` row con:
- `steppers-btn-prev` = testo link (non outline button)
- `steppers-btn-save` = outline button "Salva bozza" (d-none d-lg-block + d-block d-lg-none per mobile)
- `steppers-btn-confirm send` = primary "Invia" (apre modal)

Nostro: colonna con "Indietro" (outline) + "Salva bozza" + "Invia" — struttura simile ma diversa responsività

### 4. Card structure summary sections [PARZIALE]
Reference: ogni sezione riepilogo in card con `card-header border-bottom border-light` e link "Modifica"  
Nostro: Filament Section (non ha link modifica integrati)  
**Piano**: aggiungere link "Modifica" che chiama `wire:click="goToStep('...')"` per tornare allo step corrispondente

### 5. Header — slim + navbar [OK]
Reference e nostro: `it-header-slim-wrapper` + `it-header-center-wrapper` + navbar — struttura presente

## Piano implementazione (priorità)

| # | Item | Urgenza | File | Stato |
|---|------|---------|------|-------|
| 1 | ~~Submit button visibile~~ | ~~CRITICO~~ | ~~XotBaseWizardWidget + Blade~~ | ✅ DONE |
| 2 | ~~Modal conferma prima submit~~ | ~~ALTA~~ | ~~ticket-create-wizard.blade.php~~ | ✅ DONE |
| 3 | ~~Breadcrumbs cmp-breadcrumbs~~ | ~~MEDIA~~ | ~~ticket-create-wizard.blade.php~~ | ✅ DONE |
| 4 | Link "Modifica" sezioni riepilogo | MEDIA | getSummarySchema() + Section::headerActions? | ⏳ DA FARE |
| 5 | Nav buttons layout parity | BASSA | ticket-create-wizard.blade.php + app.css | ⏳ DA FARE |

### Note implementazione "Modifica" [#4]

`Section` da `Filament\Schemas\Components\Section` — verificare se supporta `->headerActions([])`.
Se sì: `Action::make('modifica_luogo')->label('Modifica')->action(fn() => $this->previousStep())`.
Se no: alternativa è `Text::make()->html()` con `<a wire:click="goToStep(2)">Modifica</a>` dentro la section.
Da verificare API Filament 5.x prima di implementare.
