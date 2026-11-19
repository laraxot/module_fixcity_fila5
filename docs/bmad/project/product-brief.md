---
title: "FixCity product brief"
type: product-brief
status: approved-baseline
updated: 2026-10-07
tags: [bmad, product, civic-tech, fixcity]
qmd: "FixCity product brief civic reporting municipality citizen transparency"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# FixCity

## Problem

Municipal issues arrive through phone, email, counters and spreadsheets. Citizens
cannot reliably know whether a report was received or what happens next; PA staff
lack structured geography, ownership, SLA evidence and a durable audit trail.

## Users

Citizens need a fast, legible mobile workflow. Operators need a focused queue and
safe state transitions. Supervisors need workload/SLA evidence. Administrators need
tenant, role, content and integration controls.

## Value proposition

FixCity makes municipal reporting observable end-to-end: every authenticated report
has a clear owner, current state, responsible PA actor, public/private boundary and
recoverable history. It is reusable across municipalities through a modular, tenant-
aware Laraxot platform.

## Core capabilities

- Public map/list and localized service discovery.
- Authenticated report wizard with location and media.
- Ticket lifecycle, assignment, priority, timeline, comments and followers.
- Notifications, privacy/content management, ratings and operational analytics.
- Filament PA console and accessible Sixteen front office.

## Success

The candidate release proves the vertical slice for guest, citizen A/B, operator,
supervisor and administrator; no unauthorized disclosure occurs; citizens can see
real progress; operators can complete the workflow; all release gates have dated
evidence and named sign-off. Product targets are report completion under two minutes,
WCAG 2.1 AA for critical flows, PHPStan zero errors and measurable notification/SLA
delivery.

## Risks and assumptions

The product assumes a municipality supplies approved privacy copy, categories,
contacts and operational SLA rules. Biggest risks are legal ambiguity around the
privacy checkbox, unverified production-like infrastructure, notification delivery,
and drift between legacy documentation and current code.

## Non-goals

No chatbot-first interaction, gamification, native rewrite, speculative microservices,
or claim of legal compliance without owner/legal evidence.
