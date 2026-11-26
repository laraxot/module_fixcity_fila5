---
title: STORY-037 — Public Ticket Tracking Page
status: in-progress
module: Fixcity
actor: Citizen (Anonymous)
github_issue: https://github.com/laraxot/fixcity_fila5/issues/27
discussion: https://github.com/laraxot/fixcity_fila5/discussions/27
type: story
created: legacy
updated: '2026-09-26'
tags:
- bmad
- fixcity
qmd: STORY 037 public ticket tracking page FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Objective
Implement public ticket tracking page where citizens can follow their ticket status using a unique code.

## Expected Flow
```
Citizen → /segnalazione-traccia/{code} → Page shows ticket status + timeline
                               ↓
           If code invalid → Show error + form to retry
                               ↓
           If valid → Show: 
             - Ticket ID / Code
             - Current Status (badge)
             - Timeline (activities)
             - Categoría / Prioridad
             - Fecha creación / Data creazione
```

## Implementation Plan

### Step 1: Folio Page (Quick Flow)
- Path: `Modules/Fixcity/resources/views/pages/segnalazione-traccia.blade.php`
- Route: Folio `segnalazione-traccia.create` with `{code}` parameter
- Action: `GetPublicTicketByCodeAction`

### Step 2: Action to Fetch Ticket
```php
app(GetPublicTicketByCodeAction::class)->execute(string $code): ?Ticket
```

### Step 3: Vue/Blade Component
- Progress timeline showing: new → assigned → in_progress → resolved → closed
- Status badges with colors (from `TicketStatusEnum`)
- Responsive design (Bootstrap Italia)

## Quality Gate
- [ ] PHPStan: 0 errors in Fixcity module
- [ ] Pint: compliant
- [ ] UI/UX: verified in mobile/desktop
- [ ] Translation: IT/EN keys added

## Second Brain Updates
- Action documented in `docs/bmad/stories/`
- Flow documented in `docs/user-journey-maps.md` section "Anonymous User: Tracking"
