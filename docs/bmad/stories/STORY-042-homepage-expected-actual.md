---
title: STORY-042 — Homepage Expected vs Actual (BMAD)
status: in-progress
module: Fixcity + Theme Sixteen
github_issue: https://github.com/laraxot/fixcity_fila5/issues/42
discussion: https://github.com/laraxot/fixcity_fila5/discussions/42
---

## Objective
Document what the homepage (`/it`) SHOULD look like vs what it shows NOW, then fix.

## Expected Homepage (Anonymous Guest, `/`)

### Section 1: Hero
- **Title**: "FixCity — Segnalazioni Civiche"
- **Subtitle**: "Segnala disservizi, monitora lo stato, partecipa alla città"
- **Primary CTA**: "Fai una segnalazione" → `/it/segnalazione-crea`
- **Secondary CTA**: "Traccia una segnalazione" → `/it/segnalazione-traccia`
- **Logo**: Comune name prominently shown

### Section 2: How It Works (3 steps)
1. **Segnala** → "Scegli la categoria, descrivi il problema, allega foto"
2. **Traccia** → "Ricevi un codice, segui lo stato in tempo reale"
3. **Risolvi** → "La PA riceve la tua segnalazione e aggiorna lo stato"

### Section 3: Live Map / Recent Tickets
- Map with recent tickets (geo markers)
- List of 6 most recent tickets
- Filter by status/category

### Section 4: Statistics
- Total tickets today
- Resolved today
- Pending
- In progress

### Section 5: Footer
- Links: privacy, accessibility, contacts
- Social media

## Current Homepage (WHAT WE SEE)
- `Themes/Sixteen/resources/views/pages/home.blade.php` renders:
  - Hero with title/subtitle
  - CTA buttons
  - How It Works section
  - **BUG**: Undefined `$loginUrl` error (500)
  - **BUG**: `route('tickets.list')` not defined (500)

## Gap Analysis
| Element | Expected | Actual | Status |
|---------|----------|--------|--------|
| Hero | Title + subtitle + CTA | Partial — broken by undefined var | 🐛 |
| CTA Create | Link to wizard | ✅ `route('tickets.create')` | ✅ |
| CTA Track | Link to tracking | ❌ Missing | 🔴 |
| How It Works | 3-step | ✅ Present | ✅ |
| Stats | KPI cards | ❌ Not in theme view | 🔴 |
| Live Map | Map preview | ❌ Not in theme view | 🔴 |
| Recent Tickets | List | ✅ From HomeController | ✅ |
| Login/Register | Buttons | ✅ Present but broken | 🐛 |
| Mobile responsive | Full support | ⚠️ Need browser test | ⚠️ |
| Accessibility | WCAG 2.1 AA | ⚠️ Need audit | ⚠️ |

## Implementation Plan
### Phase 1: Fix bugs (today)
1. Fix `$loginUrl` undefined — add defaults to theme view OR update controller
2. Fix `route('tickets.list')` — create route OR replace with URL

### Phase 2: Add missing sections (tomorrow)
1. Add stats section to theme home
2. Add live map section
3. Add track CTA

### Phase 3: Quality assurance
1. PHPStan: 0 errors
2. Pint: compliant
3. Browser test: 320px/768px/1440px
4. Accessibility scan

## Second Brain Reference
- `docs/bmad/stories/STORY-042-homepage-expected-actual.md` (this story)
- `docs/second-brain.md` (UI/UX patterns)
- `docs/user-journey-maps.md` (actor journeys)