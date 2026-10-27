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
updated: 2026-10-25
tags: [docs, organizzazione, link, second-brain]
related: [bmad-output/stories/docs-organization/README.md, bmad-output/docs-architecture/module-theme-docs-contract.md]
qmd: "dcp-56 organizzazione docs moduli temi coppie maiuscole gemelli .old sussunzione censimento event module.json"
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

## Misura 2026-10-25 — coppie di maiuscole nella radice dei moduli

Censimento di oggi (script inline: `bashscripts/docs-audit-naming.sh`, citato dalla
memoria `docs-twin-merged-but-not-collapsed`, **non esiste** in questo checkout):
collisioni di maiuscole su `.md` — `Geo` 79, `Cms` 34, `Tenant` 35. Quasi tutte stanno
in `docs/`, non nella radice.

### Eseguito: 9 gemelli di radice collassati

Prova di sussunzione per ogni coppia (`grep -vxFf canonico gemello` = righe uniche del
gemello): 8 coppie su 9 davano `0`; la nona (`Geo/changelog.md`) aveva 4 righe, tutte
template nwidart (`:package_name`, `202X-XX-XX`). Canonico = maiuscolo, che è il nome
richiesto dal gate `ModuleRootHygieneTest` per `CHANGELOG.md`.

| Modulo | File portati a `.old` |
|---|---|
| Cms | `license.md` |
| Geo | `changelog.md`, `license.md` |
| Lang | `changelog.md` |
| Rating | `changelog.md`, `license.md` |
| Tenant | `changelog.md`, `license.md` |
| Xot | `license.md` |

`git mv <file> <file>.old` (regola no-rm): nessun contenuto perso, nessun `rm`.

### Effetto sul gate "max 6 `.md` in radice"
Geo 7→5, Rating 6→4, Tenant 7→5, Xot 6→5. Nessun modulo supera più 6.

### Limite di verifica in questo checkout
`ModuleRootHygieneTest` salta i moduli senza `.git` propria e qui **nessun** modulo ha un
repo proprio (checkout appiattito): dataset vuoto → 9 test "Empty data set" e la guardia
`ha dei moduli da controllare` scatta come previsto. I conteggi sopra sono verificati a
mano (`ls <modulo>/*.md`), non da Pest.

## Seconda ondata 2026-10-25 — 156 gruppi chiusi

Politica unica, registrata in `docs/wiki/memories/docs-twin-merged-but-not-collapsed.md` §4:
canonico = **minuscolo kebab-case**; il gemello maiuscolo/camelCase va archiviato con
`git mv <gemello> <nome>.old` dopo la prova di sussunzione
(`grep -vxFf <canonico> <gemello> | grep -vc '^$'`). Lo stub `status: superseded` non chiude
la collisione: restano due file che collidono su un filesystem case-insensitive. Nessun `rm`,
nessuna cancellazione di contenuto: dove il canonico minuscolo era uno stub morto il corpo
del gemello è stato recuperato nel canonico (Cms `product-launch-plan`, `product-roadmap`,
`script-placement-rules`, `sprint-planning`, `user-research`; Geo `philosophy`).

| Perimetro | Gruppi prima | Gruppi dopo | Note |
|---|---|---|---|
| `Modules/Geo/docs` | 78 | 19 | radice 0; 16 sono `raw/` (archivio dichiarato) + 3 `AGENTS.md` in `wiki/`, `schema/`, `llm-wiki/` |
| `Modules/Xot/docs` | 8 | 0 | 1851 → 1842 `.md`; riparato anche il `CHANGELOG.md` di radice (145 righe duplicate, un blocco di frontmatter a metà corpo, 4 link morti) |
| `Modules/Tenant/docs` | 35 | 0 | chiusi anche i 2 residui: vedi «Coppie divergenti» sotto |
| `Modules/Cms/docs` | 34 | 0 | chiusi anche i 3 residui: vedi «Coppie divergenti» sotto |
| `Themes/Meetup/docs` | 4 | 0 | |
| `Themes/TwentyOne/docs` | 7 | 0 | |
| `Themes/Barthelemy/docs` | 21 | 0 | 12 non erano duplicati ma doc di moduli diversi sullo stesso basename: rinominati per concetto (`architecture-ui-module`, `roadmap-theme`, `todo-tenant`, …) |

I gruppi residui di Tenant e Cms non sono duplicati: fonderli produrrebbe un testo
contraddittorio (due PRD, due strategie). Restano aperti finché un owner non decide quale
versione è canonica.

Link entranti ripuntati solo dove il bersaglio esiste: 29 file in Tenant+Cms, 26 in
Barthelemy, 4 in Geo, più `00-INDEX`/`README` dei temi. Zero link nuovi rotti, zero
link che puntano a un `.old`.

