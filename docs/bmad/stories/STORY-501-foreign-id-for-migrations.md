---
title: "STORY-501 — Foreign key Ticket con `foreignIdFor`"
type: story
module: Fixcity
status: in_progress
created: "legacy"
updated: 2026-09-26
tags: [bmad, fixcity]
qmd: "STORY 501 foreign id for migrations FixCity BMAD story"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---
# STORY-501 — Foreign key Ticket con `foreignIdFor`

**Epic:** Data architecture  
**Priorità:** Must  
**Stato:** Implemented for new migration; legacy audit pending

## Criteri di accettazione

- [x] la migration nuova `ticket_activities` usa `foreignIdFor(Ticket::class)`;
- [x] la relazione User usa `foreignIdFor(XotData::make()->getUserClass(), 'user_id')`;
- [x] nessuna migration storica viene riscritta;
- [ ] audit dello schema storico e decisione su eventuali migration evolutive;
- [ ] migration testata sul database del comune pilota.

## Perché

`foreignIdFor` ricava tipo e chiave dal model e non congela l'assunzione `bigint`; questo è necessario perché i moduli possono usare UUID/chiavi configurabili e connessioni diverse.

## GitHub (tracciamento)

| Risorsa | Ruolo |
|---|---|
| [Issue tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/issues/264) | decisione migration R3 |
| [Discussion tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/discussions/265) | decisione compatibilità schema |
