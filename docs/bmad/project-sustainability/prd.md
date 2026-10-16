---
title: "PRD — sostenibilità FixCity (discovery)"
type: prd
status: proposed
updated: 2026-10-15
---

# PRD — requisiti documentali

Questi FR descrivono ciò che il *sistema di documentazione e governance* deve
garantire prima di qualsiasi build. Non autorizzano codice.

## Funzionali

- **FR-001** Esiste un ordine ufficiale dei canali di sostenibilità (P0–P3 / No).
- **FR-002** Ogni canale ha owner, rischi, e condizioni di GO.
- **FR-003** L’ownership è mappata su moduli/temi (Cms, Tenant, Fixcity, …).
- **FR-004** La comunicazione FO (footer/pagina) segue il dossier footer-sponsorship
  (partner sì, banner no).
- **FR-005** Metriche di impatto ticket sono definite come input di storytelling,
  senza esporre PII.
- **FR-006** Opt-in tenant per partner/CTA è documentato prima del JSON live.
- **FR-007** Privacy di eventuali pagamenti/donazioni è documentata in Gdpr docs.

## Non funzionali

- **NFR-001** Nessun tracking pubblicitario di terzi nel FO.
- **NFR-002** WCAG 2.2 AA su eventuali superfici di recognition.
- **NFR-003** Isolamento tenant: nessun partner cross-tenant non autorizzato.
- **NFR-004** DRY: un dossier BMAD + docs modulo; niente tre narrazioni divergenti.
- **NFR-005** KISS: nessun modulo Sponsor finché Epic governance non chiude.

## Accettazione discovery

Readiness passa da CONCERNS a READY-FOR-SOLUTIONING solo con: policy partner,
beneficiario (o «donazioni deferred»), listino/co-funding B2G bozza, KPI impatto,
owner nominati.
