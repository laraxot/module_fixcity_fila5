---
title: "BMAD — Map marker popup gap analysis"
type: bmad-ui-gap
module: Fixcity
status: closed
created: 2026-09-27
---

# Observed gap and verified correction

The active map uses `Modules/Geo` and receives public GeoJSON from FixCity. The popup had a rich card and an async detail request, but the guest flow was not release-ready:

- the primary copy was “Scheda completa”, while the requested action is “Dettagli”;
- the primary action needed a larger, clearer hit target and visible keyboard focus;
- the public GeoJSON initially contained a hard-coded `/it` URL, risking the wrong language on English pages;
- an active Folio detail page exists at `/tickets/{ticket}` and is named `tickets.detail`; the API detail endpoint supplies the popup preview.

## Corrections

- Italian and English primary labels now read “Dettagli” and “Details”.
- The primary CTA is full-width, bold, at least 48px high, high contrast, and has an explicit focus ring.
- GeoJSON links are locale-aware and target the Folio ticket detail page.
- The popup keeps a separate secondary close action.

## Evidence

Static audit of `Modules/Geo/resources/js/components/map/popup-ticket.js`, popup styles, `map-lit.js`, `BuildTicketsGeoJsonAction`, and the Sixteen Folio page `tickets/[ticket].blade.php`. Browser verification follows the code change and is recorded in STORY-514.
