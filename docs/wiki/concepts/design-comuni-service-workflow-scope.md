---
title: "Design Comuni workflow scope for FixCity"
type: concept
tags: [second-brain, design-comuni, citizen-flow, service-boundaries]
created: 2026-09-27
updated: 2026-09-27
qmd: "Design Comuni workflows 44 templates FixCity reporting scope SPID CIE payment privacy review confirmation"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../../bmad/design-comuni-service-workflow-audit-2026-09-27.md
  - ../../bmad/design-comuni-workflow-expected-2026-09-27.md
  - ./ticket-design-comuni-comparison.md
---

# Reusable decision

Design Comuni v2.4.0 publishes reusable patterns for six municipal transaction
families in 44 workflow pages. FixCity is currently a civic-reporting product,
not a general municipal transaction platform. Map the actual citizen flow as
account access → tenant privacy step → report data → review → receipt and
follow-up. The public service sheet should expose those phases before the user
starts.

Do not infer support for SPID/CIE, pagoPA, F24, permits, benefits or appointments
from the reference templates. Add them to public navigation only after the
owner module has a real workflow, tenant-approved content and integration
evidence. Privacy policy wording and consent semantics require the tenant's
privacy owner. See the BMAD audit and correction plan for current evidence.

## Related knowledge

- [[../../bmad/design-comuni-service-workflow-audit-2026-09-27]]
- [[../../bmad/design-comuni-workflow-expected-2026-09-27]]
- [[../../../../Themes/Sixteen/docs/bmad/design-comuni-workflow-correction-plan-2026-09-27]]
- [[ticket-design-comuni-comparison]]
