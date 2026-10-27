---
title: "FixCity product requirements"
type: prd
version: "1.0"
status: draft-for-owner-review
updated: 2026-10-07
tags: [bmad, prd, fixcity, enterprise]
qmd: "FixCity PRD requirements citizen ticket municipality PA workflow"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Product Requirements Document

## Executive Summary

FixCity is an accessible, tenant-aware civic-reporting service that makes municipal
issues observable from citizen submission through PA resolution.

## Project Overview

The brownfield Laravel modular monolith already contains the core ticket, user, geo,
media, notification, theme and administration capabilities. This PRD defines the
remaining evidence and implementation required for a safe candidate release.

## Executive summary

FixCity is an accessible, tenant-aware civic-reporting service. It replaces opaque,
fragmented municipal intake with a traceable ticket loop while preserving strict
citizen privacy and PA accountability.

## Functional Requirements

### Citizen and public experience

- **FR-001 MUST — Public discovery:** guests can view the localized public list/map
  and published ticket details without private data.
- **FR-002 MUST — Authenticated report:** a citizen can enter title, description,
  category, location, approved privacy acknowledgement and media, then submit.
- **FR-003 MUST — Confirmation and tracking:** submission returns a safe confirmation
  and capability code; code lookup exposes only the public contract.
- **FR-004 MUST — Owner area:** an authenticated citizen sees only owned tickets,
  statuses, authorized timeline and follower controls.
- **FR-005 SHOULD — Rating:** only an eligible owner can rate a resolved/closed ticket.

### PA operations

- **FR-006 MUST — Triage:** authorized staff filter, inspect and prioritize tickets.
- **FR-007 MUST — Assignment:** authorized staff assign/remove a responsible operator.
- **FR-008 MUST — State transition:** valid transitions require actor, reason and
  timestamp; invalid transitions are rejected and auditable.
- **FR-009 MUST — Collaboration:** authorized staff can comment, relate, attach and
  export according to policy.
- **FR-010 SHOULD — SLA:** supervisors can inspect age, status and workload metrics.

### Platform services

- **FR-011 MUST — Identity/RBAC:** User roles and tenant boundaries protect every
  private operation.
- **FR-012 MUST — Notifications:** event-driven notifications honor preference,
  verified destination, retry and failure recording.
- **FR-013 MUST — Media/geodata:** uploads are validated and locations normalized;
  public serialization removes private metadata.
- **FR-014 MUST — Localization/content:** tenant-owned localized pages and labels
  have explicit fallback and no raw translation keys in critical journeys.
- **FR-015 SHOULD — GDPR evidence:** consent/treatment/export/delete workflows retain
  only approved evidence and follow retention policy.
- **FR-016 COULD — Mobile parity:** mobile/native workflows cover the same core intent
  without inventing a second ticket domain model.

### Delivery and documentation

- **FR-017 MUST — Component ownership:** every module/theme has a current BMAD index,
  responsibility, dependencies, gaps and verification route.
- **FR-018 MUST — Release evidence:** code quality, tests, browser, accessibility,
  backup/restore, queues and owner sign-off are recorded per candidate release.

### Acceptance Criteria

Every MUST requirement is accepted only when its behavior is covered by a test or
browser evidence on the candidate environment, with authorization and failure paths.
The owning story must link the exact artifact and limitation.

## Non-functional requirements

## Non-Functional Requirements

- **NFR-001 Performance:** critical public pages and ticket reads target p95 < 500ms
  on the candidate environment, measured with repeatable HTTP/browser evidence.
- **NFR-002 Security:** unauthorized owner/private access returns a safe denial and
  never leaks capability codes, media metadata or internal activity.
- **NFR-003 Accessibility:** critical citizen and tracking flows meet WCAG 2.1 AA,
  keyboard, focus, semantic error and reduced-motion requirements.
- **NFR-004 Reliability:** ticket creation is transactional/idempotent; notification
  failures are retryable and observable without losing the ticket.
- **NFR-005 Maintainability:** PHPStan Modules has zero errors; changes respect module
  ownership, Actions, contracts and forward-only migrations.
- **NFR-006 Operability:** queue, mail, failed jobs, logs, backup and restore have a
  runbook and candidate-environment evidence.
- **NFR-007 Compliance:** tenant privacy content and acknowledgement semantics are
  approved by the owner before production; code agents do not infer legal consent.

## Release success metrics

## Success Metrics

| Metric | Target | Evidence |
|---|---|---|
| Critical report completion | < 2 minutes for a prepared citizen | browser timing on candidate |
| Static analysis | 0 PHPStan errors in Modules | exact command output |
| Critical authorization | 0 unauthorized disclosures | Pest + browser matrix |
| Accessibility | WCAG AA critical-flow checklist complete | keyboard/screen-reader/browser record |
| Operational readiness | G1–G7 all green or formally accepted | release evidence index |

## Explicit non-goals

## Assumptions and Dependencies

Assumptions: tenant owners provide approved content and SLA rules; Ops provides an
isolated candidate environment; provider credentials are supplied through approved
secret handling. Dependencies are Laravel/module manifests, database, object storage,
queue worker, mail/provider sandbox, browser tooling and tenant decisions.

## Out of Scope

Native rewrite, chatbot-first reporting, gamification, marketing notifications,
unsupported themes, and legal certification without owner evidence.

## Epics and User Stories

The seven epics and twelve root completion stories are mapped in `epics.md` and
`stories/`; existing module-owned stories remain the implementation detail for their
component boundaries.

## Traceability Matrix

| Requirement family | Epic | Architecture / evidence |
|---|---|---|
| FR-001–005 | E2 | Fixcity/User/Sixteen; architecture.md |
| FR-006–010 | E3 | Fixcity/Activity/Job; architecture.md |
| FR-011–015 | E4/E5 | User/Tenant/Gdpr/Notify/Media/Geo/Lang/Cms |
| FR-016–018 | E1/E6/E7 | Mobile, all themes/modules, release-plan.md |
| NFR-001–007 | E4/E5/E6/E7 | security-plan.md, devops-plan.md, test-strategy.md |
