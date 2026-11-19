---
title: "PRD — partner e sostenibilità nel footer"
type: prd
status: proposed-for-owner-review
updated: 2026-10-15
tags: [bmad, prd, footer, sponsorship, accessibility]
qmd: "product requirements document fixcity"
---

# Product Requirements Document

## FR-001 — Sezione editoriale

Il footer può esporre una sezione localizzata «Partner e sostenitori» alimentata
dal contenuto tenant, distinta dai link istituzionali.

## FR-002 — CTA di sostegno

Il contenuto può esporre una CTA «Sostieni il progetto» solo con URL, beneficiario,
disclosure e copy approvati dal tenant; in assenza, la CTA non viene mostrata.

## FR-003 — Scheda partner

Ogni partner deve avere nome, logo, alt text, URL, tipo di relazione, periodo di
validità, stato di approvazione e disclosure. Un partner senza URL valido resta
testo non interattivo.

## FR-004 — Governance

Il sistema deve supportare una policy di ammissibilità, revisore, data di revisione,
conflitto d'interesse e revoca. Le decisioni devono essere auditabili.

## FR-005 — Accessibilità

La sezione deve funzionare da tastiera, con zoom/reflow 400%, testo ingrandito,
lettori di schermo e `prefers-reduced-motion`; i loghi non sono l'unica fonte di
informazione.

## FR-006 — Privacy

Il pilota non usa tracking di terze parti, pixel, cookie non necessari o profilazione.
Qualunque eccezione richiede DPIA/valutazione privacy e consenso dove necessario.

## FR-007 — Localizzazione e tenant

Nome, descrizione, disclosure, alt text e URL devono essere localizzabili e isolati
per tenant; il fallback deve essere esplicito e non deve inventare contenuti.

## NFR

- **NFR-001 Trust:** sponsor e partner non possono sembrare endorsement del Comune.
- **NFR-002 Performance:** immagini ottimizzate, dimensione massima definita,
  lazy loading solo se non nasconde contenuto essenziale.
- **NFR-003 Security:** URL sanitizzati e domini esterni identificabili.
- **NFR-004 Legal:** licenza logo, disclosure e beneficiario registrati.
- **NFR-005 WCAG:** criteri 1.1.1, 1.3.1, 1.4.3, 1.4.10, 1.4.11, 1.4.12, 2.1.1,
  2.4.4 e 2.4.7/2.4.11 secondo la baseline WCAG adottata.

## Criteri di accettazione della discovery

La proposta non passa a implementazione se mancano owner, policy di ammissibilità,
beneficiario, diritti dei loghi, disclosure, piano privacy o test con cittadini.
