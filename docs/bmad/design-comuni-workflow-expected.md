---
title: "Design Comuni service workflows — FixCity expected journey"
type: bmad-ux-contract
status: current-baseline
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, design-comuni, workflows, citizen, accessibility]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - design-comuni-service-workflow-audit-2026-09-27.md
  - ../../../Themes/Sixteen/docs/bmad/design-comuni-workflow-correction-plan-2026-09-27.md
---

# Expected citizen journey

## Official model

The Design Comuni v2.4.0 site index lists 35 functional site templates. Its
separate service-workflow index lists 44 linked templates: two shared steps
(digital-identity access and privacy information) and six service families
(ranking applications, permits, economic benefits, fines via pagoPA, IMU/F24,
and paid services). The families reuse a service sheet, applicant data,
service-specific data, review, confirmation and personal-area outcome; payment
and extra completion steps appear only where relevant. This is a set of
municipal service patterns, not a requirement to claim that every municipality
or FixCity supports every transaction.

## FixCity scope

FixCity's verified service is reporting civic issues. Its citizen path is:

1. Read the public report-service sheet and understand access requirements.
2. Sign in or create an account.
3. Read the tenant's published privacy information and complete the configured
   privacy step.
4. Enter the issue, location, description and optional evidence.
5. Review the data, return to edit if needed, then submit.
6. Receive a reference/tracking code and follow updates from the personal area
   or the public tracking flow.
7. Browse public reports and the map without entering a report flow.

The service sheet must preview these real steps before its CTA. In particular,
it must not compress privacy, review, confirmation and follow-up into one vague
"send" action. Login must remain disclosed before protected data entry.

## Capability boundary

Do not advertise SPID/CIE, pagoPA, F24, appointment booking, permits, ranking
applications or benefits as operational FixCity capabilities without an
implemented integration and owner-module evidence. Tenant privacy text and
contact details require tenant approval. A template in the public reference is
not evidence that FixCity or a particular municipality offers that service.
