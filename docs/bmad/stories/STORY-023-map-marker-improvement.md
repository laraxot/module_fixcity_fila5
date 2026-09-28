---
title: Map Marker Improvements
status: to_do
priority: must

## Current State
- Map: Placeholder div in tickets-map-widget.blade.php
- No markers visible
- No popup information
- No link to ticket detail

## Expected State
- Map: Leaflet/Mapbox with click handlers
- Markers: For each ticket with geolocation
- Popup: Show ticket details (title, status, priority) with styling
- Link: "View details" button linking to ticket detail page
- Accessibility: ARIA labels and keyboard navigation
- Performance: Lazy loading, clustering for many markers

## Differences
- Current: Static placeholder
- Expected: Interactive map with clickable markers

## Location to fix
1. tickets-map-widget.blade.php (UI)
2. LoadPublicTicketsGeoJsonAction (data source)
3. Map initialization JavaScript (frontend)
4. Ticket detail route for links

## BMAD Fix Plan
1. Create LoadMapMarkersAction (extract markers from GeoJSON)
2. Create TicketPopupView (Blade component for popup content)
3. Create MapComponent (Blade + Alpine + Leaflet)
4. Update tickets-map-widget.blade.php to use new map
5. Ensure ticket detail link uses correct route

## Quality Gates
- Map works on desktop and mobile
- Markers show popup on click
- Popup contains ticket info and detail link
- All strings traduibili tramite __()
- No Italian hardcoded in map JavaScript
