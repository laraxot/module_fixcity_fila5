---
title: "FixCity completion gap analysis"
type: bmad-gap-analysis
status: active
updated: 2026-10-07
tags: [bmad, gap, readiness, release, second-brain]
qmd: "FixCity project completion gaps staging privacy queue backup accessibility all modules themes"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# What is missing for completion?

“Complete” means a release candidate has evidence, not that source files exist.

| ID | Gap | Scope | State | Closure evidence |
|---|---|---|---|---|
| G-01 | Owner decision and approved copy for privacy acknowledgement/retention | Fixcity/Gdpr/Tenant/Cms/Lang/Sixteen | blocked | signed decision + localized candidate smoke |
| G-02 | Full candidate environment with isolated MySQL and real tenant config | all modules | blocked | reproducible deploy and environment manifest |
| G-03 | Queue worker, SMTP sandbox, failed jobs, retry and alerts | Notify/Job | open | delivery matrix, replay runbook, alert evidence |
| G-04 | Backup, restore, retention and encryption on candidate | Job/Tenant/database | open | dated backup hash + isolated restore |
| G-05 | End-to-end citizen A/B and PA role matrix including denial paths | User/Fixcity/Activity/Notify | partial | Pest + browser matrix on candidate |
| G-06 | Full wizard upload/error/focus/screen-reader verification | Sixteen/Fixcity/Media/Geo | partial | WCAG checklist and browser evidence |
| G-07 | SLA/work-order semantics approved by municipality | Fixcity/Job/Tenant | open | product decision + operational workflow |
| G-08 | Open311 outbound/API scope decision | Fixcity/Geo/Job | deferred | owner decision: build, defer or reject |
| G-09 | Deduplication/merge semantics and audit | Fixcity/Activity | deferred | approved merge rules and tests |
| G-10 | Media privacy scrub/retention policy | Media/Gdpr/Fixcity | open | metadata fixture audit and retention job evidence |
| G-11 | All modules/themes have canonical BMAD index and no ambiguous docs | every module/theme | partial | component catalog + per-owner indexes |
| G-12 | Legacy merge markers/frontmatter and duplicate README debt | docs/wiki/module/theme docs | open | quality gate green with reviewed moves |
| G-13 | Alternate theme support classification | Barthelemy/Meetup/TwentyOne | open | supported/archive decision and smoke scope |
| G-14 | Complete release sign-off | product/PA/QA/Ops | blocked | signed G1–G7 go/no-go record |
| G-15 | Terms/disclaimer, legal identity, versioned acceptance and legal publication gate | Fixcity/User/Gdpr/Tenant/Cms/Sixteen | open/owner-blocked | approved localized documents, version evidence and explicit candidate gate |
| G-16 | No dedicated localized terms/disclaimer publication surface | Fixcity/Cms/Sixteen | open/owner-blocked | owner-provided document exposed through the legal-document contract |
| G-17 | Legacy/runtime legal links still use fixed paths or dead anchors | Sixteen/Fixcity | open | canonical localized href inventory and regression evidence |

## Already evidenced

PHPStan `analyse Modules` is currently green; the public discovery/create/confirm/
tracking slice and several privacy/ownership/browser checks exist locally. Those facts
reduce implementation risk but do not close candidate-environment gaps.

## Order of closure

1. Resolve owner/legal decisions (G-01, G-07, G-08, G-09).
2. Provision candidate and safe data/tenant configuration (G-02).
3. Implement and verify operational/privacy gaps (G-03, G-04, G-05, G-06, G-10).
4. Normalize all component documentation and theme support decisions (G-11–13).
5. Run readiness and collect release sign-off (G-14).
