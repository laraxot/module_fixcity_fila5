---
type: investigation
title: "Investigation DesignComuni Frontend Redesign - TailwindCSS + Alpine.js + Lit + DaisyUI"
links: {github_issue: #55}
---

# Investigation: DesignComuni Frontend Redesign

## Problem Statement
The Fixcity module frontend currently uses a mix of Bootstrap Italia, custom CSS, and legacy patterns. It needs a complete redesign to match the Italian Design Comuni design system specifications exactly, using a modern stack: TailwindCSS + Alpine.js + Lit + DaisyUI + Flowbite + Filament (NO Bootstrap Italia).

## Current State Analysis

### Existing Frontend Architecture
- **Theme**: Sixteen theme in `laravel/Themes/Sixteen/` with extensive Design Comuni CSS tokens and components
- **Fixcity Module**: Uses actions for data loading (`LoadDesignComuniElencoDemoCardsAction`, `LoadDesignComuniElencoFilterCatalogAction`)
- **JSON Data**: `disservizio-lista.json` for filter catalog (12 categories with counts)
- **CSS Structure**: Multiple CSS files in `resources/css/components/` with Design Comuni tokens
- **Reference Pages**: Design Comuni reference pages analyzed in `docs/design-comuni/` with HTML comparisons

### Design Comuni Reference Requirements
Primary reference: https://italia.github.io/design-comuni-pagine-statiche/index.html
Key pages to match:
1. **Homepage** - Brand header, hero, services grid, news, footer
2. **Segnalazioni Elenco** - Filter sidebar, results table/cards, pagination
3. **Segnalazione Dettaglio** - Report detail with timeline, map, attachments
4. **Segnalazione Crea** - Multi-step wizard form
5. **Servizi** - Service catalog with categories
6. **All other Design Comuni page types**

### Token System (from Sixteen theme)
- Colors: `--dc-blue-dark (#003D73)`, `--dc-blue-primary (#0066CC)`, `--dc-success (#00B373)`, etc.
- Typography: Titillium Web (sans), Roboto Mono (mono), modular scale
- Spacing: 8px base grid, consistent spacing tokens
- Shadows: Named elevation levels only
- Borders: Defined radii and colors
- Transitions: Standardized durations

### Issues Identified
1. **Hardcoded colors**: `LoadDesignComuniElencoFilterCatalogAction` uses `#007a53` instead of tokens
2. **Mixed CSS approach**: Bootstrap Italia classes mixed with custom Design Comuni CSS
3. **No Tailwind integration**: Design tokens not available as Tailwind utilities
4. **Missing interactive states**: Components lack proper hover/focus/disabled states
5. **No reduced motion support**: Animations don't respect `prefers-reduced-motion`
6. **Component duplication**: Similar components in multiple CSS files
7. **No Lit web components**: Complex UI elements not encapsulated as web components

## Proposed Solution

### Phase 1: Foundation Setup
1. Install TailwindCSS, Alpine.js, Lit, DaisyUI, Flowbite via npm in Sixteen theme
2. Create `tailwind.config.js` with Design Comuni tokens mapped to Tailwind theme
3. Configure PostCSS pipeline for Tailwind + existing CSS
4. Set up Lit component build pipeline
5. Configure Alpine.js for interactivity

### Phase 2: Token System Migration
1. Convert all Design Comuni CSS tokens to Tailwind theme values
2. Create Tailwind utility classes for all Design Comuni components
3. Replace hardcoded colors in PHP actions with token references
4. Ensure APCA contrast compliance (≥75 body, ≥45 large-bold)

### Phase 3: Component Library
1. Create base components with DaisyUI + Tailwind:
   - Buttons (primary, secondary, outline, ghost)
   - Form inputs, selects, checkboxes, radios
   - Cards (report cards, service cards)
   - Tables with proper states
   - Modals, dropdowns, tooltips
   - Pagination, breadcrumbs
   - Alerts, badges, tags
   - Accordion, tabs
2. Create Alpine.js components for:
   - Filter sidebar (collapsible, searchable)
   - Report list (sort, filter, pagination)
   - Wizard steps (segnalazione crea)
   - Mobile navigation
3. Create Lit web components for:
   - Map integration (Leaflet/MapLibre)
   - File upload with preview
   - Rich text editor
   - Date picker
   - Autocomplete search

### Phase 4: Page Implementation
1. **Homepage**: Hero, services grid, news carousel, footer
2. **Segnalazioni Elenco**: Filter sidebar + results area with cards/table toggle
3. **Segnalazione Dettaglio**: Timeline, map, attachments, actions
4. **Segnalazione Crea**: Multi-step wizard with validation
5. **Servizi**: Category grid, service cards, search/filter
6. **All other pages**: Following Design Comuni patterns

### Phase 5: Accessibility & Quality
1. WCAG 2.1 AA compliance audit
2. Focus visible states for all interactive elements
3. Reduced motion fallbacks
4. Semantic HTML structure
5. ARIA labels where needed
6. Color contrast verification

## Acceptance Criteria
- [ ] All Design Comuni tokens available as Tailwind utilities
- [ ] Zero hardcoded hex colors in CSS/JS/PHP
- [ ] All interactive elements have: default, hover, focus-visible, active, disabled states
- [ ] All animations respect `prefers-reduced-motion`
- [ ] Visual parity with Design Comuni reference pages (pixel-perfect)
- [ ] PHPStan level 10 passes (zero errors)
- [ ] Pest tests pass
- [ ] Pint code style passes
- [ ] ux_audit passes on all generated CSS
- [ ] Components work in Filament admin panels
- [ ] Build produces optimized, purged CSS

## Next Steps
1. Create decision-log.md with technology choices
2. Run spike-prototype for Lit + Alpine integration
3. Create detailed plan-work tasks in epic capsule
4. Begin Phase 1 implementation
5. Set up visual regression testing

## References
- Design Comuni Reference: https://italia.github.io/design-comuni-pagine-statiche/index.html
- Design Comuni Servizi: https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html
- Design Comuni GitHub: https://github.com/italia/design-comuni-pagine-statiche
- Sixteen Theme Analysis: `laravel/Themes/Sixteen/docs/design-comuni/`
- BMAD Story: `docs/stories/designcomuni-frontend-redesign.story.md`