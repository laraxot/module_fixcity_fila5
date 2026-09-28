---
title: STORY-041 — Fix Missing Route tickets.list
status: in-progress
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/41
discussion: https://github.com/laraxot/fixcity_fila5/discussions/41
---

## Problem
Route `[tickets.list]` not defined causing 500 error on homepage `/it`.

## Root Cause
Template(s) reference `route('tickets.list')` but no such route exists in Folio or web routes.

## Files Affected
- `Modules/Fixcity/resources/views/pages/home.blade.php` (if using this route)
- `Themes/Sixteen/resources/views/pages/home.blade.php` - uses `route('tickets.create')` (exists)
- May be other templates referencing non-existent routes

## Solution
1. Identify all `route('tickets.*')` references in templates
2. Map to actual existing routes:
   - `tickets.create` → `/tickets/create` (Folio: `tickets.create`)
   - `tickets.track` → `/tickets/track` (Folio: `tickets.track`)
   - Need to create `tickets.list` → `/segnalazioni` (public list) OR replace with direct URL

## Implementation
- Replace non-existent route with actual route or direct URL
- Create folio page for public tickets list if needed

## Quality Gate
- No RouteNotFoundException
- Homepage renders without 500