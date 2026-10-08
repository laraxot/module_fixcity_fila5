---
title: STORY-045 — Map Marker Popup UI/UX Enhancement
github_issue: https://github.com/laraxot/fixcity_fila5/issues/45
discussion: https://github.com/laraxot/fixcity_fila5/discussions/45
---

## Goal
When user clicks a map marker, show a complete card (not just minimal info) with:
- Title + Status badge + Category
- Short description
- Link to tracking page (`/segnalazione-traccia/{code}`) or detail
- Date/Time
- Image thumbnail if available
- Responsive design (mobile/desktop)

## Current State (From `api/ticket-details` endpoint)
- `BuildTicketPublicDetailsPayloadAction` creates payload with `id, title, description, images, status, slug, code`
- Popup renders basic info
- Code link missing
- No tracking link
- No category info

## Fix Plan
1. Update payload to include `category_name`, `priority`, `public_code`
2. Update map popup Blade/template with full card layout
3. Add tracking link using `route('tickets.track')?code=`
4. Add image thumbnail
5. Add responsive styling

## Translation Keys (IT/EN)
- `fixcity::map.popup.title`
- `fixcity::map.popup.status`
- `fixcity::map.popup.track_link`
- `fixcity::map.popup.category`
- `fixcity::map.popup.priority`

## Second Brain
- `docs/second-brain.md`: UI patterns for public-facing components
- `docs/user-journey-maps.md`: Anonymous user map exploration