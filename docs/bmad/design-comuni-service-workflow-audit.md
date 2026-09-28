---
title: "Design Comuni v2.4.0 — service workflow coverage audit"
type: bmad-gap-analysis
status: audited-with-follow-up
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, design-comuni, workflow-audit, fixcity]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
sources:
  - https://italia.github.io/design-comuni-pagine-statiche/index.html
  - https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html
related:
  - design-comuni-workflow-expected-2026-09-27.md
  - stories/STORY-517-design-comuni-coverage.md
  - ../wiki/concepts/design-comuni-service-workflow-scope.md
  - ../../../Themes/Sixteen/docs/bmad/design-comuni-workflow-correction-plan-2026-09-27.md
---

# Audit method and reference findings

The root catalogue identifies release v2.4.0 and separates the municipal site
templates from the service workflow templates. The service index was fetched
directly and each of its 44 linked workflow pages returned HTTP 200. The linked
set groups into two shared pages and six transaction families; these pages are
reusable examples and do not imply that all integrations are available here.

| Reference group | Workflow emphasis | FixCity applicability |
| --- | --- | --- |
| Shared access | Digital identity before protected service data | Local account login/registration exists; SPID/CIE integration is not evidenced |
| Shared privacy | Explain processing before collecting personal data | Tenant policy and acknowledgement step exist; legal semantics/tenant approval remain gated |
| Ranking application | Applicant data, application details, review, receipt and later completion | Not an implemented FixCity domain |
| Permits/authorizations | Applicant data, permit-specific data, review, receipt and optional payment | Not an implemented FixCity domain |
| Economic benefits | Applicant and benefit data, review, receipt and personal-area follow-up | Not an implemented FixCity domain |
| Fine payment | Multi-phase case data, review, payment and receipt | pagoPA is not evidenced in FixCity |
| IMU/F24 | Tax data, payment preferences, F24 preview and receipt | F24 generation/payment is not evidenced in FixCity |
| Paid services | Service data, review, payment and receipt | No payment gateway is evidenced in FixCity |

The supported FixCity reporting flow is narrower and has its own seven
templates: public service sheet, privacy step, report data, review, confirmation,
personal-area record and public list/map. The route/template coverage is present,
but this audit does not certify legal approval, deployment readiness, or a
successful end-to-end run against a production-equivalent database.

# Runtime comparison

Before STORY-525, the report-service page stated that a FixCity account was
required and linked to both create and public reports, but its three-step
summary omitted the privacy step and conflated confirmation/tracking with
submission. The correction now presents five phases in all four locales:
account access; tenant privacy step; report details and location; review and
submission; receipt and follow-up. Existing login redirect and canonical Folio
routes are preserved. SPID/CIE and payment remain unclaimed.

# Crawl and verification evidence — 2026-09-27

The root index exposes 35 unique functional `/sito/` templates; direct HTTP
checks returned 200 for all 35. Its eight families contain 9 general, 2
administration, 2 news, 3 services, 2 events, 8 appointment, 2 assistance and
7 disservice-report templates. The separate workflow index exposes 44 unique
pages; direct HTTP checks returned 200 for all 44. The eight appointment pages
include separate guest and authenticated applicant screens. Total: 35 site
templates + 44 workflow templates = 79. The two index documents are not
included in the functional-template count.

Local HTTP checks for `/it|en|de|es/services/report-issue` returned 200 and
parsed exactly five non-empty steps from each rendered page. Blade view cache,
PHP syntax checks for all four locale files, `git diff --check`, and
`bash bashscripts/quality-gates/verify-llm-wiki.sh` passed. Chromium could not
launch for Playwright because `libatk-1.0.so.0` is missing. The responsive and
browser regression remains open, not passed.

# Evidence limits

The reference pages are static templates. Their sample offices, deadlines,
fees, payment methods and legal text are not tenant data. FixCity must render
only approved tenant content and implemented capabilities. Browser automation
can check rendered copy, destinations, responsiveness and errors; legal review,
screen-reader usability and staging integrations require separate evidence.
