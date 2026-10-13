---
title: STORY-041 — Fix Missing Route tickets.list
status: in-progress
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/41
discussion: https://github.com/laraxot/fixcity_fila5/discussions/41
type: note
updated: 2026-10-11
tags: [docs, fixcity, bmad]
qmd: "story 041 fix route tickets list fixcity"
---

## Problem
Route `[tickets.list]` not defined causing 500 error on homepage `/it`.

## Root Cause
Template(s) reference `route('tickets.list')` but no such route exists in Folio or web routes.

## Files Affected
- `Modules/Fixcity/resources/views/pages/home.blade.php` (if using this route)
- `Themes/Sixteen/config/local/fixcity/database/content/pages/home.json` - uses
  `route('container0.view',['container0' => 'tickets','slug0' => 'create' ])`
  for the CMS/Folio create mount.
- May be other templates referencing non-existent routes

## Solution
1. Identify all `route('tickets.*')` references in templates
2. Map to actual existing routes:
   - `container0.view` + `container0=tickets` + `slug0=create` →
     `/tickets/create` (Folio mount: `[container0]/[slug0]/index`)
   - `tickets.track` → `/tickets/track` (Folio: `tickets.track`)
   - Need to create `tickets.list` → `/segnalazioni` (public list) OR replace with direct URL

## Implementation
- Replace non-existent route with actual route or direct URL
- Create folio page for public tickets list if needed

## Quality Gate
- No RouteNotFoundException
- Homepage renders without 500
