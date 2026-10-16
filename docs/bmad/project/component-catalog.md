---
title: "FixCity component catalog — all modules and themes"
type: bmad-component-catalog
status: current-baseline
updated: 2026-10-07
tags: [bmad, modules, themes, ownership, architecture]
qmd: "FixCity all modules themes component ownership responsibilities"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Complete component inventory

This is the scope boundary for BMAD. Every module and every theme is represented;
absence from a feature epic means “not part of that feature”, never “forgotten”.

## Modules

| Module | Product responsibility | BMAD owner / completion focus |
|---|---|---|
| AI | AI threads, proposals, tool logs and assisted content | provider safety, privacy, fallback and audit |
| Activity | audit/event history and operational traceability | immutable events, retention and viewer |
| Blog | articles, categories, tags and feeds | content lifecycle and SEO boundaries |
| Cms | pages, menus, blocks and Folio content | tenant content, publishing and cache |
| Comment | polymorphic comments, reactions and subscriptions | authorization, moderation and notifications |
| Fixcity | civic tickets, categories, status, assignment, SLA, tracking, ratings | primary vertical slice and release gates |
| Gdpr | consent, treatments and GDPR events | lawful basis, retention and export/delete evidence |
| Geo | addresses, coordinates, places and geocoding | safe location normalization and map privacy |
| Job | queued jobs, imports, exports and schedules | retry, idempotency, observability and failure recovery |
| Lang | translations and localized content | locale fallback and missing-key prevention |
| Media | uploads, conversions and temporary media | validation, privacy, retention and storage recovery |
| Mobile | mobile/native workflows and client-facing actions | mobile parity, offline/error behavior and release packaging |
| Notify | email, SMS, WhatsApp, Telegram and FCM | preference, provider delivery, retry and monitoring |
| Rating | generic polymorphic ratings/likes | owner-only eligibility, abuse controls and aggregates |
| Seo | metadata, social cards and discoverability actions | canonical URLs, sitemap and tenant-safe metadata |
| Tenant | municipality/domain/configuration boundary | isolation, tenant resolution and configuration safety |
| UI | shared UI vocabulary, options and reusable presentation data | accessibility and no duplicate component vocabulary |
| User | authentication, profiles, roles, permissions, teams and OAuth | identity, authorization and account recovery |
| Xot | shared Laraxot foundation, contracts, base models/actions and Filament | compatibility contract; changes require cross-module review |

## Themes

| Theme | Responsibility | BMAD owner / completion focus |
|---|---|---|
| Barthelemy | legacy/alternate presentation surface | classify supported routes or explicitly archive |
| Meetup | alternate/demo presentation surface | classify supported routes or explicitly archive |
| Sixteen | active FixCity public UI and Design Comuni presentation | responsive, accessible citizen journeys and route ownership |
| TwentyOne | alternate presentation surface | classify supported routes or explicitly archive |

## Cross-cutting ownership rules

- Domain behavior belongs to the owning module; shared behavior belongs in Xot only
  when at least two modules genuinely need the same contract.
- Sixteen owns presentation and route composition, not ticket business rules.
- Tenant, User, Gdpr, Activity and Notify changes are high-conflict and serialize
  against stories touching them.
- Every component must have a current README/index, architecture note, gap status,
  owner story and verification evidence before release closure.

## Documentation gaps found

AI/Cms/Gdpr/Seo have thin BMAD entrypoints; Job lacks a complete BMAD index; several
modules/themes contain duplicate case-sensitive README files; module docs have
historical merge markers/frontmatter debt. These are documentation work items, not
permission to delete history or silently choose between contradictory claims.
