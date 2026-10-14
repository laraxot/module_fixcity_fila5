---
title: "STORY-054 — Ticket map marker visibility fix (Design Comuni green #007A52, no question-mark)"
status: open
module: Fixcity
github_issue: "#54"
discussion: "#54"
type: story
tags: ["map-marker", "design-comuni", "wcag", "visibility", "ticket-marker-visible"]
qmd: "map-marker visibility design-comuni #007A52"
---

## Bug Description
The map marker in the Sixteen theme (`laravel/Themes/Sixteen/resources/views/tickets/index.blade.php`) is described as "nero piccolo con un punto di domanda a fianco" — a small black marker with a question-mark overlay. The current SVG uses `fill="#007a52"` but the white-stroke + black-marker combination made it appear dark/small and the `?` icon overlay (question mark) is incorrect per Design Comuni specifications. The marker was not fully visible at all zoom levels and failed WCAG contrast/alt-text requirements.

## Design Reference
Design Comuni standard marker (reference `design-comuni-pagine-statiche`): green PA `#007A52`, proper 36×48px size, white inner circle (`#ffffff`) with `#007A52` stroke, no extra icon/question overlay.

## Fix Applied
Updated `ticket-marker-visible` SVG in `laravel/Themes/Sixteen/resources/views/tickets/index.blade.php`:
- Fill `#007a52` (Design Comuni PA green)
- White inner circle with `#007a52` stroke
- Explicit `width="36" height="48"`
- Improved `aria-label="Segnalazione sul territorio"`
- `aria-hidden="false"`, `role="img"`
- No question-mark overlay

## Quality Gates
- [x] Marker visible per Design Comuni (green PA #007A52)
- [x] No question-mark icon overlay
- [x] WCAG `aria-label` + `role="img"` present
- [x] Size 36×48 with proper anchor
