---
qmd: "workflow catalog"
title: "FixCity — catalogo dei workflow BMAD installati"
type: workflow-catalog
status: active
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, catalog, fixcity, second-brain]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - workflows/README.md
  - ../wiki/concepts/user-journey-map.md
  - actor-flows.md
---

# BMAD installato e suo uso nel progetto

Questa mappa collega i comandi trovati nell'installazione locale sotto
`bashscripts/ai/.agents/bmad-skills/` (o symlink `.claude/skills/bmad-*`)
agli artefatti owner. Non afferma che un comando sia stato eseguito: lo stato si
registra nella story e nel tracker dopo aver prodotto e verificato l'output.

| Comando BMAD locale | Fase e uso | Artefatto owner FixCity |
|---|---|---|
| `workflow-init` | inizializzare iniziativa, percorsi e stato | story/brief in `docs/bmad/` |
| `research` | raccogliere evidenze e alternative | `gap-analysis.md`, journey map |
| `brainstorm` | generare e selezionare opzioni | decisione collegata alla story; nessun requisito implicito |
| `product-brief` | definire obiettivo, utenti e valore | brief owner in `docs/bmad/` |
| `prd` | requisiti funzionali/non funzionali e acceptance criteria | `docs/prd.md` + stories |
| `create-ux-design` | user flow, wireframe, accessibilità | Sixteen UX spec/report + story |
| `architecture` | componenti, boundary e decisioni | architecture/spec del modulo owner |
| `tech-spec` | disegno tecnico implementabile | specifica legata alla story |
| `create-workflow` | rendere ripetibile un processo | `docs/bmad/workflows/` |
| `create-story` | valore, scope, AC, dipendenze e test | `docs/bmad/stories/` con Issue e Discussion |
| `solutioning-gate-check` | readiness di architettura e stories | esito gate collegato al tracker |
| `sprint-planning` | selezionare lavoro pronto e capacity | `docs/sprint-status.yaml` |
| `dev-story` | implementare una story pronta e aggiornare stato | codice owner + test + story |
| `workflow-status` | mostrare progressi reali e blocker | tracker/story, senza inferire completamento |
| `create-agent` | solo quando serve una capability durevole distinta | workflow/agente locale con owner esplicito; non crea ruoli prodotto |

## Sequenza progetto

Usare [workflows/README.md](workflows/README.md) per il ciclo 00–10.
Workflow d'attore: [cittadino](workflows/actor-citizen.md), [operatore](workflows/actor-operator.md), [supervisore](workflows/actor-supervisor.md), [amministratore](workflows/actor-admin.md), [sistema](workflows/actor-system.md).
Matrice UX atteso/presente/mancante: [actor-journeys.md](actor-journeys.md) e approfondimento [user-journey-map](../wiki/concepts/user-journey-map.md).

Story attiva: [STORY-013 — Preferenze email segnalazioni](stories/STORY-013-ticket-notification-preferences.md) con [piano dev](stories/STORY-013-ticket-notification-preferences.dev.md).

### Stato di avanzamento

I 15 comandi sopra sono inventariati dal filesystem BMAD locale; questo catalogo non
attesta che siano stati eseguiti in questa iniziativa. I processi del progetto sono
le fasi 00–10 e le cinque schede attore. Non marcare stories `done` senza prove
UI/auth/test; registrare blockers e output nel tracker.
