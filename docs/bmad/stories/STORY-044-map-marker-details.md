---
title: STORY-044 — Map Marker Details Improvement
status: in-progress
module: Fixcity + Theme Sixteen
github_issue: https://github.com/laraxot/fixcity_fila5/issues/44
discussion: https://github.com/laraxot/fixcity_fila5/discussions/44
---

## Problem
When clicking on a map marker (GeoJSON), the popup/details don't show properly — either missing link to detail page, missing information, or broken display.

## Expected
When user clicks marker:
- Popup shows: ticket title, status badge, category, code
- Link to public tracking page: `/segnalazione-traccia/{code}`
- Or link to detail: `/segnalazioni/{id}` (if public)
- Responsive popup (mobile/desktop)

## Actual (Current State)
- `BuildPublicTicketsGeoJsonAction` creates GeoJSON payload with `id, title, description, images, status, slug, code`
- Popup details reference `api/ticket-details/[ticket]` endpoint
- The popup may not show code, or the detail link may be missing
- No direct link to tracking page from marker

## Implementation
1. **Fix GeoJSON payload**: Ensure `code` is included (only for owner/authenticated users per capability rules)
2. **Fix popup template** (`map.blade.php` or `geojson.blade.php`):
   - Add tracking link using `route('tickets.track')` or direct URL `/segnalazione-traccia/{code}`
   - Show status with proper badge styling
   - Show category/type
3. **Update Translation Keys**: Add `fixcity::map.popup.*` keys
4. **Accessibility**: Ensure popup has ARIA roles, keyboard navigation

## BMAD References
- `STORY-037` — Public tracking page
- `actor-flows.md` — Anonymous user journey (mappa pubblica)
- `user-journey-maps.md` — Guest: Esplora segnalazioni pubbliche

## Quality Gate
- Marker click shows complete info
- Tracking link works
- Popup responsive (mobile/desktop)
- Translation keys present (IT/EN)

## Second Brain Updates
- `docs/second-brain.md`: Geo flow section
- `docs/user-journey-maps.md`: Update anonymous user map section