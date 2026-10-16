---
title: "Modules Wiki Index"
type: index
tags: [modules, wiki, index, second-brain, bmad]
created: 2026-04-15
updated: 2026-09-27
qmd: "modules wiki index second brain ai harness bmad architecture parity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/272"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/273"
related:
  - ./concepts/second-brain-operating-model.md
  - ./concepts/ai-harness-module-discipline.md
  - ../../../../../docs/wiki/concepts/hackernoon-ai-coding-tips-fixcity-map.md
  - ../../../../../docs/wiki/bmad/architecture.md
---

# Modules Wiki Index

## Scopo

- [ticket-domain-and-public-flow-boundary](./concepts/ticket-domain-and-public-flow-boundary.md) — ownership ticket, CMS, Geo e Sixteen.

Wiki trasversale per tutti i moduli Laravel del progetto.

> Nota 2026-10-14: questo file sta nel wiki del modulo **Fixcity** (`laravel/Modules/Fixcity/docs/wiki/`); il titolo "Modules Wiki Index" e la frase sopra vengono da un modello condiviso tra moduli. Il wiki di progetto, valido per tutti i moduli, e' `docs/wiki/` alla radice del repo. Contenuto reale di questa cartella, contato il 2026-10-14: 121 concept in `concepts/` (canonici per regole e decisioni del modulo), 5 `rules/`, 5 `troubleshooting/`, 5 `comparisons/`, 3 `overviews/`, 3 `memories/`, 1 `decisions/`, 1 `philosophy/`, 2 `redundancy/` (legacy); `entities/`, `queries/`, `summaries/` e `lint/` non hanno file `.md`. Cronologia: [log.md](./log.md). Stato dei componenti: [MODULE-STATUS.md](./MODULE-STATUS.md) e' **stale** (elenca un modello `Segnalazione` che non esiste e un "60% completato" del 2026-06-13).

## AI / second brain

- [hackernoon-ai-coding-tips-fixcity-map](../../../../../docs/wiki/concepts/hackernoon-ai-coding-tips-fixcity-map.md) — Tips 001–022
- [bmad/architecture](../../../../../docs/wiki/bmad/architecture.md) — 5 pilastri + harness
- [ai-harness-module-discipline](./concepts/ai-harness-module-discipline.md) — contratto agenti
- [second-brain-operating-model](./concepts/second-brain-operating-model.md) — PARA/CODE
- [design-comuni-service-workflow-scope](./concepts/design-comuni-service-workflow-scope.md) — perimetro reale dei flussi Design Comuni e FixCity
- [freellmapi-boundary](./concepts/freellmapi-boundary.md) — router AI locale fuori dal runtime cittadino
- [design-comuni-ods-information-architecture](./concepts/design-comuni-ods-information-architecture.md) — mapping ODS v2.0 di servizi, pratiche e contatti
- [demo-operator-panel-access](./concepts/demo-operator-panel-access.md) — accesso pannello e capability operatore distinti
- [Xot canon second-brain](concepts/second-brain-local-discipline.md)

## Documentazione (tutti i moduli)

- [Frontmatter YAML + GitHub](../../../../../docs/wiki/rules/wiki-markdown-frontmatter-mandatory.md)
- [frontmatter-github-links-standard](../../../../../docs/wiki/memories/frontmatter-github-links-standard.md)

## Architettura DB

- [Parità N modelli = N migrate/factory/seeder](../../../../../docs/wiki/bmad/architecture-module-model-artifact-parity.md)
- [updateTimestamps solo in tableUpdate](../../../../../docs/wiki/rules/migration-update-timestamps-only.md) — no duplicati in `tableCreate`
- Audit: `bashscripts/tools/audit-module-artifact-parity.sh <Module>` · `audit-migration-timestamp-redundancy.sh`

## Harness per modulo (estensioni)

| Modulo | Pagina |
|--------|--------|
| Xot | ai-harness-xot-discipline _(target mancante, verificato 2026-10-14: nessun file con quel nome in `Modules/Xot/docs`)_ |
| Fixcity | [ai-harness-fixcity-discipline](concepts/ai-harness-fixcity-discipline.md) |
| User | [ai-harness-user-discipline](../../../User/docs/wiki/concepts/ai-harness-user-discipline.md) |
| Geo | [ai-harness-geo-discipline](../../../Geo/docs/wiki/concepts/ai-harness-geo-discipline.md) |

## Log

- [log](./log.md)
