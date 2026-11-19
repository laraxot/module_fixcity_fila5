---
title: "BMAD 04 — Story FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, story, github, fixcity]
module: Fixcity
qmd: "bmad story acceptance criteria issue discussion fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - 00-bootstrap.md
  - 05-implementation.md
  - ../stories/STORY-001-citizen-create-ticket.md
---

# Story

**Perché:** la story è l'unità di tracciamento; senza Issue/Discussion e AC
testabili il lavoro non è auditabile.

## Passi

1. Cercare collisioni ID e story equivalenti in `docs/bmad/stories/` e root.
2. Creare file in `laravel/Modules/Fixcity/docs/bmad/stories/` (non solo root).
3. Frontmatter: title, id, status, Issue, Discussion, tags.
4. Corpo minimo: attore, valore, scope, fuori-scope, dipendenze, AC, rischi,
   file owner, piano di verifica.
5. Collegare workflow `actor-*` e process toccati.

## Gate

- Issue + Discussion presenti;
- AC osservabili;
- nessun `STORY-XXX` per lavoro attivo;
- owner modulo corretto.

## Output

Story `ready-for-dev` (o equivalente) pronta per [05-implementation](05-implementation.md).