### `Modules/Event`: da cartella spuria a modulo dormiente
Non era uno stray: è l'owner BMAD deliberato del dominio eventi (dossier + decision log),
con modello `Event` e migration. Ma una cartella in `Modules/` senza `module.json` non è un
modulo per nwidart, per il merge dei `composer.json`, per PHPStan (`paths: Modules/`) e per
ogni `for m in Modules/*`. Aggiunti `module.json` (`providers: []`, `active: 0`) e
`composer.json` (`laraxot/module_event_fila5`), spostati `Models/` → `app/Models/` e
`Migrations/` → `database/migrations/` (due cartelle con maiuscole in meno, e path coerenti
col PSR-4 dichiarato). Resta fuori da `modules_statuses.json`:
`FileActivator::hasStatus()` richiede una voce esplicita, quindi nwidart lo elenca come
`[Disabled]`, nessun provider è registrato e la migration non viene caricata. Verificato:
`php artisan module:list` → `[Disabled] Event`; `composer dump-autoload` →
`Modules\Event\` in `autoload_psr4.php`.

### Coppie divergenti: rinominate per concetto, non fuse
Dove i due file sono **documenti diversi** (non gemelli) fondere produrrebbe un testo
contraddittorio, archiviare non si puo' (nessuna prova di sussunzione). Si rinomina il
secondo per concetto, come in `Themes/Barthelemy`:

| Modulo | Prima | Dopo |
|---|---|---|
| Tenant | `PHILOSOPHY.md` (1347 r., v2.0 EN) | `philosophy-religion.md` (`philosophy.md`, 105 r., resta il riassunto canonico) |
| Tenant | `bmad/ARCHITECTURE.md` (254 r., architettura vera) | `bmad/architecture-tenant.md` |
| Tenant | `bmad/architecture.md` (41 r., residuo troncato dello stesso doc, con marker di conflitto commitati: la story `git-state-conflict-marker-daemon-cleanup` documenta la troncatura) | `bmad/architecture.md.old` |
| Cms | `PHILOSOPHY.md` (625 r., dogmi EN) | `philosophy-religion.md` (`philosophy.md`, 153 r., resta il canonico) |
| Cms | `PRD.md` (525 r., corporate v1.0) | `prd-corporate.md` (`prd.md`, 94 r., resta il vivente) |
| Cms | `PRODUCT_STRATEGY.md` (97 r., EN) | `product-strategy-corporate.md`, `superseded_by` riallineato a `product-strategy.md` |

Tolti anche gli 8+8 righe di frontmatter spurio (secondo blocco `title:` emesso dal
generatore shard) dai due file Tenant toccati, e risolto un conflitto Git nel frontmatter di
`Tenant/docs/wiki/overviews/tenant-module.md` (le due parti davano path diversi; esistono
solo quelli `../../`). Link ripuntati: 7 interni a Tenant, 3 in `Cms/docs/index.md`, 4 in
moduli esterni (Seo x2, AI, Blog) che puntavano a `Cms/docs/PRD.md`.

Census `basename.lower()` per cartella: **Tenant 2 -> 0, Cms 3 -> 0**.

## Aperto
Lanciare `frontmatter` e `dups` per perimetro dopo i link; DOC-011 (gate continuo) per non far ricrescere i numeri: ogni story chiude con `links` a zero correggibili.

Census repo-wide dopo questa ondata (script inline, `.md` per cartella, `basename.lower()`):
**887 gruppi residui in 11 moduli/temi** — Notify 537, Lang 82, User 82, Rating 44,
Media 31, Sixteen 25, AI 23, Seo 20, Geo 19, Blog 18, Gdpr 6. Le famiglie ricorrenti sono
di nuovo gli shard del generatore (`ON-DEMAND-PATTERN`, `METODI_DUPLICATI_ANALISI`,
`PROJECT-STRUCTURE`, `CHANGELOG`) piu' i residui dichiarati di `Geo/docs/raw` (16) e
`AGENTS.md`. Serve un'altra ondata per modulo, con la stessa prova di sussunzione.

Altri due debiti misurati, non toccati qui:
- **123 file** in `Modules/Tenant/docs` hanno marker di conflitto Git commitati
  (`^(<<<<<<<|>>>>>>>)`) — incidente noto, richiede uno sweep dedicato.
- **996 file** in tutto il repo hanno un blocco frontmatter spurio (`title:` in un blocco
  `---…---` dentro il corpo, firma del generatore shard). Rimosso solo nei file toccati.

`bashscripts/docs-audit-naming.sh`, citato dalle memorie come gate, **non esiste** in questo
checkout: il censimento è uno script inline (`os.walk` + `basename.lower()` per cartella).
Va aggiunto se si vuole che DOC-011 sia eseguibile.
