---
title: "Readiness — footer sponsorship"
type: readiness-report
status: CONCERNS
updated: 2026-10-15
tags: [bmad, readiness, footer, sponsorship]
qmd: "solutioning gate fixcity"
---

# Solutioning gate

## Baseline

- Recommendation: presente (GO/NO-GO marketing).
- Product brief: presente.
- Research: presente, con fonti WCAG/GDPR/PA IT e best practice civic-tech.
- Brainstorm: presente.
- PRFAQ: presente.
- PRD: presente, FR-001–FR-007 e NFR-001–NFR-005.
- Architecture: presente, ma proposta e non approvata.
- UX: presente, proposta e non testata con utenti.
- Tech-spec: presente come bozza post-governance; **non** autorizza codice.
- Epics/stories: mappa + storie 1.1–2.2 in backlog; nessuna storia Epic 3 ready-for-dev.

## Copertura

| Gate | Esito | Motivo |
|---|---|---|
| FR → architettura | PASS parziale | Ogni FR ha un confine, ma mancano decisioni owner |
| NFR → UX/architettura | PASS parziale | WCAG/privacy descritti, da verificare con test |
| Governance | FAIL operativo | Nessuna policy sponsor/partner approvata |
| Economia/legale | FAIL operativo | Beneficiario, pagamenti e diritti non definiti |
| UX | CONCERNS | Nessun test di comprensione/fiducia eseguito |

## Verdict: CONCERNS

È una buona idea come **spazio trasparente per partner e sostenitori**. Non è
ancora pronta come piattaforma di banner sponsor. Prima dell'implementazione
servono: decisione del tenant, policy, beneficiario, diritti sui loghi, copy
approvato, test utenti e piano privacy.

## Gate successivo

Eseguire solo le storie 1.1, 1.2, 2.1 e 2.2 come discovery/documentazione.
Rivalutare Epic 3 dopo questi risultati. Non creare ancora una Filament Resource né
un nuovo modulo.
