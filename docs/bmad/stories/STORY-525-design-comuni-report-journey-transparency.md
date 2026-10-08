---
title: "STORY-525 — Make the report service journey explicit"
type: story
status: in-progress
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
priority: Must
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
references:
  - https://italia.github.io/design-comuni-pagine-statiche/index.html
  - https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html
  - https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-dettaglio.html
related:
  - ../design-comuni-service-workflow-audit-2026-09-27.md
  - ../design-comuni-workflow-expected-2026-09-27.md
  - ../wiki/concepts/design-comuni-service-workflow-scope.md
  - ../../../../Themes/Sixteen/docs/bmad/design-comuni-workflow-correction-plan-2026-09-27.md
---

# User story

As a resident deciding whether to report an issue, I want the public service
sheet to describe the actual steps, so I know when login is required, what
privacy step comes first, what information to prepare, and how I will follow up.

## Acceptance criteria

- [x] The service sheet describes account access, privacy, report data, review,
      confirmation and follow-up in the correct runtime order.
- [x] Italian, English, German and Spanish show equivalent localized copy;
      translation-owner review remains open.
- [x] The page does not claim SPID/CIE, payment, or services without evidence.
- [x] The localized create CTA keeps its canonical Folio destination and guest
      login redirect; the all-reports destination remains available.
- [ ] Playwright verifies the five journey phases in all four locales, along
      with existing catalogue navigation and responsive checks.
- [ ] Pest, PHPStan and the repository wiki quality gate pass for changed code.
- [ ] Tenant privacy wording and legal consent semantics remain subject to
      privacy-owner approval; this story must not decide them.

## Implementation boundary

Presentation belongs to the Sixteen theme. The service domain remains in the
Fixcity module. Keep Folio + Volt/Blade + Actions; do not add HTTP controllers,
services, synthetic service families, or invented municipal contacts.

## Verification record — 2026-09-27

- Direct HTTP crawl returned 200 for all 35 functional site templates and all
  44 service-workflow templates in the official v2.4.0 catalogues.
- Local checks returned HTTP 200 for the report-service page in IT/EN/DE/ES and
  parsed five non-empty localized phases from every response.
- Blade view cache, PHP syntax checks on four locale files, repository wiki
  quality gate and `git diff --check` passed.
- The Playwright spec now asserts five steps for every locale. Execution was
  attempted but Chromium could not start because `libatk-1.0.so.0` is missing;
  responsive/browser acceptance remains open. Native-language review and
  tenant privacy-owner approval remain open too.
- No domain PHP changed. Modules PHPStan baseline remains zero errors; this
  presentation-only update was not included in the `analyse Modules` target.
