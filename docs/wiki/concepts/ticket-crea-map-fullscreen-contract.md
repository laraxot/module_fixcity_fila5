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

## Story 8-74 refinement

The requested improvement is tracked as:

- `_bmad-output/implementation-artifacts/8-74-segnalazione-crea-map-fullscreen-refinement.md`
- `.planning/stories/8-74-segnalazione-crea-map-fullscreen-refinement.story.md`
- `laravel/Modules/Fixcity/docs/stories/segnalazione-crea-map-fullscreen-refinement.md`

Fixcity verification requirements:

- verify the exact URL with the encoded `step=` query string;
- capture browser screenshots before fullscreen, during fullscreen, and after exit;
- check both desktop and mobile widths;
- confirm `location` state still updates after click/drag/geolocation;
- do not implement fullscreen logic in the Fixcity widget unless the issue is proven to be a wizard wrapper integration problem.

Anti-patterns:

- hiding the side information box as the primary fix;
- adding selectors tied to `tests.segnalazione-crea`;
- changing ticket `location` persistence while working on fullscreen.
