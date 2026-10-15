---
title: STORY-023 — Fixcity Ticket Wizard UI/UX Responsive Parity
status: in-progress
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/23
discussion: https://github.com/laraxot/fixcity_fila5/discussions/23
type: story
created: legacy
updated: '2026-09-26'
tags:
- bmad
- fixcity
qmd: 023 wizard responsive uiux.story FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Objective
Complete responsive parity (Phase 4) for the CreateSegnalazioneWizardWidget (M0 milestone).

## Current State
- ✅ Phase 1: Filament Schemas correction
- ✅ Phase 2: CSS Visual Parity (`filament-wizard-parity.css`)
- ✅ Phase 3: Bootstrap Italia section structure (Luogo, Disservizio, Autore)
- ⏭ Phase 4: Responsive parity — PENDING
- ✅ Phase 5: Multilingual verification (IT/EN)

## Tasks
- [ ] Verify wizard renders correctly on mobile (320px–768px)
- [ ] Add `@media` queries for wizard grid (3-col author → 1-col mobile)
- [ ] Add touch-friendly sizing for form elements
- [ ] Verify accessible keyboard navigation
- [ ] Verify ARIA labels for screen readers
- [ ] Test on Safari iOS / Chrome Android

## UI/UX Checklist
- [ ] Visual parity with Design Comuni `segnalazione-02-dati.html`
- [ ] Mobile-first responsive breakpoints
- [ ] Accessible form labels and error messages
- [ ] Loading states for submit button
- [ ] Success/error feedback toast
- [ ] Wizard step indicator (progress bar)

## Quality Gate
- ✅ PHPStan: 0 errors in Fixcity module
- ✅ Pint: compliant
- ⏭ Pest: pending DB
