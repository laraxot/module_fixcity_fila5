---
title: "STORY-499 — Assegnazione ticket dalla PA"
type: story
module: Fixcity
status: in_progress
created: "legacy"
updated: 2026-09-26
tags: [bmad, fixcity]
qmd: "STORY 499 pa ticket assignment FixCity BMAD story"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---
# STORY-499 — Assegnazione ticket dalla PA

**Epic:** Vertical slice  
**Priorità:** Must  
**Stato:** Implementazione iniziale completata; verifica integrazione pendente

## Criteri di accettazione

- [x] `Ticket::assignee()` usa `responsible_id`;
- [x] la pagina Filament dettaglio ticket espone assegnazione/rimozione;
- [x] la lista operatori è ricercabile;
- [ ] policy limita l'azione a operator/supervisor/admin;
- [ ] test Feature verifica persistenza e visibilità;
- [ ] test browser verifica il percorso reale.

## GitHub (tracciamento)

| Risorsa | Ruolo |
|---|---|
| [Issue tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/issues/383) | epic di completamento parent |
| [Discussion tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/discussions/392) | decisione ruoli PA |
