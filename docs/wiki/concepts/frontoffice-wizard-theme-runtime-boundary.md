---
title: "Frontoffice Wizard Theme Runtime Boundary"
type: concept
confidence: high
created: 2026-05-26
updated: 2026-05-26
tags: [fixcity, sixteen, wizard, vite, tailwind, alpine, lit]
related:
  - ../../../../../Themes/Sixteen/docs/wiki/concepts/no-bootstrap-runtime-assets-rule.md
  - ./frontoffice-ticket-priority-default-rule.md
  - ../../../../../../docs/ux-design-fixcity.md
---

# Frontoffice Wizard Theme Runtime Boundary

Il wizard pubblico Fixcity usa il tema Sixteen come runtime visuale, ma non deve introdurre asset o workaround fuori dalla pipeline del tema.

## Regola

- I campi e lo stato del ticket vivono nel modulo Fixcity (`TicketForm`, widget e modello).
- Il comportamento JS/CSS di header, wizard e mappa pubblica vive nel tema Sixteen e passa da Vite.
- Non aggiungere `<script>`, CDN Bootstrap, file boot manuali o CSS page-specifici nei Blade del modulo per correggere il tema.
- La mappa pubblica usa `CoordinatePicker`/`coordinate-picker-lit`, non shim `geoMapPickerField`.

## Impatto su `segnalazione-crea`

La URL `/it/tests/segnalazione-crea?step=form.data%3A%3Adata%3A%3Awizard-step` deve essere verificata guardando HTML reale e manifest pubblicato, ma la correzione deve restare nello schema owner o nel bundle Sixteen, non in asset manuali.

## Collegamento specifiche UX (source of truth)

Le regole di stack e governance UX (no bootstrap runtime, form = filament, boundary module/theme, quality gates) sono specificate nel documento canonico:

- [`docs/ux-design-fixcity.md`](../../../../../../docs/ux-design-fixcity.md)
