---
title: "FixCity — workflow per attore"
type: actor-flows
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, actors, citizen, pa, supervisor, admin, workflow]
module: Fixcity
qmd: "actor flows citizen operator supervisor admin system fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - workflows/actor-citizen.md
  - workflows/actor-visitor.md
  - workflows/actor-operator.md
  - workflows/actor-supervisor.md
  - workflows/actor-admin.md
  - workflows/actor-system.md
  - ../wiki/concepts/actor-flow-map.md
  - ../wiki/concepts/user-journey-map.md
---

# Workflow attori (sintesi)

Dettaglio eseguibile nei file `workflows/actor-*.md`.
Percorsi UX completi (dovrebbe/vede/può/fatto/manca):
[user-journey-map](../wiki/concepts/user-journey-map.md).

## Cittadino → [actor-citizen](workflows/actor-citizen.md)

Privacy → wizard → conferma → tracking → feedback. Solo i propri ticket.

## Visitatore → [actor-visitor](workflows/actor-visitor.md)

Home/elenco pubblico → filtri e tracking per codice; la CTA di creazione manda al login nella lingua richiesta, poi ritorno al wizard.

## Operatore PA → [actor-operator](workflows/actor-operator.md)

Coda → dettaglio → `responsible_id` → transizioni Enum → timeline/notifica.

## Supervisore → [actor-supervisor](workflows/actor-supervisor.md)

SLA/KPI → riassegnazioni → audit → escalation.

## Amministratore → [actor-admin](workflows/actor-admin.md)

Dashboard tenant → salute sistema → backup/release sign-off.

## Sistema → [actor-system](workflows/actor-system.md)

Eventi → activity → geo → code/notifiche senza perdere il ticket.

## Matrice minima delle prove

| Flusso | Cittadino | Operatore | Supervisore | Admin | Sistema | Prova |
|---|---:|---:|---:|---:|---:|---|
| Creazione ticket | ✓ | — | — | — | ✓ | Pest + browser |
| Visibilità isolata | ✓ | ✓ | ✓ | ✓ | — | policy test |
| Assegnazione | — | ✓ | ✓ | ✓ | — | feature test |
| Cambio stato | — | ✓ | ✓ | ✓ | ✓ | transition + activity |
| Timeline/notifica | ✓ | ✓ | ✓ | ✓ | ✓ | integration |
| Chiusura/feedback | ✓ | ✓ | ✓ | ✓ | ✓ | browser + Pest |

Gap per flusso: [actor-flow-map](../wiki/concepts/actor-flow-map.md).
