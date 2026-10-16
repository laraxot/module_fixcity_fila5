---
title: "PRD — inbound blog ed eventi"
type: prd
status: proposed
updated: 2026-10-15
---

# PRD discovery

Estende [../footer-sponsorship/inbound-marketing-prd.md](../footer-sponsorship/inbound-marketing-prd.md).

## Funzionali

- **FR-IM-010** Ogni articolo dichiara persona, problema, evidenza, revisione, CTA.
- **FR-IM-011** Ogni evento dichiara modalità, accessibilità, programma, privacy.
- **FR-IM-012** Hub risorse organizzato per *bisogno*, non per formato file.
- **FR-IM-013** CTA scalate: info → evento → adotta/collabora → sostieni (gated).
- **FR-IM-014** `content_type=post|event` su Article; query pubbliche distinte.
- **FR-IM-015** Tenant isolation: contenuti pubblicabili per host.
- **FR-IM-016** SEO: title/description/OG; Event/Article schema quando live.
- **FR-IM-017** Sponsor evento solo contestuale + disclosure.
- **FR-IM-018** Ogni contenuto deve appartenere a un pillar e collegarsi a un
  cluster o a una risorsa operativa senza creare link farm.
- **FR-IM-019** Il sistema deve distinguere CTA informative, partecipative,
  B2G e di sostegno, con una sola CTA primaria per pagina.

## Non funzionali

- **NFR-IM-001** WCAG 2.2 AA su liste e dettaglio
- **NFR-IM-002** Nessun ad-tech / pixel terzi nel pilota
- **NFR-IM-003** Consenso esplicito per qualsiasi email
- **NFR-IM-004** KISS: riuso ArticleResource, no tabella Eventi
- **NFR-IM-005** Ogni contenuto pubblicato deve avere owner, revisore, fonti e
  data di revisione verificabili.

## Acceptance

Vedi readiness: owner, calendario, privacy, test utenti, blueprint approvato.
