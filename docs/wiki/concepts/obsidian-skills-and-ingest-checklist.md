---
title: "Checklist Obsidian, skills e ingest QMD"
type: concept
module: Fixcity
tags: [obsidian, skills, qmd, ingest, second-brain]
created: 2026-06-05
updated: 2026-06-05
qmd: "fixcity obsidian skills ingest checklist qmd frontmatter github"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/256"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/257"
related:
  - ./module-artifact-parity-audit.md
  - ../../../../../../docs/wiki/rules/wiki-markdown-frontmatter-mandatory.md
---

# Obsidian skills and ingest checklist

## Scopo

Standardizzare il controllo periodico "skills + Obsidian + ingest" in ottica DRY + KISS.

## Checklist operativa

1. Verificare i documenti Obsidian disponibili (`docs/.obsidian/README.md`)
2. **Frontmatter** su ogni `.md` toccato: `title`, `type`, `tags`, `qmd`, `issues`, `discussions`
3. Audit parità modulo: `bashscripts/tools/audit-module-artifact-parity.sh <Module>` (vedi [module-artifact-parity-audit](./module-artifact-parity-audit.md))
4. Allineare i concetti modulo/tema con link bidirezionali (`related:` nel frontmatter)
5. Aggiornare `index.md` e `log.md` del modulo owner
6. Aggiornare rule + memory + skill quando emerge una nuova regola stabile
7. Eseguire ingest (`bashscripts/docs/llm-wiki-qmd.sh update`) dopo nuove pagine wiki
8. Query smoke (`bashscripts/docs/llm-wiki-qmd.sh search "<termine>"`) sui nuovi termini `qmd`

## Boundary

- Obsidian è strumento di navigazione/knowledge graph
- La fonte di verità resta `docs/raw` + `docs/wiki`
- Le correzioni runtime non sono complete senza wiki + ingest + GitHub tracciato

## False friends

- "Ho scritto il documento, quindi è già ingestito"
- "Issue citata nel testo senza URL in frontmatter"

## Collegamenti

- [architecture-wiki-frontmatter-github.md](../../../../../../docs/wiki/bmad/architecture-wiki-frontmatter-github.md)
