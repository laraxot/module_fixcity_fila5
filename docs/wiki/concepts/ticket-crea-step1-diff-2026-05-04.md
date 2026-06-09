---
title: Segnalazione-Crea Step 1 — Diff Visivo con Design Comuni (2026-05-04)
type: concept
created: 2026-05-04
updated: 2026-05-04
tags: [visual-parity, segnalazione-crea, wizard, stepper, tailwind, design-comuni]
status: active
sources:
  - https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html
  - http://127.0.0.1:8000/it/tests/segnalazione-crea
related:
  - ff-wizard-visual-parity
  - wizard-visual-parity
  - visual-parity-verification-rule
---

# Segnalazione-Crea Step 1 — Diff Visivo con Design Comuni

> Analisi screenshot 2026-05-04. Story di fix: [[7-77-segnalazione-crea-step1-visual-html-parity]]

## Differenze Identificate

### DIFF-01: Stepper orizzontale
- **Ref:** 3 tab a tutta larghezza, bordo bottom verde `3px` sull'attivo, separatori verticali
- **Locale:** `.steppers` verticale, stile non corrispondente
- **Fix:** CSS flex su `.steppers-header ul`, `border-bottom: 3px solid #007a52` su `.active`

### DIFF-02: Bottone "Avanti"
- **Ref:** Verde `#007a52`, ~450px desktop, full-width mobile, label "Avanti"
- **Locale:** Giallo Filament `oklch(0.828...)`, 124px, label "Successivo"
- **Fix:** CSS override `.fi-ac-btn-action` nel contesto `.segnalazione-wizard-root`

### DIFF-03: Checkbox label
- **Ref:** "Ho letto e compreso l'informativa sulla privacy"
- **Locale:** "Accetto la privacy"
- **Fix:** Aggiornare `fixcity::segnalazione.privacy.checkbox.label`

### DIFF-04: Bottoni "Invia" spurii
- **Ref:** Solo "Avanti" sullo step 1
- **Locale:** "Successivo" + 2x "Invia" (Filament renderizza tutti)
- **Fix:** `display: none` per `.fi-btn[wire:click*="submit"]` su step 1

## Regole Architetturali

- NO CSS inline nelle Blade
- NO Bootstrap Italia runtime
- CSS solo in `laravel/Themes/Sixteen/resources/css/`
- Dopo ogni modifica: `cd laravel/Themes/Sixteen && npm run build && npm run copy`

## Stato Header (OK)
- Slim header: `rgb(0, 64, 43)` ✅
- Navbar: `rgb(0, 122, 82)` ✅
- Active nav item: presente ✅
