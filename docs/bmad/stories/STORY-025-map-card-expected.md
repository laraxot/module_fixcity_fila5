---
title: Map Marker Card - Expected Behavior
status: to_do
priority: must

## Expected (BMAD Spec)
When user clicks on a marker on the map, a **scheda completa** (complete card) should appear with:
- Ticket title (prominent)
- Status badge (colorful)
- Category/type icon
- Location (address)
- Description (truncated)
- Date created
- **"Vai al dettaglio" button** (clear CTA)
- Card should be positioned near marker
- Card should have shadow/border for visibility
- Card should animate in (smooth)
- Card should have close button
- On mobile: card takes full width, scrollable

## Current State
- Marker click shows basic info or nothing
- No styled card popup
- No detail link visible
- Info not prominent enough

## UI/UX Goals
- High visibility card
- Clear CTA to detail page
- Responsive design
- Accessible (ARIA labels)
- Consistent with Design Comuni style

## Metrics
- Card visible without scrolling
- CTA button clearly clickable
- Smooth animation <300ms
