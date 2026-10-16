---
title: "Designers Italia baseline — FixCity"
type: baseline
status: current
versione-corrente: "current"
accessed: 2026-10-15
owner: release
tags: [designers-italia, design-system, accessibility, release-evidence]
---

# Designers Italia baseline

This record pins the normative reference used by FixCity release evidence. It
does not claim legal certification or visual parity by itself.

## Current normative sources

- **Manuale operativo Design dei servizi pubblici:**  
  https://docs.italia.it/italia/designers-italia/manuale-operativo-design-docs/
- **Linee guida di design — requisiti, versione corrente:**  
  https://docs.italia.it/italia/design/lg-design-servizi-web/it/versione-corrente/requisiti.html

The release candidate uses the current requirements and the operational manual
accessed on **2026-10-15**. The historical Docs Italia introduction studied
during research is **superseded** and is retained only as background; it is not
the current normative baseline.

## Runtime boundary

The current runtime stack is:

- TailwindCSS
- Alpine.js
- Lit
- DaisyUI
- Flowbite

Bootstrap Italia is a semantic and visual reference only. Bootstrap Italia
runtime assets and Bootstrap classes are outside the FixCity runtime boundary.

## Evidence links

- UX contract: [DESIGN.md](DESIGN.md)
- Accessibility and interaction evidence: [EXPERIENCE.md](EXPERIENCE.md)
- Legal and privacy gates: [research-report.md](research-report.md) and
  [go-live-gap-register.md](go-live-gap-register.md)
- Release planning: [8.18 normative requirements and procurement gate](stories/8.18.normative-requirements-and-procurement-gate.story.md)
- Readiness closure: [7.1 readiness evidence index](stories/7.1.readiness-evidence-index.story.md)

## Limits

This baseline identifies the sources and implementation boundary. It does not
replace accessibility testing, owner/DPO approval, procurement evidence,
municipality decisions, or candidate-environment verification.
