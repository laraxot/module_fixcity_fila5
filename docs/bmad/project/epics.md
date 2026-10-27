---
title: "FixCity epics and story map"
type: epics
status: ready-for-story-planning
updated: 2026-10-07
tags: [bmad, epics, fixcity, completion]
qmd: "FixCity epics completion backlog modules themes release"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Epic map

The root backlog contains only cross-cutting completion work. Domain-specific work
continues in each module's owner repository and is linked here by scope.

## Epic 1 — Product and component truth
Goal: every module/theme has one current owner, purpose, dependency map and gap state.
Stories: 1.1 component-catalog; 1.2 module-theme-docs; 1.3 story-tracker-normalization;
1.4 view-cache-component-integrity.

## Epic 2 — Citizen vertical slice
Goal: guest discovery through authenticated report, confirmation and tracking is proven.
Stories: 2.1 candidate-citizen-flow; 2.2 media-location-errors; 2.3 privacy-acknowledgement;
module-owned Design Comuni stories DCP-32 (privacy), DCP-33 (dati + mappa), DCP-34
(riepilogo + mappa), DCP-35 (conferma).
Lo scope trasversale delle pagine reali è DCP-32S e non duplica il numero dello
step privacy.

## Epic 3 — PA operations and audit
Goal: authorized PA roles complete triage, assignment, transition, collaboration and closure.
Stories: 3.1 role-matrix; 3.2 SLA-work-order-decision; 3.3 audit-timeline-evidence.

## Epic 4 — Notifications and resilience
Goal: event-driven notification delivery is reliable, observable and recoverable.
Stories: 4.1 worker-provider-drill; 4.2 retry-failure-alerting.

## Epic 5 — Security, privacy and data lifecycle
Goal: tenant isolation, media privacy, GDPR evidence and backup/restore are accepted.
Stories: 5.1 media-retention; 5.2 backup-restore; 5.3 registration-consent-evidence;
5.4 marketing-revocation; 5.5 subject-rights-account-lifecycle; 5.6
verified-registration-multilingual.

## Epic 6 — UX/accessibility, themes and cookie compliance
Goal: Sixteen critical flows meet WCAG/Design Comuni contract, alternate themes
are classified, and the public multilingual cookie-consent boundary is backed by
Gdpr evidence and owner approval.
Stories: 6.1 accessibility-candidate; 6.2 theme-support-decision; 6.3
cookiebar-gdpr-bootstrap-italia; 6.4 cookie-policy-localized-inventory; 6.5
consent-gated-optional-processing; 6.6 gdpr-revocation-and-audit-boundary; 6.7
cookiebar-accessibility-and-localization-qa; 6.8 cookie-compliance-owner-approval;
6.9 cookie-consent-release-regression-gate; 6.10 localized-legal-route-inventory;
6.11 no-bootstrap-runtime-boundary; 6.12 terms-disclaimer-contract; 6.13
legal-identity-content-checklist; 6.14 legal-acceptance-version-evidence; 6.15
legal-publication-gate; 6.16 terms-disclaimer-publication-surface; 6.17
legal-link-runtime-normalization.

## Epic 7 — Release closure
Goal: all gates have evidence and named go/no-go approval.
Stories: 7.1 readiness-evidence-index; 7.2 release-sign-off; 7.3 code-quality-static-analysis-gate.

## Epic 8 — Designers Italia service-design alignment
Goal: treat the official Designers Italia guidance as an end-to-end service-design
baseline covering outcomes, IA, content, accessibility, research, privacy and
continuous improvement, without introducing Bootstrap runtime.
Stories: 8.1 design-guideline-baseline; 8.2 service-design-outcomes; 8.3
information-architecture; 8.4 content-language-seo; 8.5 inclusive-accessibility-design;
8.6 user-research-usability; 8.7 privacy-by-design-alignment; 8.8
continuous-improvement-release-evidence; 8.9 project-governance-and-responsibilities;
8.10 service-model-and-ecosystem; 8.11 content-inventory-localization-and-rights;
8.12 interaction-prototyping-and-validation; 8.13 design-system-and-component-contract;
8.14 reliability-transparency-security; 8.15 platforms-and-authentication;
8.16 licenses-and-open-content; 8.17 monitoring-feedback-and-satisfaction;
8.18 normative-requirements-and-procurement-gate.

## Epic 9 — Go-live closure
Goal: convert the backlog into a production-ready candidate with operational,
privacy, security, accessibility and signed release evidence.
Stories: 9.1 candidate-environment-and-rollback; 9.2 operations-backup-and-recovery;
9.3 legal-privacy-owner-approval; 9.4 e2e-accessibility-candidate;
9.5 security-data-lifecycle-candidate; 9.6 go-live-sign-off-and-rollback.

## Epic 10 — Agentic engineering governance
Goal: use BMAD, Second Brain and optional development/security/design plugins as
controlled accelerators without exposing data or weakening release gates.
Stories: 10.1 ai-tooling-allowlist-and-data-boundary; 10.2
bmad-superpowers-development-loop; 10.3 security-plugin-release-gate; 10.4
visual-ai-review-boundary.

## Dependencies

E1 is foundational; E2–E6 can run in parallel where owned scopes do not overlap;
E7 depends on all; E8 feeds E7. Security/Gdpr/Tenant/User/Notify/Xot and Sixteen are shared
boundaries and must be serialized when touched. Within the cookie/legal stream, 6.3
→ 6.4/6.5 → 6.6/6.7 → 6.8 → 6.9; 6.10 depends on owner-approved legal
destinations and blocks 6.7/6.9; 6.11 depends on 6.2/6.3 and blocks 6.7/6.9;
6.12 → 6.13/6.14 → 6.15 → 6.16, while 6.10 → 6.17; 6.16/6.17 block
6.9/7.1/7.2.
6.9 remains the release evidence gate.
Within E8, 8.1 is the baseline; 8.2/8.3/8.9/8.10 can consume it, 8.4/8.5/8.6/8.11/8.12/8.13
consume the relevant design inputs, 8.7/8.14/8.15/8.16/8.17 cover specialist gates, and
8.18 consolidates 4.1–4.8. Story 8.18 blocks 7.1; 7.1 must precede 7.2.
E9 is the go-live execution/evidence layer: 9.1 precedes 9.2/9.4/9.5, 9.3 covers
owner-blocked legal input, and 9.6 consolidates them. 9.6 must precede 7.2 and launch.
E10 is optional to runtime but mandatory for governed agent-assisted delivery:
10.1 → 10.2 → 10.3/10.4. It cannot override E9 or legal/security approvals.

## Design Comuni story coordination

The authoritative Sixteen story index is
`laravel/Themes/Sixteen/docs/bmad/stories/00-INDEX.md`. DCP-32→DCP-35 are a
dependency chain for the Filament ticket wizard; independent page stories may run
in parallel only when their Owned File/Module Scope is disjoint.
