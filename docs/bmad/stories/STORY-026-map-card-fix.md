---
title: Map Marker Card - Fix Plan
status: to_do
priority: must

## Fix Plan

### 1. Modify my-map.js
- Add popup on marker click
- Style popup as a card with shadow/border
- Show: title, status badge, address, city, detail link
- Animate card in
- Add close button
- Mobile responsive

### 2. Data available
- `feature.properties.id`
- `feature.properties.title`
- `feature.properties.type` (icon + label)
- `feature.properties.address`
- `feature.properties.city`
- `feature.properties.status` (badge color)
- `feature.properties.url`

### 3. Implementation details
- Use Leaflet popup with custom HTML
- CSS for card styling (shadow, border, animation)
- Translation keys for strings (fixcity::map.*)
- Ensure link to detail page works

## Acceptance Criteria
- Card visible on marker click
- Card has title + status badge + address
- "Vai al dettaglio" button present
- Card animates in smoothly
- Mobile responsive
