---
title: "BMAD 09 — Release FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, release, staging, runbook, fixcity]
module: Fixcity
qmd: "bmad release staging backup smoke vertical slice fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - 06-quality-assurance.md
  - 10-retrospective.md
  - ../release-plan.md
---

# Release

**Perché:** il pilota PA richiede slice dimostrata, backup e runbook — non solo
classi presenti.

## Passi

1. Staging con migrazioni forward-only e seed demo.
2. Backup/restore provati (dati sacri: niente destroy cieco).
3. Smoke vertical slice coi quattro attori + sistema (notifiche/code).
4. Queue failure path e monitoring minimi.
5. Runbook incidenti e rollback operativo non distruttivo.
6. Sign-off owner PA / product su evidenze allegate.

## Gate

QA verde, backup OK, smoke browser OK (o gap ambiente documentato), firmatario.

## Output

Checklist release + runbook + decisione go/no-go in [release-plan](../release-plan.md).
