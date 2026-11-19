---
title: "Nomi dei file .md ancora fuori regola: cosa resta e perche'"
type: report
status: active
created: 2026-10-15
updated: 2026-10-15
tags: [convenzione, docs, naming, bmad]
qmd: "nomi file md fuori regola collisioni residue md-filenames plan unresolved -1.md -2.md archivio raw senza nome"
related:
  - ../bmad-output/stories/dcp-201-nomi-dei-file-md-senza-date-e-senza-suffissi.story.md
  - ../bashscripts/ai/wiki/rules/markdown-filename-conventions.md
---

# Nomi dei file .md ancora fuori regola

Dopo la rinomina di massa di DCP-201 restano **1118 file** che violano la regola (niente date, timestamp o suffissi `-1.md`, `-2.md`, `-3.md`) piu' **163 archivi** in `docs/raw/` esclusi per decisione D194-1 (il timestamp e' l'identita' del reperto). Non sono stati toccati perche' rinominarli avrebbe voluto dire **inventare un nome**: lo strumento non usa mai un numero o una data come distinguo.

## Perche' non si rinominano da soli
- **696 gruppi** hanno un file che occupa gia' il nome pulito (`foo.md` esiste e c'e' anche `foo-2.md`) con contenuto diverso e titoli uguali o assenti.
- **12 gruppi** hanno piu' copie (`foo-1.md`, `foo-2.md`, `foo-3.md`) senza un titolo che le distingua.
- **15 file** non hanno un nome ricavabile (il nome e' solo una data o solo un numero): report giornalieri (`2026-03-13.md`) e frammenti di tabelle spezzate da una shell (`-19.md`).
Dimensione dei gruppi (file per gruppo -> quanti gruppi): {2: 487, 3: 145, 4: 41, 5: 13, 6: 13, 8: 3, 9: 4, 10: 1, 13: 1}.

## Dove sono
| Area | File |
|---|---|
| `laravel/Modules/User` | 446 |
| `laravel/Modules/Notify` | 202 |
| `laravel/Themes/Barthelemy` | 155 |
| `laravel/Modules/Cms` | 84 |
| `laravel/Themes/Sixteen` | 63 |
| `laravel/Modules/Geo` | 33 |
| `laravel/Modules/Tenant` | 26 |
| `laravel/Modules/Job` | 22 |
| `laravel/Modules/Activity` | 15 |
| `laravel/Modules/Gdpr` | 15 |
| `laravel/Modules/Media` | 11 |
| `laravel/Modules/Lang` | 10 |
| `laravel/Modules/AI` | 5 |
| `laravel/Modules/Comment` | 5 |
| `bashscripts/ai` | 4 |
| `laravel/Modules/Rating` | 4 |
| `laravel/Modules/Xot` | 4 |
| `laravel/Modules/Seo` | 2 |
| `docs/correzioni-phpstan-completate-2.md` | 1 |
| `docs/correzioni-phpstan-completate-3.md` | 1 |
| `docs/correzioni-phpstan-completate_3.md` | 1 |
| `docs/analysis-dettagliata-7.md` | 1 |
| `docs/analysis-dettagliata-6.md` | 1 |
| `docs/analysis-dettagliata-5.md` | 1 |
| `docs/analysis-dettagliata-4.md` | 1 |
| `docs/analysis-dettagliata-2.md` | 1 |
| `docs/analysis-dettagliata-8.md` | 1 |
| `docs/analysis-dettagliata-3.md` | 1 |
| `docs/chat` | 1 |
| `docs/reports` | 1 |

## Esempi di gruppi in collisione
- `docs/correzioni-phpstan-completate.md` <- `correzioni-phpstan-completate-2.md`, `correzioni-phpstan-completate-3.md`, `correzioni-phpstan-completate_3.md`
- `docs/analysis-dettagliata.md` <- `analysis-dettagliata-7.md`, `analysis-dettagliata-6.md`, `analysis-dettagliata-5.md`, `analysis-dettagliata-4.md`
- `docs/chat/cascade-phpstan-zero-verification.md` <- `2026-07-12-cascade-phpstan-zero-verification.md`
- `bashscripts/ai/.agents/memories/docs-digest.md` <- `2026-02-26-docs-digest.md`
- `bashscripts/ai/.agents/memories/token-efficiency.md` <- `token-efficiency-2026-03-10.md`
- `bashscripts/ai/wiki/memories/docs-digest.md` <- `2026-02-26-docs-digest.md`

## Come si risolvono (decisione D194-2 di DCP-201)
1. Se il corpo di una copia e' un sottoinsieme dell'altra: `status: superseded` con `superseded_by`, poi ritiro con `.old` (regola `no-rm-no-archive-use-old-suffix`).
2. Se sono parti di una serie: un termine che dice cosa contengono (`-analisi`, `-sviluppo`), scritto da chi conosce il contenuto.
3. Frammenti senza contenuto utile (titolo `" 2"`): ritiro con `.old`, valutato con `audit-garbage-filenames.sh`.
4. Report giornalieri: una cartella per giorno con `index.md` (come `docs/worklog/AAAA/MM/GG/index.md`), non un file con la data nel nome.

## Come rigenerare questa fotografia
```bash
python3 -I bashscripts/tools/docs/md-filenames.py plan --out /tmp/md-plan.json   # JSON: renames, retire, unresolved, archive, unnamed
python3 -I bashscripts/tools/docs/md-filenames.py check                           # elenco delle violazioni, exit 1 se ce ne sono
python3 -I bashscripts/tools/docs/md-filenames.py links                           # link .md relativi rotti (baseline 2026-10-15: 44125, dopo la rinomina: 43853)
```
