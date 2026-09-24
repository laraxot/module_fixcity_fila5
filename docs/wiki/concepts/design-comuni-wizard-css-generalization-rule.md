---
title: Design Comuni Wizard CSS Generalization Rule
type: concept
tags: [fixcity, wizard, design-comuni, css, theme]
created: 2026-04-22
updated: 2026-04-22
sources:
  - ../../../../Themes/Sixteen/docs/wiki/concepts/design-comuni-site-wide-component-css-rule.md
---

# Design Comuni Wizard CSS Generalization Rule

## Regola

Il wizard ticket non deve comportarsi diversamente dagli altri wizard solo perche e' il wizard ticket.

Fixcity puo fornire dati, step, schema e attributi semantici, ma la resa visuale deve essere una regola generale del tema per wizard Filament, sezioni schema, componenti Geo e header Design Comuni.

## Anti-pattern

Non introdurre regole tema basate su:

- `tests.segnalazione-crea`;
- `.ticket-wizard-root`;
- classi route-specific per casi ordinari.

## Pattern

Se serve uno scoping, usare classi di componente o attributi semantici riusabili:

```css
.fi-section[data-step-section="inefficiency"] { ... }
coordinate-picker-lit { ... }
.fi-sc-wizard { ... }
```

Questo mantiene DRY + KISS e permette ad altri wizard di ereditare lo stesso comportamento.
