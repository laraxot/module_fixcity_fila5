---
type: decision-log
title: "Decision Log — Fixcity"
links: {github_issue: #XXX, discussion: #XXX}
status: draft
updated: 2026-10-11
tags: [docs, fixcity]
qmd: "decision log   fixcity fixcity"
---
# Decision Log — Fixcity

## Decisions

### 2026-10-22: Canonical Folio routes for public ticket links
- **Choice**: Solo shell tema — `container0.index` (lista) e `container0.view` con
  `container0=tickets` e `slug0=<id|create|track>` (dettaglio / crea / traccia).
  Area personale: `container0.view` + `area-personale` + slug CMS. **Vietati**
  `route('tickets.*')`, `route('tickets.show')`, e `LaravelLocalization::localizeUrl()`
  nei Blade FO.
- **Rationale**: La risposta HTTP arriva da `[container0]/[slug0]` del tema, non
  dalle Folio modulo omonime. `localizeUrl` nel Blade duplica la responsabilità
  della rotta e reincarna regressioni (vedi [frontoffice-link-conventions.md](./frontoffice-link-conventions.md)).
- **Status**: Required (religione)
- **Scope**: Fixcity contract + Sixteen blocks; test Unit/Views `Frontoffice*ContractTest`.

### 2026-10-22: FO i18n — chiavi traduzione, non stringhe nel Blade
- **Choice**: Multilingua `it|en|de|es`. Chrome tema → `pub_theme::` (navigation /
  header / auth.logout / ui); dominio → modulo owner (`fixcity::…`); Filament
  senza `->label()`. Pattern `module::context.collection.element.type`.
- **Rationale**: URL localizzati dalla rotta Folio; la copy non può restare italiana
  hardcoded nei Blade blog/events/logout. Governance Lang + blueprint Fixcity/Sixteen.
- **Status**: Required (religione)
- **Scope**: Sixteen blocks + lang; docs frontoffice-link-conventions + filament-blueprint.

### 2026-10-22: FO shell — alias IT + 404 senza CMS + APP_ENV local
<!-- Supersedes: shell vuote 200 su path IT; linked from frontoffice-link-conventions.
User: «analizzare tutto il front office… bmad + second brain + filament blueprint + playwright mcp» -->
- **Choice**: Alias `servizi|eventi|novita|faq|segnalazioni` → container canonici (301);
  `area-personale` index → login/pratiche; container senza `*.index` CMS → **404**.
  Dev server FO: `APP_ENV=local` (mai `testing`: lista ticket risulta vuota).
- **Rationale**: Shell solo header/footer sono incoerenti; path Design Comuni IT devono
  risolvere. Elenco pubblico = stati `canViewByAll` + `location` not null.
- **Status**: Required
- **Scope**: Sixteen Folio `[container0]/index`; `ListPublicTicketsAction` URL `container0.view`.

### 2026-01-01: DesignComuni Frontend Redesign — TailwindCSS + Alpine.js + Lit + DaisyUI
- **Choice**: Replace Bootstrap Italia with TailwindCSS + Alpine.js + Lit + DaisyUI + Flowbite + Filament
- **Rationale**: Modern utility-first approach following impeccable.style and tasteskill.dev principles; aligns with Design Comuni visual requirements using token-based design
- **References**: impeccable.style, tasteskill.dev, ui-ux-pro-max-skill, threejs-skills
- **Status**: Approved, in progress
- **Impact**: All Fixcity frontend pages redesigned; Sixteen theme CSS converted to Tailwind utilities

### 2026-01-01: Design Token System — Token-Only Policy
- **Choice**: All colors, spacing, shadows, typography must reference Design Comuni tokens (no hardcoded hex)
- **Rationale**: Design Comuni reference uses exact token values; token-only ensures visual consistency and maintainability
- **Status**: Enforced

### 2026-01-01: Component Architecture — DaisyUI + Lit Web Components
- **Choice**: DaisyUI for base components; Lit for complex reusable web components; Alpine.js for interactivity
- **Rationale**: DaisyUI provides Design Compliant base styles; Lit ensures encapsulation; Alpine handles lightweight reactivity
- **Status**: Configured

### 2026-01-01: Accessibility — WCAG 2.1 AA + States + Reduced Motion
- **Choice**: All interactive elements declare default/hover/focus-visible/active/disabled + prefers-reduced-motion fallback
- **Rationale**: Design Comuni requires accessible interfaces; strict mode enforces full state coverage
- **Status**: Required

### 2026-10-11: Verification Tooling — Mauve, Playwright, and Lit Testing
- **Choice**: Install and configure mauve, puppeteer, playwright-mcp for comprehensive testing
- **Rationale**: Need automated visual regression, accessibility audit, and component testing to ensure Design Comuni compliance
- **References**: mauve.isti.cnr.it, playwright.dev
- **Status**: Installed and configured
- **Impact**: Enables automated quality gates for accessibility, visual parity, and performance

### 2026-10-11: Architecture Plan — Component-Based Frontend Redesign
- **Choice**: Adopted comprehensive architecture plan with foundation, component library, page implementation, and quality assurance phases
- **Rationale**: Structured approach ensures systematic redesign while maintaining quality standards
- **Status**: Approved
- **Impact**: Provides clear roadmap for frontend redesign with measurable milestones

### 2026-10-11: Token Migration Strategy — Systematic CSS Conversion
- **Choice**: Convert all existing Design Comuni CSS tokens to Tailwind utility classes
- **Rationale**: Ensures Design Comuni design system is fully available in modern utility-first approach
- **Status**: Defined in plan
- **Impact**: Enables zero hardcoded hex values and full token compliance

## Open Questions
- [ ] Confirm Design Comuni reference for specific component variations (cards, filters)
- [ ] Verify Tailwind build configuration with Filament admin theme
- [ ] Test Lit component integration with Alpine.js in production build
- [ ] Schedule visual regression baseline capture
- [ ] Plan accessibility audit timeline with mauve
- [ ] Define component library completion criteria
- [ ] Establish production deployment checklist
- [ ] Configure automated testing in CI/CD pipeline
- [ ] Schedule stakeholder review meetings
- [ ] Plan documentation and knowledge transfer activities
- [ ] Define success metrics and measurement methodology
- [ ] Establish feedback collection process for iteration

## D-PA-NAV — Tab area personale identici su tutte le pagine (2026-10-22)

- **Decisione:** chrome unico `Themes/Sixteen/.../personal-area/heading.blade.php` + `navigation.blade.php` per pratiche, seguite, notifiche, impostazioni.
- **Perche:** i tab sono la mappa dell'area; placement diverso o nav assente su una sorella e' bug di prodotto (DCP-110/111).
- **Anti-regressione:** feature test sulle quattro rotte; docs Sixteen `personal-area-shared-nav-tabs-contract` + `why-personal-area-tabs-must-be-identical`.
