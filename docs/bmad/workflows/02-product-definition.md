---
title: "BMAD 02 — Product definition FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, product, prd, fixcity]
module: Fixcity
qmd: "bmad product definition vertical slice acceptance criteria fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - 01-discovery.md
  - 04-story.md
  - ../README.md
---

# Product definition

**Perché:** senza slice end-to-end il prodotto resta un insieme di classi, non un
servizio civico verificabile.

## Vertical slice minima

`privacy → dati → posizione → riepilogo → creazione → conferma → tracking →
assegnazione → lavorazione → risoluzione → feedback`

## Passi

1. Tradurre discovery in requisiti numerati (P0/P1).
2. Per ogni requisito: attore, precondizioni, outcome, errore, prova.
3. Dichiarare invarianti: isolamento cittadino, `responsible_id`, timeline,
   notifiche, no controller/services.
4. Collegare story candidate senza placeholder `XXX` attivi.

## Gate

Ogni P0 ha acceptance criteria osservabile e prova (Pest/browser/policy).

## Output

Requisiti in gap/PRD + story candidate in `docs/bmad/stories/`.
