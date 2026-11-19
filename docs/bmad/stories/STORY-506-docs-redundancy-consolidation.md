---
title: "STORY-506 — Consolidamento ridondanza docs FixCity e temi"
type: story
module: Fixcity
status: in_progress
created: "legacy"
updated: 2026-09-26
tags: [bmad, fixcity]
qmd: "STORY 506 docs redundancy consolidation FixCity BMAD story"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---
# STORY-506 — Consolidamento ridondanza docs FixCity e temi

**Epic:** Second Brain & documentation governance  
**Priorità:** Must  
**Stato:** Audit completato; consolidamento per famiglie da eseguire

## Criteri di accettazione

- [x] conteggio SHA-256 per modulo e temi registrato;
- [x] famiglie duplicate e percorsi sospetti identificati;
- [x] owner canonico e policy di classificazione definiti;
- [x] collisioni tra ID story duplicate inventariate (500, 502, 503, 495, 496);
- [ ] riferimenti entranti verificati per ogni famiglia;
- [x] story policy 502 assorbita nella story canonica di invarianti;
- [x] story second-brain 505 assorbita in questa story;
- [x] ID `STORY-503` del refactor Action reso descrittivo per non collidere col tracking cittadino;
- [x] copie controller 495/496 marcate `superseded` e collegate alle story Folio attive;
- [x] placeholder `STORY-XXX` resi storici e collegati all'epic/verification attuale;
- [ ] collisione legacy 500 tra matrice transizioni e verifica vertical slice classificata;
- [ ] riferimenti entranti verificati e aggiornati per ogni collisione;
- [x] contratti architetturali Fixcity e UX Sixteen consolidati con ownership distinta;
- [x] Second Brain Fixcity ridotto a indice del contratto canonico;
- [ ] duplicati esatti consolidati in change set separati;
- [ ] index wiki aggiornati;
- [ ] healthcheck Second Brain/QMD eseguito quando disponibile.

## Regola operativa

Aggiornare prima il canonico, poi archiviare o sostituire il duplicato con un riferimento. Non creare un nuovo report datato se il documento esistente può essere aggiornato.

## GitHub (tracciamento)

| Risorsa | Ruolo |
|---|---|
| [Issue tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/issues/502) | issue consolidamento da associare |
| [Discussion tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/discussions/503) | decisione owner documentali |
