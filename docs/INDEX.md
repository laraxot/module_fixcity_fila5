---
title: "INDEX"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-10-13
qmd: "INDEX"
issues: []
discussions: []
status: draft
---

# Documentation Index

Elenco dei file markdown in questa directory.

## 📚 Core Documentation

| File | Description | Priority |
|------|-------------|----------|
| **[QUEUEABLE-ACTION-RULE.md](QUEUEABLE-ACTION-RULE.md)** | **Architecture Rule: Use QueueableAction (spatie), NEVER Services** | 🔴 **Critical** |
| **[TECH-STACK-NO-BOOTSTRAP.md](TECH-STACK-NO-BOOTSTRAP.md)** | Approved tech stack (Tailwind + Alpine + Lit + DaisyUI + Flowbite) | 🔴 **Critical** |
| **[BLOCKS-FOLDER-NAMING.md](BLOCKS-FOLDER-NAMING.md)** | Naming conventions from Flowbite/Tailwind UI | 🟡 **Important** |
| **[PROJECT-STRUCTURE.md](PROJECT-STRUCTURE.md)** | Project structure overview | 🟡 **Important** |
| **[DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md)** | Full documentation map | 🟢 **Reference** |

## 🎯 Quick Reference

### Architecture Rules (Must Follow)
1. **QueueableAction** - Use spatie/laravel-queueable-action for all business logic
2. **No Services** - Never create Service classes
3. **No Bootstrap** - Use TailwindCSS + DaisyUI instead
4. **No Controllers** - Use Filament, Folio, Actions, ViewModels

### Tech Stack
- **CSS**: TailwindCSS + DaisyUI (NO Bootstrap)
- **JS**: Alpine.js + Lit Web Components
- **PHP**: QueueableAction pattern (spatie/laravel-queueable-action)
- **UI**: Flowbite blocks + Tailwind UI

## Sottocartelle e loro scopo (aggiornato 2026-10-13, DCP-112)

Nessun file e' stato spostato o rinominato: questa tabella descrive cio' che c'e'. Le story (`stories/`, `bmad/`) non si cancellano ne' si rinumerano: si collegano o si marcano superseded.

| Cartella | Scopo |
|---|---|
| [`compliance/`](compliance/00-scopo-del-progetto.md) | Nuova. Scopo del progetto e ragioni delle scelte (`00-`), matrice di conformita' normativa con prove (`01-`), registri delle fonti normative verificate (`02-`, `03-`). Story DCP-112 e di chiusura DCP-130..145 |
| `stories/` | Story numerate del modulo (DCP-xx e STORY-xx), 55 file. Fonte di verita' di intento e criteri di accettazione |
| `bmad/` | Artefatti BMAD del dominio: flussi per attore, audit del percorso del cittadino, dossier, gap analysis, story BMAD storiche |
| `wiki/` | Second brain del modulo: concepts, overviews, decisions, rules, memories, troubleshooting |
| `llm-wiki/` | Wiki per agenti LLM (AGENTS.md, concepts, manifest di ingest) |
| `raw/` | Fonti grezze non elaborate: articles, comparisons, concepts, entities, incidenti |
| `roadmap/` | Roadmap di prodotto: launch plan, PRD, strategy, sprint planning |
| `rules/` | Regole formali del modulo (wizard Filament, divieto di Services) |
| `workflows/` | Workflow per attore: cittadino, amministratore, back office PA |
| `wizard/` | Nota sul tag `form` del wizard (1 file) |
| `investigation/` | Indagini sul frontend Design Comuni |
| `clean-code/` | Regola di schema clean code (1 file) |
| `technical-debt/` | Debito tecnico dichiarato (rinomine italiane, rimozione Livewire) |
| `phpstan/` | Risultati PHPStan per livello (JSON) |
| `assets/` | Screenshot di verifica (controlli mappa, passo dati) |

File indice nella radice di `docs/`: `INDEX.md` (questo, mappa delle sottocartelle), `00-INDEX.md` (navigazione canonica BMAD), `index.md` (panoramica del modulo), `README.md` (instradamento ai root di documentazione), `DOCUMENTATION_INDEX.md` (mappa storica generata il 2026-03-30). Regole di compliance e norme: parti da `compliance/00-scopo-del-progetto.md`.

## 🔗 Navigation

- **Stories**: `../../../docs/stories/`
- **Chat**: `../../../docs/chat/`
- **Wiki**: `../../../docs/wiki/`
- **Theme Docs**: `../Themes/Sixteen/docs/`

---
**DRY Principle**: This index references other documentation. Always check linked files for complete information.
