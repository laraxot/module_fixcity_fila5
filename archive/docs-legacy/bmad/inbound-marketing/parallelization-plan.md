---
title: "Parallelization plan — inbound marketing"
type: parallelization-plan
status: concerns
updated: 2026-10-15
tags: [bmad, parallel, swarm]
qmd: "inbound stories parallel waves owned scope dependencies"
issues: []
discussions: []
---
# Parallelization plan

Il piano è documentale: non autorizza ancora codice perché il readiness gate è
`CONCERNS`. Le stories possono procedere in parallelo solo con scope disgiunti e
owner assegnati.

| Wave | Stories | Condizione |
|---|---|---|
| 1 | IM-BLOG-001, IM-GDPR-001, IM-BLOG-005, IM-MEDIA-001 | owner editoriale e privacy ancora bloccanti |
| 2 | IM-BLOG-002, IM-FIXCITY-001, IM-TENANT-001 | dopo governance; scope separati |
| 3 | IM-BLOG-003, IM-CMS-001, IM-SEO-001, IM-UI-001, IM-LANG-001 | dopo contratti dati |
| 4 | IM-SIXTEEN-001, IM-MEETUP-001 | solo presentazione; nessuna logica dominio |
| 5 | IM-NOTIFY-001 | deferred e solo dopo consenso verificato |

Merge order: una story per owner alla volta; review PHPStan/Pest/browser nei
confini interessati; nessun merge di implementazione finché il readiness report
non passa da `CONCERNS` a `READY`.

## Wave fiducia/adozione (docs-first, parallel set 1–2)

| Set | Stories | Nota |
|---|---|---|
| 1 | IM-FIXCITY-002 | prova fiducia — after/with IM-BLOG-001 |
| 2 | IM-FIXCITY-003, IM-BLOG-006 | frizioni + playbook |
| 2 | IM-FIXCITY-005 | demo seed (no FO production) |
