---
type: decision-log
title: "Decision Log — Fixcity"
links: {github_issue: #XXX, discussion: #XXX}
---
# Decision Log — Fixcity

## Decisions

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

