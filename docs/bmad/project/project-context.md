# Project Context — FixCity

> The project **constitution**. This document is loaded by every BMAD planning skill
> so they all share the same ground truth. Keep it tight, current, and authoritative.
> When a major decision changes scope, update this file and append the change to
> `decision-log.md`.

- **Track:** enterprise  _(quick-flow | bmad-method | enterprise)_
- **Created:** 2026-10-07T13:11:17Z

---

## Project Goal

FixCity is a multi-municipality civic-reporting platform: citizens submit and track
geolocated urban issues, while public-administration staff triage, assign, resolve
and audit them. Done means the complete citizen-to-PA vertical slice is proven on a
release candidate, with privacy, accessibility, notifications, backup/restore and
operational monitoring accepted by the tenant owner.

## Primary Users

- **Citizen:** quickly reports an issue, attaches evidence, sees only authorized
  details, follows status and rates a resolved issue.
- **PA operator:** triages, assigns, updates and comments on tickets.
- **Supervisor:** monitors SLA, workload, escalation and audit evidence.
- **Platform administrator:** manages tenant configuration, users, roles and release
  readiness.

## Scope

Public discovery and map/list; authenticated report wizard; ticket lifecycle and
ownership; PA Filament operations; timeline/comments/followers; media and geodata;
notifications; ratings; tenant/localization/privacy; accessibility and release
operations; documentation and quality automation.

## Core Constraints

- Laravel 13, PHP 8.4, Nwidart modular monolith, Filament 5 and theme Sixteen.
- Actions hold business logic; Folio/Volt/Filament handle UI; no HTTP Controllers or
  Services layer for new work.
- Module providers and dependencies are declared in manifests; modules are autonomous
  repositories under `laravel/Modules`.
- Forward-only Git; never rewrite historical migrations or change `phpstan.neon`.
- Target WCAG 2.1 AA and Design Comuni familiarity; no color-only or desktop-only
  workflows.
- Tenant legal content and infrastructure credentials require owner/ops approval.

## Non-Goals

Native mobile rewrite, chatbot-first reporting, gamification, generic SaaS redesign,
Bootstrap runtime replacement, speculative microservices, and legal approval by code
agents. Open311 outbound, advanced SLA/work orders, deduplication and PWA are planned
extensions, not silently assumed complete.

## Key Stakeholders / Roles

Product/tenant owner approves scope and legal copy; PA representative approves the
operational workflow; DBA/Ops provides isolated staging and backup evidence; QA signs
functional/browser/accessibility gates; maintainers implement through module owners.

## Glossary

- **Ticket / segnalazione:** a citizen-reported municipal issue.
- **Owner:** citizen authorized to view private ticket details and capability code.
- **Responsible:** PA operator assigned to work the ticket.
- **Timeline:** public/internal activity events with actor, reason and timestamp.
- **Capability code:** non-guessable public tracking credential with restricted output.
- **Tenant:** municipality configuration and localized content boundary.
- **Gate:** evidence-based release criterion; an HTTP 200 or local test alone is not
  release evidence.

---

## Decision Thread

Running decisions live in [`decision-log.md`](./decision-log.md). The first entry is
the track choice from initialization. Consult it before making decisions that might
contradict earlier ones.

## Planning Status (count-based)

- **Track:** enterprise
- **Stories defined:** 12 cross-cutting completion stories in the root plan; existing
  module-owned stories remain linked as implementation evidence.
- **Stories remaining:** 12 at planning baseline; statuses are tracked in
  `sprint-status.yaml` without points or velocity.

_This document plans the work. Implementation is handed to external dev tools via
ready-for-dev story files; the planning plugin never writes or tests application code._
