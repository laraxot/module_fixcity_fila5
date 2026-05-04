---
title: Farmshops.eu Clustering Pattern Integration
description: Implementation of farmshops.eu marker clustering behavior in Fixcity GeoMapLit
category: geo-patterns
---

# Farmshops.eu Clustering Pattern Integration

## Overview

This document describes how the GeoMapLit component in Fixcity implements the clustering behavior from farmshops.eu (`direktvermarkter.js`).

## Key Differences from Original Farmshops.eu

### 1. Icon System
- **farmshops.eu**: Uses ExtraMarkers with FontAwesome icons (fa-number)
- **Fixcity**: Uses custom SVG markers with color-coded pins

### 2. Cluster Icons
- **farmshops.eu**: Uses PNG icons for each type (hof.png, markt.png, etc.)
- **Fixcity**: Uses colored SVG circles with type indicators

### 3. Cluster Radius Logic
Both implementations use the same radius logic:
```javascript
maxClusterRadius: (z) => z < 12 ? 80 : 45
```

### 4. Cluster Content
Both implementations follow the same pattern:
- Zoom < 8: Show only count
- Zoom >= 8: Show count + type indicators

## Implementation Details

### Cluster Icon Creation
The `_createClusterIcon` method creates:
- 80px diameter circle for zoom < 12
- 45px diameter circle for zoom >= 12
- Only count when zoom < 8
- Count + type indicators when zoom >= 8

### AJAX Popup Pattern
Implements the same pattern as farmshops.eu:
```javascript
layer.once('click', () => {
    fetch(`/api/ticket-details/${id}`)
        .then(res => res.json())
        .then(detail => {
            // Update popup with fetched data
        });
});
```

## Testing Strategy

### Manual Testing Checklist
- [ ] Verify clusters form at different zoom levels
- [ ] Check cluster icon size changes at zoom 12
- [ ] Test popup loads details via AJAX
- [ ] Verify type indicators appear at zoom 8
- [ ] Test mobile responsiveness

### Automated Testing with Playwright
- Cluster rendering at zoom 7, 9, 12
- AJAX popup content loading
- Responsive behavior on different screen sizes

## Performance Considerations

1. **Chunked Loading**: Enabled in markerClusterGroup for better performance with large datasets
2. **Outside Bounds Removal**: Removes markers outside viewport to improve performance
3. **MutationObserver**: Uses depth 12 for proper Filament 5 wizard integration

## Files Modified
- `laravel/Modules/Geo/resources/js/components/geo-map-lit.js`
- `laravel/Modules/Geo/docs/wiki/concepts/geo-map-cluster.md`
- `laravel/Modules/Fixcity/docs/wiki/concepts/farmshops-clustering-integration.md`

## Related Documentation
- [GeoMapLit Component](../entities/geo-map-lit.md)
- [Second Brain Documentation](../../concepts/geo-map-widget-architecture.md)