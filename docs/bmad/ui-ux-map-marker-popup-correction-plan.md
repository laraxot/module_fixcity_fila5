---
title: "BMAD — Map marker popup correction plan"
type: bmad-ui-correction-plan
module: Fixcity
status: implemented
created: 2026-09-27
---

# Correction plan

1. Keep the marker popup presentation in the Geo module, its UI owner.
2. Label the primary action “Dettagli” / “Details”.
3. Make the primary action the first, full-width control with a 48px minimum target, stronger typography and keyboard focus indicator.
4. Preserve a useful fallback action when no detail URL is available.
5. Generate locale-aware detail metadata from the active request locale and point to the Folio `tickets.detail` page; never hard-code `/it` in the GeoJSON action.
6. Verify marker click, modal opening, focusability, overflow and responsive layout with a real browser.

## Verification evidence

The implementation and browser evidence are recorded in STORY-514 and the local chat board entry for this work.
