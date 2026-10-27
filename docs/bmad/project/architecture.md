---
title: "FixCity system architecture"
type: architecture
status: draft-for-review
updated: 2026-10-07
tags: [bmad, architecture, laravel, modular-monolith, fixcity]
qmd: "FixCity Laravel 13 modular monolith architecture modules themes Actions Folio Filament"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# FixCity architecture

## System Overview

FixCity is a Laravel 13/PHP 8.4 modular monolith for municipal civic reporting.
Citizens use localized Folio/Volt pages and PA staff use Filament; Actions and
module-owned models enforce the domain rules. The primary aggregate is Ticket.

## Pattern

**Modular monolith.** Laravel 13/PHP 8.4 runs one deployable application whose 19
modules own domains and whose four themes own presentation variants. This preserves
transactional ticket workflows and low operational complexity while allowing module
repositories and dependency manifests to evolve independently.

Rejected: microservices (no measured isolation/scale requirement), a second ticket
domain for mobile (duplication), and a controller/service layer (conflicts with the
project's Folio + Actions architecture).

## Runtime topology

`Sixteen theme/Folio pages → Volt/Filament UI → owning Action → Xot contracts/base
models → Eloquent/database`, with cross-cutting User/Tenant/Gdpr/Activity/Notify/
Media/Geo/Comment/Rating modules. Filament admin uses XotBase resources. API-like
surfaces are Folio pages under module resources, not MVC controllers.

## Locked ADRs

### ADR-001 — Domain ownership

Actions, models, migrations and tests live in the owning module. Xot receives only
proven shared contracts. This prevents semantic drift and makes parallel work safe.

### ADR-002 — UI boundary

Sixteen owns citizen presentation; Folio/Volt/Filament widgets are the entry points.
No new HTTP controllers or Services layer. Business decisions stay in Actions.

### ADR-002a — HTTP resource style

**Decision:** use localized REST-style resource URLs and server-rendered HTML
representations for front office pages; JSON/GeoJSON is limited to explicit widget
projections. **Why:** this matches Folio and preserves progressive enhancement.
**Trade-off:** the public surface is less suitable for generic API clients than a
separate versioned API, which is intentionally out of scope.

### ADR-003 — Authorization and privacy

All private operations use UserContract/policies and tenant context. `owner_id` is
the citizen ownership authority; audit fields never grant visibility. Public payloads
are explicit allowlists and capability codes are owner-scoped.

### ADR-004 — State and audit

TicketStatusEnum is the state vocabulary. Every state change validates transition,
records actor/reason/time in TicketActivity and emits notification work after commit.

### ADR-005 — Data and migrations

Eloquent modular persistence is the source of truth; migrations are forward-only,
use module-owned models/contracts and never rewrite an applied migration.

### ADR-006 — Quality gate

PHPStan is run against all Modules at max configuration, followed by relevant Pest,
Pint, browser/view checks and wiki gate. `phpstan.neon` is immutable to agents.

### Naming and conventions

Modules own domain code and Actions use verb-noun names; Sixteen owns public Blade
components and `pub_theme::` translations. URLs are lowercase localized slugs,
PHP uses strict types, and public projections use explicit allowlists.

## Module boundaries

See [`component-catalog.md`](component-catalog.md). Fixcity is the primary domain;
Xot, User, Tenant and Sixteen are shared/high-conflict boundaries. All other modules
must expose stable contracts/actions rather than reaching into another module's
concrete internals.

## Data model

The Ticket aggregate references owner/responsible users, category, coordinates,
priority/type/status, media, comments, activities, relations, hours, subscribers
and ratings. Tenant and localization context scope content and policy. Activity and
notification records provide operational evidence. No new entity is planned without
an owning module, migration strategy, authorization rule and retention decision.

## Integration contracts

- Public ticket list/detail/tracking: localized Folio pages with allowlisted payloads.
- PA operations: Filament Resource/Actions and policy checks.
- Notifications: queued events/jobs with after-commit dispatch and failed-job record.
- Storage: Media module with validation, conversion and privacy metadata policy.
- Maps/geocoding: Geo actions with normalization and failure fallback.
- Tenant content: Cms/Tenant/Lang, with explicit locale fallback.

## Coverage

FR-001–005 map to Sixteen/Fixcity/User; FR-006–010 to Fixcity/User/Activity/Job;
FR-011–015 to User/Tenant/Notify/Media/Geo/Gdpr/Lang/Cms; FR-016 to Mobile;
FR-017–018 to every module/theme plus Xot quality infrastructure. NFR-001–007 are
cross-cutting and are gated in `security-plan.md`, `devops-plan.md` and
`release-plan.md`.

## FR / NFR Coverage Matrix

| IDs | Covered components | Evidence |
|---|---|---|
| FR-001 FR-002 FR-003 FR-004 FR-005 | Fixcity, User, Sixteen, Rating | E2 / EXPERIENCE.md |
| FR-006 FR-007 FR-008 FR-009 FR-010 | Fixcity, Activity, Job, User | E3 |
| FR-011 FR-012 FR-013 FR-014 FR-015 | User, Notify, Media, Geo, Lang, Cms, Gdpr, Tenant | E4/E5 |
| FR-016 FR-017 FR-018 | Mobile, all modules/themes, Xot | E1/E6/E7 |
| NFR-001 NFR-002 NFR-003 | Sixteen, Fixcity, User, Geo, Media | security-plan.md / DESIGN.md |
| NFR-004 NFR-005 NFR-006 | Job, Notify, Xot, all modules | devops-plan.md / test-strategy.md |
| NFR-007 | Gdpr, Tenant, Cms, Lang, Fixcity | security-plan.md |

Every identifier is repeated here so the readiness gate can detect coverage
mechanically; detailed rationale remains in the sections above.

## Assumptions and Constraints

The platform assumes approved tenant content, isolated candidate infrastructure and
provider credentials supplied through Ops. Constraints are Laravel 13/PHP 8.4,
forward-only migrations, no controller/service layer, WCAG AA, immutable PHPStan
configuration and module/theme ownership.

## Component Design

The complete component list, including all 19 modules and 4 themes, is locked in
`component-catalog.md`. Each component exposes responsibilities through Actions,
contracts, events, resources or Folio pages and consumes only declared/shared
contracts.

## Data Model

Ticket, User/Profile, Category, TicketActivity, TicketSubscriber, TicketRelation,
TicketHour, Comment, Media, Rating and Tenant are the principal entities; Activity,
Notify and Job provide audit/delivery/operation records.

## API Specifications

Public API-like surfaces are localized Folio pages under module resources with
allowlisted response data: public tickets GeoJSON, public detail and tracking by
code. Admin mutations are Filament Actions. Authentication and policy errors are
localized safe responses; no new controller endpoints are planned.

## Technology Stack

| Layer | Choice | Rationale |
|---|---|---|
| Runtime | PHP 8.4 / Laravel 13 | current application manifest |
| Modules | Nwidart modular monolith | module ownership and transactional workflow |
| UI | Folio/Volt/Filament 5 + Sixteen | existing public/admin boundaries |
| Persistence | Eloquent + module migrations | existing domain model and forward-only policy |
| Quality | PHPStan, Pest, Pint, browser tooling | measurable release gates |

## Trade-off Analysis

The modular monolith keeps ticket, activity and notification transactions simple at
the cost of stricter ownership discipline. Shared Xot abstractions reduce duplication
but are high-conflict, so changes require cross-module evidence. Local SQLite speeds
feedback but cannot replace candidate MySQL/staging evidence.
