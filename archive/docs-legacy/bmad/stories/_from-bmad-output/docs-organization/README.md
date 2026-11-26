---
title: "Documentation organization story map"
type: index
status: active
updated: 2026-10-13
tags: [bmad, documentation, modules, themes, qmd, graphify, second-brain]
qmd: "BMAD story map documentation organization modules themes"
---

# Story map — organizzazione docs

Queste storie completano la Story 1.2 e costituiscono il backlog per la prossima
sessione. Sono intenzionalmente sequenziali: prima si osserva, poi si classifica,
infine si consolida. Nessuna story autorizza modifiche applicative.

## Sequenza

| ID | Story | Dipende da |
|---|---|---|
| DOC-001 | inventario e ownership di tutti i docs root | — |
| DOC-002 | completamento e verifica degli ingressi | DOC-001 |
| DOC-003 | audit front matter e stati | DOC-001 |
| DOC-004 | classificazione duplicati e indici concorrenti | DOC-001 |
| DOC-005 | verifica e riparazione link | DOC-002, DOC-004 |
| DOC-006 | migration map e consolidamento non distruttivo | DOC-003, DOC-004, DOC-005 |
| DOC-007 | salute QMD e collezioni | DOC-002, DOC-006 |
| DOC-008 | estrazione e diagnosi Graphify | DOC-005, DOC-006 |
| DOC-009 | curation Second Brain e correzioni | DOC-006, DOC-007, DOC-008 |
| DOC-010 | qualità contenutistica per modulo/tema | DOC-002, DOC-006 |
| DOC-011 | healthcheck continuo e readiness gate | DOC-007, DOC-008, DOC-009, DOC-010 |

## Regola per domani

Eseguire DOC-001–005 in sola lettura o con modifiche annotate. Eseguire DOC-006
solo dopo revisione della migration map. Le story DOC-007–011 chiudono il ciclo
con evidence riproducibile.
