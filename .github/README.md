---
title: "GitHub — Fixcity"
type: note
tags: [documentation, github]
created: 2026-11-26
updated: 2026-11-26
qmd: "github readme Fixcity"
---

# Fixcity — superfici GitHub

Package **Modules** del monorepo FixCity (`laravel/Modules/Fixcity`), repository indipendente
[`laraxot/module_fixcity_fila5`](https://github.com/laraxot/module_fixcity_fila5) (`composer: laraxot/module_fixcity_fila5`).

## Link rapidi

- [Issue](https://github.com/laraxot/module_fixcity_fila5/issues) · [Pull request](https://github.com/laraxot/module_fixcity_fila5/pulls)
- [Discussions](https://github.com/laraxot/module_fixcity_fila5/discussions) · [Actions](https://github.com/laraxot/module_fixcity_fila5/actions)
- [Milestone](https://github.com/laraxot/module_fixcity_fila5/milestones) · [Label](https://github.com/laraxot/module_fixcity_fila5/labels)
- [Wiki](https://github.com/laraxot/module_fixcity_fila5/wiki) · [Security](https://github.com/laraxot/module_fixcity_fila5/security)

## Sviluppo

- Branch di lavoro: `dev`. Mai branch personali, mai force-push (regole `git-forward-only`, `agent-no-git-branch-creation`).
- Ogni commit di un agente cita la sua issue GitHub: `(#N)` nel titolo o nel corpo. Se l'issue non esiste, si apre prima.
- Le modifiche nel monorepo `base_fixcity_fila5` vanno portate anche qui, altrimenti il prossimo mirror le cancella.

## Documentazione

- `docs/` contiene **al massimo 100 file `.md`**; il resto va in `archive/` o consolidato.
- `docs/adr/` per le decisioni, `docs/contracts/` per i contratti delle Action/interfacce, `docs/stories/` per il lavoro BMAD.
- Nomi file `.md` senza date/timestamp/suffissi `-1` (regola `markdown-filename-conventions`).

## Qualità

- PHPStan e Pest girano dal checkout host `laravel/` (config SSoT `laravel/phpstan.neon`), non da questo repo.
- Formattazione: `vendor/bin/pint` lato host.
- Workflow CI attivi: 0 file in `.github/workflows/`.

## Contributi

Issue e PR seguono le convenzioni del progetto madre (`base_fixcity_fila5`, `AGENTS.md`): Action invece di Service, test Pest nei `tests/` del package, documentazione in italiano nei `docs/` del package owner.
