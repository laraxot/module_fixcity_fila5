---
title: "Segnalazione step 1 privacy — delta Design Comuni vs wizard locale"
type: comparison
sources:
  - "https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html"
confidence: high
created: 2026-05-04
updated: 2026-05-04
tags: [fixcity, wizard, privacy, design-comuni]
related:
  - "../overviews/fixcity-module.md"
  - "../../ticket-wizard-frontoffice.md"
  - "../../../../../Themes/Sixteen/docs/wiki/comparisons/segnalazione-01-privacy-design-comuni-vs-local-wizard.md"
  - "../../../../../_bmad-output/implementation-artifacts/7-103-segnalazione-01-privacy-tailwind-lit-html-audit-correction-plan.md"
---

# Delta privacy step 1 — Fixcity

## Scopo

Documentare il confronto tra il **modello statico** Design Comuni ([segnalazione-01-privacy](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html)) e la pagina **`/it/tests/segnalazione-crea`** (wizard Filament), con piano di allineamento **senza** Bootstrap Italia come stack finale (Tailwind + Alpine + Lit nel **tema Sixteen**).

## Dove sta la logica

- Schema privacy: `TicketForm::getFrontofficePrivacySchema(string $privacyLink)`
- Widget: `CreateTicketWizardWidget` (estende `XotBaseWizardWidget`)
- Vista wrapper (markup stepper + CTA): `resources/views/filament/widgets/ticket-create-wizard.blade.php` nel modulo Fixcity — **presentazione** da spostare gradualmente verso pattern tema (vedi wiki Sixteen).

## Documentazione dettagliata

**Non duplicare** la tabella delta completa qui: mantenere una sola fonte estesa in:

- [Sixteen — confronto completo](../../../../../Themes/Sixteen/docs/wiki/comparisons/segnalazione-01-privacy-design-comuni-vs-local-wizard.md)

## Story BMAD

- [7-103 — audit e piano correzione](../../../../../_bmad-output/implementation-artifacts/7-103-segnalazione-01-privacy-tailwind-lit-html-audit-correction-plan.md)
- Correlata: [7-39](../../../../../_bmad-output/implementation-artifacts/7-39-segnalazione-01-privacy-design-comuni-filament-visual-parity.md)

## Delta critici modulo (sintesi operativa)

### 1. `create-ticket.blade.php` — inline `<style>` (CRITICO)
- Violazione regola `no-inline-blade-style-rule` e `feedback_no_page_specific_css`
- ~80 righe CSS inline nel blade legacy
- **Azione**: rimuovere `<style>` e portare tutto in `Themes/Sixteen/resources/css/segnalazione-wizard.css`

### 2. Etichette stepper — allineamento a reference
- Reference: "Autorizzazioni e condizioni" | "Dati di segnalazione" | "Riepilogo"
- Verificare chiavi in `lang/it/segnalazione.php` (`steps.privacy`, `steps.data`, `steps.summary`)

### 3. Larghezza colonna corpo form
- Reference usa `col-12 col-lg-8` per il body form (più stretto del titolo `col-lg-10`)
- Locale usa `col-12 col-lg-10 offset-lg-1` → visivamente più largo
- **Azione (tema)**: passare a Tailwind `w-full lg:w-2/3` per il contenuto form step 1

### 4. Sezione "Contatta il comune" — assente
- Non è responsabilità del widget (il widget gestisce solo il form)
- **Azione**: aggiungere CMS block nel JSON `tests.segnalazione-crea.json` dopo il wizard block

### 5. Checkbox accessibility
- Reference: `<input id="privacy">` + `<label for="privacy">`
- Verificare che Filament `Checkbox::make('privacyAccepted')` generi `id`/`for` corretti

## Workflow qualità

Dopo modifiche al tema: `cd laravel/Themes/Sixteen && npm run build && npm run copy`.
