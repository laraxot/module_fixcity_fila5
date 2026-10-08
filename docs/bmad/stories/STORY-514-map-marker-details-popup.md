---
id: STORY-514
title: "Public marker popup exposes visible report details"
status: done
module: Fixcity
created: 2026-09-27
---

# Story

As a guest citizen, I want to click a map marker and immediately understand the report, then open its details, so that the map is useful without authentication.

## Acceptance criteria

- [x] Marker popup shows title, status, type and location without clipping.
- [x] Primary action is translated as “Dettagli” / “Details”.
- [x] Primary action opens the public Folio detail experience and is keyboard accessible.
- [x] English and Italian retain their locale in generated URLs and labels.
- [x] Primary action is visually prominent (44px mobile, 48px desktop) with a visible focus ring.
- [x] Chromium verifies Italian and English popups at 320px, 768px and 1440px; CTA is visible and its destination returns HTTP 200.
- [x] Wiki quality gate passes; PHPStan was not part of this UI-only change.

## Implementation evidence

- Popup markup and active CSS are in `Modules/Geo/resources/js/components/map/popup-ticket.js`; the popup CTA opens Folio `/tickets/{id}` in the active locale.
- The public listing is `/tickets`; `/segnalazioni` redirects to that route while preserving the locale.
- Browser evidence: `/tmp/fixcity-ui-audit/final-{it,en}-{320,768,1440}.png`; all six runs reported no JavaScript errors or horizontal overflow.
- Expected state, observed gap, and correction plan are recorded in the adjacent `ui-ux-map-marker-popup-*-2026-09-27.md` documents.

## Traceability

- Issue: https://github.com/laraxot/base_fixcity_fila5/issues/514
- Discussion: https://github.com/laraxot/base_fixcity_fila5/discussions/514
