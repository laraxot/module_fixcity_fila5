# Wizard Visual Parity – Segnalazione Wizard

## Overview
The **Segnalazione** wizard (`/it/tests/segnalazione-crea`) should visually match the Design Comuni reference page *segnalazione‑03‑riepilogo*.

### Missing elements
- **Submit button** – the wizard step `form.riepilogo` lacks the required `Submit` control that finalises the wizard.
- **Header alignment** – the header does not contain the correct slim‑bar background colour and spacing as defined in the Design Comuni mockup.
- **Map visibility** – the map component (`coordinate‑picker‑lit`) is hidden on step transition due to CSS selectors scoped to `.segnalazione‑wizard‑root`.

### Required fixes
1. Add a generic **Submit** button component (`<x-button type="submit" ...>Invia segnalazione</x-button>`) to the wizard form.
2. Apply the global header styles defined in `header-footer-colors.css` – no page‑specific selectors.
3. Replace page‑specific `.segnalazione‑wizard‑root coordinate‑picker‑lit` rules with a component‑level rule in `filament‑wizard‑parity.css` (see implementation plan).

## Documentation updates
- Update this module‑level wiki file.
- Ensure the theme‑level wiki mirrors the same content.
- Add entries to the global `docs/wiki/index.md` and `docs/wiki/log.md`.

## Why this rule matters
Design Comuni enforces **component‑first CSS**; page‑specific selectors break DRY/KISS, cause regressions across locales and hinder reuse. Keeping parity ensures accessibility, visual consistency, and simplifies future theme changes.
