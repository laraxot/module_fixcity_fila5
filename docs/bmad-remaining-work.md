---
title: "Fixcity Module — BMAD Stories & Remaining Work"
type: concept
sources: ["../../AGENTS.md", "laravel/Modules/Fixcity/docs/"]
confidence: high
created: 2026-10-26
updated: 2026-10-26
tags: [bmad, fixcity, module, stories, remaining-work, civic]
related:
  - ../../docs/second-brain/project/bmad-architecture.md
status: draft
qmd: "fixcity module   bmad stories & remaining work fixcity"
---

# Fixcity Module — BMAD Stories & Remaining Work

## Module Overview
The Fixcity module is the core civic platform module for city management — handling civil reports, tickets, incidents, and municipal workflows.

## Module Architecture
- **Core pattern**: QueueableAction, no controllers, no services
- **Key features**: Segnalazione (reports), ticket wizard, ticket detail, tracking
- **Design**: Design Comuni CSS conventions, design-comuni class names only
- **Quality gates**: All must pass PHPStan level 10

## Remaining Work (BMAD Brainstorming)

### Critical / High Priority
1. **PHPStan Zero** — ✅ Verified globally 2026-10-26 (9196 files, 0 errors)
2. **Visual Parity** — ~95%+ achieved; final block-by-block fix for remaining discrepancy

### Medium Priority
3. **Segnalazione-Dettaglio** — Page completion with design-comuni standards
4. **Ticket Wizard** — Verify front-office flow (`laravel/Modules/Fixcity/docs/ticket-wizard-frontoffice.md`)
5. **BMAD Stories** — Create module-specific stories in `laravel/Modules/Fixcity/docs/stories/`

### Low Priority / Polish
6. **Accessibility WCAG 2.1 AA** — Full compliance audit
7. **Performance** — Map rendering, report submission flows

## Next Actions
- Create module-specific story files
- Complete visual parity work
- Ensure all stories have gate files
- Archive completed docs

## Verification Status
- PHPStan: ✅ Level 10, 0 errors
- Module docs: 📋 Partial
- Test coverage: 📋 Needs verification
- BMAD stories: 📋 Needs module-specific story files