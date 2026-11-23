---
title: "Architecture Decision Records"
type: index
tags: [adr, decisioni]
created: 2026-11-18
updated: 2026-11-18
---

# Architecture Decision Records

Il **perche'** delle scelte di questo pacchetto: una decisione per file, con le alternative scartate.
Il nome del file e' l'argomento, senza date ne' numeri (la data sta nella frontmatter).

## Formato

```markdown
---
title: "ADR — <decisione in una riga>"
type: decision
status: accepted | superseded | deprecated
created: AAAA-MM-GG
updated: AAAA-MM-GG
issues: []
---

# ADR: <decisione>

## Contesto
Fatti e vincoli che hanno reso necessaria la scelta (misure, norme, policy, incidenti).

## Decisione
Cosa si fa, in concreto, e dove sta nel codice.

## Alternative scartate
Ogni alternativa seria, con il motivo per cui non e' stata scelta.

## Conseguenze
Pro, contro, debito che resta, cosa misurare. Chi cambia la decisione scrive un nuovo ADR e segna questo `superseded`.
```

## Decisioni

- [ADR — Il deploy tiene la cache delle rotte per lingua](route-trans-cache-deploy.md)
- [Il ciclo di vita Ticket è governato da Actions](ticket-lifecycle-is-action-owned.md)
- [Il dettaglio pubblico del ticket è un contenitore CMS con dati governati da Fixcity](public-ticket-detail-route-ownership.md)
- [Le Policy sono registrate tramite autodiscovery](policy-autodiscovery.md)

## Decisioni precedenti (formato libero, non ancora ADR)

- [wizard-submission-states](../wiki/decisions/wizard-submission-states.md)
- [bmad-plan-services-to-actions](../decisions/bmad-plan-services-to-actions.md)
- [no-services-final-status](../decisions/no-services-final-status.md)
- [themed-access-errors-through-http-exception-handler](../decisions/themed-access-errors-through-http-exception-handler.md)
- [ticket-hours-user-boundary](../decisions/ticket-hours-user-boundary.md)
