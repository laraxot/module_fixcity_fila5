---
title: "AI harness — disciplina agenti modulo Fixcity"
type: concept
module: Fixcity
tags: [fixcity, ai, harness, migrations, ticket, bmad]
created: 2026-06-05
updated: 2026-06-05
qmd: "fixcity ai harness agent discipline ticket migration data sacred queueable action"
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/29"
  - "https://github.com/laraxot/base_fixcity_fila5/issues/266"
discussions:
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/30"
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/267"
related:
  - ./data-sacred-migrations.md
  - ./migration-update-timestamps-only.md
  - ../../../../../../docs/wiki/concepts/hackernoon-ai-coding-tips-fixcity-map.md
  - ../../../../../../docs/wiki/bmad/architecture.md
---

# AI harness — Fixcity

Estensione locale della [mappa HackerNoon root](../../../../../../docs/wiki/concepts/hackernoon-ai-coding-tips-fixcity-map.md).

## Scope modulo

- Ticket, profili, categorie, wizard segnalazioni
- Migrazioni owner in `database/migrations/` — **dati sacri**, una migrazione per modello
- Logica in `app/Actions/*Action.php` (QueueableAction) — **no** `laravel/app/Services`

## Tip applicati qui

| Tip | Fixcity |
|-----|---------|
| 003 | Piano audit migrazioni prima di edit colonne |
| 008 | STORY Fixcity in `docs/stories/` prima di refactor ticket/map |
| 010 | Riusa `LoadPublicTicketsGeoJsonAction`, `GenerateTicketsJsonAction` — grep Actions prima di creare |
| 015 | `.cursor/rules/no-laravel-app-services.mdc`, profiles migration guardrail skill |
| 020 | Wiki locale `docs/wiki/concepts/` + link root |

## Prompt e canon

- [llm-wiki.txt](../../../../../../bashscripts/tools/prompts/llm-wiki.txt)
- [second-brain-local-discipline.md](./second-brain-local-discipline.md)

## Script utili

```bash
bashscripts/tools/audit-module-artifact-parity.sh Fixcity
bashscripts/tools/audit-migration-timestamp-redundancy.sh laravel/Modules/Fixcity/database/migrations
bashscripts/tools/validate-wiki-frontmatter.sh laravel/Modules/Fixcity/docs/wiki/index.md
```

## Collegamenti

- [data-sacred-migrations.md](./data-sacred-migrations.md)
- [module-artifact-parity-audit.md](./module-artifact-parity-audit.md)
