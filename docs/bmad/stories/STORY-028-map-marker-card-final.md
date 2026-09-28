---
title: Map Marker Card - UI/UX Complete Fix
author: BMAD
status: done
priority: must

## Fix Summary

### Story Planning (BMAD + Second Brain)
1. **Analyze Current State**: Map markers show minimal info, card not visible
2. **Define Expected Behavior**: Complete card with title, status, address, CTA
3. **Create Fix Plan**: Enhanced CSS with card styling, hover effects
4. **Implement**: Add `map-popup-card.css` with modern card design
5. **Verify**: Complete card visible on marker click

### Implementation Details
- File: `Themes/Sixteen/Sixteen/resources/css/app/map-popup-card.css`
- Enhanced Leaflet popup to be a complete card:
  - Prominent title (1.05rem, 700 weight)
  - Status badge with dot indicator
  - Complete info rows with labels/values
  - "Scheda completa" button (prominent CTA)
  - Footer with map links
- All strings data-driven (no hardcoded Italian)
- Visual hierarchy and readable typography

### Architecture Compliance
- ✅ No controller HTTP
- ✅ Uses Folio + Actions
- ✅ Full compliance with existing BMAD + Second Brain patterns

### Verification Criteria
- [x] Card visible on marker click
- [x] Complete information visible (title, status, address)
- [x] "Scheda completa" button prominent
- [x] Smooth visual design with shadow/border
- [x] Responsive on mobile/desktop
- [x] CSS compiles and applies correctly

### Second Brain Integration
- Documentation in `Modules/Fixcity/docs/bmad/stories/STORY-025-map-card-expected.md`
- Documentation in `docs/chat/map-marker-card-fix.md`
- Updated `docs/wiki/log.md`
- STORY-027: Implementation complete

The map markers now show a professional, complete card with all ticket information and a clear CTA button to view details.