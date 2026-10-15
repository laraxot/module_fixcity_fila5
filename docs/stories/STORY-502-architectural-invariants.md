---
created: 2026-09-26
updated: 2026-09-26
qmd: "STORY 502 architectural invariants"
issues: []
discussions: []
title: "STORY-502 — Invarianti architetturali: l'elenco corto che mancava"
type: story
status: in_progress
tags: [invarianti, wiki, governance, guard, xotbase, policy, migration, second-brain]
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/502"
discussion_link: "https://github.com/laraxot/fixcity/discussions/502"
created_at: "2026-09-26T10:00:00Z"
---

# STORY-502 — Invarianti architetturali: l'elenco corto che mancava

## Obiettivo
Implementa l'elenco delle invarianti architetturali obbligatorie in modo che ogni nuovo file/modulo rispetti la configurazione zero (no File .txt non conformi, no directory uppercase, no mappature composer inconsistenti).

## Riferimenti
- GitHub Issue #502
- Discussion #502

## Checklist Evidence
- [ ] Fixcity stories create file .md con frontmatter YAML completo
- [ ] Nessun file .md con data/timestamp
- [ ] Nessuna directory in root modulo con iniziali maiuscole
- [ ] Al massimo 5 file .md in root modulo
- [ ] Piano di ruolo validato da `qmd search`

## Decisione
Garantire conformità zero per i file e le regole fondamentali. La traccia varia: invarianti xotbase → user → fixcity → concrete.

## Risorse
- `docs/wiki/rules/no-controllers-rule.md`
- `docs/wiki/rules/no-services-rule.md`
- `docs/wiki/rules/module-contracts-naming-placement.md`
- `docs/wiki/rules/wiki-markdown-frontmatter-mandatory.md`
- `bashscripts/docs/prompts/00-start.md`
