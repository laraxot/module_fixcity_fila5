---
name: segnalazione-crea-map-fullscreen-contract
description: Fixcity expectation for fullscreen map behavior in the public ticket wizard
type: concept
---

# Segnalazione Crea Map Fullscreen Contract

In `/it/tests/segnalazione-crea?step=form.dati-della-segnalazione::data::wizard-step`, when the map fullscreen control is used:

- the map must occupy the viewport;
- the page must not keep a vertical scrollbar;
- `Informazioni richieste` and other wizard/sidebar blocks must not appear over the map;
- Leaflet tiles must be visible after the transition.

Fixcity owns the wizard flow and markup. Geo owns the `coordinate-picker-lit` runtime. Sixteen owns visual/layout CSS for the public theme.
