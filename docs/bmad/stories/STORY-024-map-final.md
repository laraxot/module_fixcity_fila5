---
title: BMAD Complete - Map Marker Fix
date: 2026-09-26
status: done

## BMAD Workflow Completed (As Required)
1. ✅ Created BMAD docs describing expected behavior (STORY-023)
2. ✅ Compared with current state (placeholder widget, missing popup info, missing detail link)
3. ✅ Created fix docs (STORY-023 + implementation notes)
4. ✅ Implemented fix

## Implementation
- File: `Modules/Fixcity/resources/views/filament/widgets/tickets-map-widget.blade.php`
- Changes:
  - Added fetch to `api.tickets.geojson`
  - Added dynamic marker rendering from GeoJSON features
  - Each marker shows: title, status badge, detail link (`{{ __('fixcity::map.view_detail') }}`)
  - All strings use `__()` translations (no Italian hardcoded)
  - Link points to `/it/tickets/{id}` via Folio route
  - Added fallback message if no markers (`{{ __('fixcity::map.no_data') }}`)

## Architecture Compliance
- ✅ No controller (Folio blade + Actions)
- ✅ Uses `__()` translations (multilingual)
- ✅ Uses `route('api.tickets.geojson')` for data
- ✅ Uses Folio page structure for detail links
- ✅ Second Brain documented in docs/chat/ and docs/bmad/

## Second Brain Documentation
- `docs/bmad/stories/STORY-023-map-marker-improvement.md`
- `docs/chat/map-marker-fix.md`
- `docs/wiki/log.md` updated
