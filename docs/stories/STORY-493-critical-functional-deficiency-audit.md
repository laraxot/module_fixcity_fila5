---
title: "STORY-493: Critical Functional Deficiency Audit"
type: story
status: open
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/493"
discussion_link: "https://github.com/laraxot/fixcity/discussions/493"
created_at: "2026-09-26T10:00:00Z"
tags: [phpstan, bmad, audit, sprint-1]
---

# STORY-493 — Critical Functional Deficiency Audit

## Issue / Discussion
- Issue #493: Architecture perfect (PHPStan 0 errors) but 0% of critical MVP features implemented
- Discussion #493: Planning critical path

## Evidence (pre-fix)
- bmad/sprint-status.yaml: Sprint 1 (28 pts) — 27 committed, 0 completed
- Sprint 2-7: all not_started
- All citizen-facing stories (STORY-001 through STORY-011): not_started
- Only Story 29 (segnalazioni-elenco-map-lit): in_progress

## Core Problem
Codebase has perfect architectural health (PHPStan 0 errors, module contracts, Action pattern) but zero user-facing functionality:
- Wizard (STORY-001-004): not_started
- Citizen Auth (STORY-011): not_started  
- Citizen Views (STORY-009-010): not_started
- E2E Tests (STORY-005-006): not_started

## Decision
BLOCKING for deployment. Sprint 1-2 stories must complete before MVP.

## Evidence Post-Fix Checklist
- [ ] STORY-001 through STORY-011: Citizen-facing features
- [ ] STORY-005-006: CI + Playwright
- [ ] STORY-021-026: PA backoffice
