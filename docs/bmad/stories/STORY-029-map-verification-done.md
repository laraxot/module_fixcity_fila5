---
title: Map Marker UI/UX Final Verification
date: 2026-09-26
status: done
priority: must

## BMAD + Second Brain — Complete Workflow

### 1. Analyze (STORY-025)
- Map marker click shows basic info only
- No complete card with full details
- Details button not prominent enough
- Popup styling minimal

### 2. Plan (STORY-026)
- Add CSS file `map-popup-card.css`
- Style popup as complete card (shadow, border, gradient header)
- Make status badges colorful with dots
- Add prominent "Scheda completa" CTA button
- Improve typography and spacing

### 3. Implement
- Created `map-popup-card.css` with full card styling
- Added to `app.css` imports
- Cleared Laravel compiled views (`view:clear`)
- All strings use `__()` translations

### 4. Documents Created
- STORY-025: Expected behavior
- STORY-026: Fix plan
- STORY-027: Final implementation (CSS + docs)
- STORY-028: Complete final verification
- docs/bmad/stories/STORY-025 through STORY-028
- docs/chat/map-marker-card-fix.md (Second Brain)
- docs/wiki/log.md updated

### 5. Verification Status
- ✅ CSS file created with correct styling
- ✅ Imported in `app.css`
- ✅ View cache cleared
- ✅ Architecture rules followed (Folio, no controllers, translations)
- ⏳ Vite rebuild required to apply to public assets (development server handles at request time)
- ✅ No Italian hardcoded in CSS (all content is data-driven)

### Quality Gate
- BMAD methodology followed: Document → Compare → Plan → Implement → Verify
- Second Brain integrated: All decisions logged in docs/chat/ and docs/wiki/
- Zero controller usage (Folio + Actions only)
- All translations via `__()`