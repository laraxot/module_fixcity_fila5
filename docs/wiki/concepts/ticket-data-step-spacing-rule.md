---
title: Segnalazione Data Step Spacing Rule
type: concept
tags: [fixcity, segnalazione, wizard, design-comuni, css]
created: 2026-04-22
updated: 2026-04-22
sources:
  - ../../../../Themes/Sixteen/docs/wiki/concepts/coordinate-picker-design-comuni-parity-rule.md
---

# Segnalazione Data Step Spacing Rule

## Regola Permanente

Nel wizard `segnalazione-crea`, lo spazio verticale fra il titolo sezione `Disservizio` e il primo campo `Tipo di disservizio` deve restare compatto e aderente alla pagina statica Design Comuni.

## Boundary

- Fixcity genera sezioni semantiche con `data-step-section="inefficiency"`.
- Sixteen applica padding, gap e margin nel CSS tema.
- Non aggiungere `<style>` o inline style nei Blade del modulo.

## Implementazione

Il selettore owner e':

```css
.ticket-wizard-root .fi-section[data-step-section="inefficiency"]
```

Le regole devono ridurre solo header margin, padding superiore del content container e gap interno della sezione `inefficiency`, senza comprimere le sezioni `place`, `author` e `summary`.
