---
title: "FixCity release readiness plan"
type: release-plan
status: no-go-until-gates
updated: 2026-10-07
tags: [bmad, release, go-no-go, fixcity]
qmd: "FixCity release readiness G1 G7 go no go candidate evidence"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Go/no-go

Current decision: **NO-GO** until the candidate proves G1–G7. G1 static analysis is
green; G2/G3 candidate evidence, G4 privacy owner decision, G5 recovery, G6
operations and G7 sign-off remain blockers. The detailed register is
`bmad-output/go-live-gap-register.md` and the coordinating stories are Epic 9.

| Gate | Required evidence | Owner |
|---|---|---|
| G1 Code | PHPStan/Pest/Pint/view checks | Maintainer/QA |
| G2 Product | citizen/PA vertical slice and denial matrix | QA/PA |
| G3 UX | responsive + WCAG critical journeys | UX/QA |
| G4 Privacy | approved copy, semantics, retention and contacts | Tenant owner/legal |
| G5 Recovery | candidate backup and isolated restore | Ops/DBA |
| G6 Operations | queue, SMTP, failed jobs, alerts, runbook | Ops |
| G7 Sign-off | evidence index and named go/no-go approval | Product/PA |

No historical claim substitutes for candidate evidence. Any reopened gate updates
`completion-gap-analysis.md` and the owning story.
