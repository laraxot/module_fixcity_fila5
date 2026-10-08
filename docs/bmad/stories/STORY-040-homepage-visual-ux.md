---
title: STORY-040 — Homepage Visual/UI/UX Fix (Anonymous User)
status: in-progress
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/40
discussion: https://github.com/laraxot/fixcity_fila5/discussions/40
---

## Problem
The homepage (`/it/`) currently shows a generic CMS "Elenco segnalazioni" layout with grid of tickets, map, and CTA. This is not optimal for an anonymous user's first impression.

## Expected Behavior (What Should Be Seen)
### For Anonymous User (Guest) on Homepage (`/` or `/it/`):
1. **Clear Value Proposition**: Hero section explaining what Fixcity does
2. **Primary CTA**: Prominent button "Fai una segnalazione" that leads to wizard
3. **Secondary CTAs**: 
   - "Vedi le segnalazioni recenti" (map/list)
   - "Come funziona" (explanation)
   - "Contatti" (contact info)
4. **Visual Elements**:
   - Municipality logo/branding prominent
   - Clean, modern design following Bootstrap Italia
   - Mobile-responsive layout
   - Accessible colors and contrast
5. **Content Sections**:
   - Hero with value proposition
   - How it works (3-step process)
   - Recent public tickets (map preview or list)
   - Statistics/KPIs (tickets resolved today, avg response time)
   - FAQ accordion
   - Footer with links and contacts

## Actual Behavior (What Is Currently Seen)
- Generic CMS page with title "Elenco segnalazioni"
- Grid of ticket cards showing limited info
- Map component
- CTA button that may not be prominent
- No clear value proposition for new users
- Not following Bootstrap Italia design patterns optimally

## Gap Analysis
| Element | Expected | Actual | Gap |
|---------|----------|--------|-----|
| Hero Section | Clear value prop + primary CTA | Generic CMS title | Missing value prop |
| Primary CTA | Prominent "Fai una segnalazione" | Less prominent button | Needs emphasis |
| Value Explanation | How it works (3 steps) | Not present | Missing section |
| Recent Activity | Map preview or list | Grid of tickets + map | Could be improved |
| Statistics | KPIs (resolved today, etc.) | Not present | Missing |
| Mobile Responsiveness | Full mobile support | Partial | Needs testing |
| Accessibility | WCAG 2.1 AA | Partial | Needs audit |

## Implementation Plan
### Phase 1: Create New Homepage Blade
- Path: `Modules/Fixcity/resources/views/pages/home.blade.php`
- Replace current CMS home page with custom Fixcity homepage
- Use Bootstrap Italia components
- Include all expected sections

### Phase 2: Update Routes
- Ensure Folio route points to new blade
- Keep existing ticket listing/map under `/segnalazioni`

### Phase 3: Add Supporting Components
- Stats component (tickets resolved, avg response time)
- How-it-works section with icons
- FAQ accordion
- Newsletter signup (optional)

### Phase 4: Quality Assurance
- PHPStan: 0 errors
- Pint: compliant
- Visual test: compare with Bootstrap Italia examples
- Accessibility: axe-core scan
- Responsiveness: test 320px, 768px, 1440px

## Second Brain Updates
- Document in `docs/bmad/stories/STORY-040-homepage-visual-ux.md`
- Update `docs/user-journey-maps.md` (once superseded)
- Add to `docs/second-brain.md` under UI/UX patterns

## Dependencies
- None - self-contained UI improvement
- Uses existing Ticket model queries for stats
- Uses existing routes/actions where possible

## Test Cases
1. Anonymous user sees hero with clear value prop
2. Primary CTA stands out visually
3. How-it-works section explains 3-step process
4. Recent activity section shows map or list
5. Statistics section loads without errors
6. Page is fully responsive
7. Page passes basic accessibility checks
8. Navigation to wizard works from CTA