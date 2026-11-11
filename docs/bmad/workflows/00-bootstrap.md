---
title: "BMAD 00 — Bootstrap FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, bootstrap, fixcity]
module: Fixcity
qmd: "bmad bootstrap lock chat story scope fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - README.md
  - 04-story.md
  - ../workflow-catalog.md
---

# Bootstrap

**Perché:** evitare lavoro fuori contesto, collisioni multi-agente e story senza
tracciamento GitHub prima del codice.

Le story in `docs/bmad/stories/` sono la fonte operativa per il lavoro attivo:
intent, acceptance criteria, owner, claim, handoff, decisioni, verifiche e stato.
`docs/wiki/` conserva conoscenza riusabile; `docs/chat/` e i report storici sono
archivio legacy e non ricevono nuove note operative. In caso di conflitto prevale
la story BMAD collegata.

## Passi

1. Leggere `AGENTS.md`, bootstrap compact, l'indice delle story BMAD e la trigger map.
2. Cercare nel Second Brain: `qmd search "fixcity <topic>"` e docs modulo.
3. Verificare lock (`bashscripts/lock/check.sh`) sui file owner.
4. Dichiarare scope: attore toccato, outcome, rischio, moduli/temi.
5. Collegare o creare story BMAD con Issue + Discussion prima del codice.
6. Scrivere claim, handoff e stato nella story BMAD owner; usare `docs/chat/` solo come archivio storico.

## Gate

- scope non ambiguo;
- story con Issue/Discussion;
- nessun lock violato;
- owner = `Modules/Fixcity` (tema solo se UI).

## Output

Story BMAD aggiornata + lista file + decisioni/verifiche + link story.
