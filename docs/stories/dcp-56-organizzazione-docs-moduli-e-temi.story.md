---
id: dcp-56
title: "Organizzazione delle cartelle docs di moduli e temi: misura, link riparati, piano"
type: story
epic: dc-parity-v2
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [docs, organizzazione, link, second-brain]
related: [bmad-output/stories/docs-organization/README.md, bmad-output/docs-architecture/module-theme-docs-contract.md]
qmd: "dcp-56 organizzazione docs moduli temi link rotti docs-organizer non distruttivo"
---

# DCP-56: organizzazione delle cartelle docs

## Richiesta (2026-10-13, utente, ripetuta)
«Costantemente devi studiare, aggiornare, migliorare e organizzare meglio le cartelle docs dentro i moduli e dentro i temi.» Qui si misura lo stato reale e si applica solo cio' che e' sicuro; il piano di consolidamento resta quello delle story DOC-001..011 (`bmad-output/stories/docs-organization/`), tutte ancora `ready-for-dev`.

## Strumento e regole
`bashscripts/tools/docs/docs-organizer.py`: non cancella, non sposta, non rinomina; salta `raw/`, `stories/`, file con `.lock` recente e file toccati da meno di 10 minuti. Sottocomandi usati: `audit` (sola lettura), `links <docs> [--fix]` (corregge un link SOLO se il bersaglio esiste su disco).

## Base misurata (audit di sola lettura, 2026-10-13 ~17:10 UTC, 23 perimetri, 20284 file `.md`)
| Perimetro | file .md | nella radice | link | rotti | corpi duplicati | stub | collisioni di maiuscole |
|---|---|---|---|---|---|---|---|
| Modules/User | 3358 | 1538 | 16149 | 9257 (57%) | 1202 | 632 | 113 |
| Modules/Geo | 2092 | 574 | 419 | 215 | 390 | 354 | 80 |
| Themes/Sixteen | 1868 | 404 | 299 | 123 | 116 | 99 | 25 |
| Modules/Cms | 997 | 576 | 1389 | 489 | 128 | 203 | 42 |
| Modules/Lang | 995 | 491 | 292 | 104 | 265 | 86 | 97 |
| Modules/Fixcity | 501 | 98 | 205 | 84 | 1 | 20 | 1 |
| Modules/Xot | 256 | 15 | 252 | 203 (81%) | 12 | 16 | 9 |
(Il contatore `broken_n` dell'audit conta i link a file e quindi non coincide con gli eventi `unresolved` di `links`, che includono altri tipi di riferimento.)

## Eseguito
### Fixcity (verificato dal lead)
`links` in simulazione: 228 correggibili, 284 non risolti. Applicato `--fix`: 228 link riscritti; per ciascuno e' stato controllato che il nuovo bersaglio esista su disco (228 su 228). Nuova simulazione: 0 correggibili, 162 non risolti. Nessun file cancellato o rinominato (`git status`: solo `M` e `??`). Esempio reale: `bmad/workflows/07-ui-ux.md` puntava a `../../../../Themes/Sixteen/docs/bmad/README.md` (un livello in meno), ora `../../../../../Themes/...`.
### Xot, Sixteen, Cms, User (eseguito 17:11 UTC, stesso metodo)
| Perimetro | correggibili prima | riscritti | bersagli esistenti (verifica del lead su ogni riga) | non risolti prima -> dopo |
|---|---|---|---|---|
| Fixcity | 228 | 228 | 228 / 228 | 284 -> 162 |
| Xot | 109 | 109 | 109 / 109 | 746 -> 341 |
| Sixteen | 486 | 486 | 486 / 486 | 1546 -> 1128 |
| Cms | 294 | 294 | 294 / 294 | 1994 -> 1517 |
| User | 1448 | 1448 | 1448 / 1448 | 19252 -> 6161 |
Totale: 2565 link riparati, 0 con bersaglio inesistente. Lo strumento ha solo modificato file (`M`); le `R`/`D` visibili in `git status` di Sixteen (288 e 2) sono rinomine preesistenti in staging di un'altra sessione (c'erano gia' nel primo `git status` del giorno), non prodotte da questo run. Output grezzo: `docs-links-summary.txt` nello scratchpad della sessione.

## Cosa NON si fa qui (richiede decisione)
- Spostare o rinominare file (DOC-006 prevede una migration map approvata): i 1538 file nella radice di `User` e i 574 di `Cms` e `Geo` restano dove sono.
- Segnare `superseded` i corpi duplicati (`dups --fix`): da fare dopo la revisione della classificazione (DOC-004).
- I 162 link non risolti di Fixcity puntano a file che non esistono piu' o non sono mai esistiti: servono `--annotate` o la riscrittura del testo; non si inventano bersagli.

## Aperto
Lanciare `frontmatter` e `dups` per perimetro dopo i link; DOC-011 (gate continuo) per non far ricrescere i numeri: ogni story chiude con `links` a zero correggibili.
