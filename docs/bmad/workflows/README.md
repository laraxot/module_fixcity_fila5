---
qmd: "README"
title: "FixCity BMAD workflows"
type: workflow-index
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, workflows, fixcity, second-brain]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# FixCity — workflow BMAD canonici

Ogni cambiamento segue la fase adatta e mantiene distinto codice presente da prova
passata. L'indice copre il ciclo completo BMAD adottato nel progetto.

| Ordine | Workflow | Risultato |
|---:|---|---|
| 00 | [Bootstrap](./00-bootstrap.md) | owner, Second Brain, stato e lock |
| 01 | [Discovery](./01-discovery.md) | attori, baseline osservata e gap |
| 02 | [Product](./02-product-definition.md) | requisiti e acceptance criteria |
| 03 | [Architecture](./03-architecture.md) | boundary e invarianti |
| 04 | [Story](./04-story.md) | story con Issue, Discussion, AC e gate |
| 05 | [Implementation](./05-implementation.md) | cambiamento minimo, Action owner e test |
| 06 | [QA](./06-quality-assurance.md) | analisi statica e prova runtime |
| 07 | [UI/UX](./07-ui-ux.md) | responsive, accessibilità e prove visuali |
| 08 | [Security](./08-security.md) | permessi, isolamento tenant e privacy |
| 09 | [Release](./09-release.md) | runbook, blocker e readiness |
| 10 | [Retrospective](./10-retrospective.md) | lezioni e Second Brain |

Catalogo dei comandi BMAD installati: [workflow-catalog.md](../workflow-catalog.md).
Percorsi UX (dovrebbe/vede/può/fatto/manca): [user-journey-map](../../wiki/concepts/user-journey-map.md).
Attori eseguibili: [actor-citizen](./actor-citizen.md) · [operator](./actor-operator.md) ·
[supervisor](./actor-supervisor.md) · [admin](./actor-admin.md) · [system](./actor-system.md).

## Ciclo di lavoro

`00 → 01 → 02 → 03 → 04 → 05 → 06` è il percorso ordinario. `07` è obbligatorio
per ogni modifica visibile; `08` per ogni dato o permesso; entrambi precedono il
rilascio. `09` richiede gate ed evidenze; `10` scrive le decisioni riusabili. Un
blocker ambientale resta esplicito e non diventa uno stato verde.

Gli artefatti di dominio sono in `Modules/Fixcity/docs/`; Sixteen possiede solo
visual design, interazioni, accessibilità e report UI. Non duplicare implementazioni
né dichiarare flussi d'attore completi senza test/browser.
