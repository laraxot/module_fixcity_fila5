---
title: "Docs organization audit 2026-10-08"
type: audit
status: done
created: 2026-10-08
updated: 2026-10-08
scope: "laravel/Modules/*/docs e laravel/Themes/*/docs (23 perimetri, 28.407 file .md)"
agent: docs-audit
tags: [docs, audit, organization, bmad, frontmatter, duplicates, links, locks]
qmd: "docs organization audit moduli temi debito docs indice canonico frontmatter duplicati superseded link rotti lock stub scaffold pilota Seo Meetup docs-organizer"
tool: bashscripts/tools/docs/docs-organizer.py
related:
  - ../laravel/Modules/Seo/docs/stories/docs-organization-pilot.story.md
  - ../laravel/Themes/Meetup/docs/stories/docs-organization-pilot.story.md
  - ../docs/wiki/rules/docs-index-file.md
  - ../docs/wiki/rules/theme-module-docs-readme-mandatory.md
  - ../docs/wiki/rules/module-theme-root-md-files-limit.md
  - ../docs/wiki/rules/wiki-markdown-frontmatter-mandatory.md
issues: []
discussions: []
---

# Docs organization audit 2026-10-08

Artefatto BMAD cross-modulo. Ordine permanente dell'utente: tenere ordinate di continuo le cartelle `docs/` di moduli e temi, senza cancellare, spostare o rinominare nulla. Questo giro: misura dell'intero perimetro (sola lettura) e due piloti completi (Seo, Meetup) descritti nelle loro story.

## Sintesi

- **28.407 file `.md`** in 23 perimetri. Il 37% (10.518) sta direttamente nella radice di `docs/`, non in una sottocartella.
- **Frontmatter completo (6 chiavi) solo su 1.428 file (5%)**. 4.878 file hanno un blocco frontmatter spurio iniettato nel corpo, 11.459 un frontmatter generico (`type: note`, `tags: [documentation]`, `qmd` uguale al titolo), 8.882 hanno un frontmatter parziale (quasi sempre manca `status`), 1.719 non hanno frontmatter, 36 ce l'hanno non chiuso.
- **7.238 copie ridondanti** (stesso corpo, frontmatter a parte) e 3.119 gruppi di nomi che differiscono solo per case, underscore o trattino (1.621 collisioni di solo case nella stessa cartella: rompono i checkout su filesystem case-insensitive).
- **Indici**: 63 indici attivi in eccesso rispetto a uno per `docs/` (`00-INDEX.md`, `00-index.md`, `00-index-1.md`, `INDEX.md`, `index.md`, `readme.md` coesistono in 18 perimetri su 23 (escluso `README.md`)).
- **Link rotti negli indici: 13.732 su 26.632 (52%)**, il 67% in `User` (indice generato da 455 KB).
- **93 file `.md.lock` piu' vecchi di 7 giorni** (elenco in appendice, nessuno rimosso).
- **182 nomi di file compaiono in almeno 10 perimetri** per un totale di 5.284 file (19%): un set di scaffold replicato da un generatore (product-*, redundancy-*, performance-optimization, qmd-setup, on-demand-pattern, ...).
- I 3 perimetri con piu' debito: **User, Xot, Notify**. Per densita': **User** (436 punti ogni 100 file), **UI** (286), **Blog** (215).

## Metodo e limiti

Strumento: `bashscripts/tools/docs/docs-organizer.py audit --out FILE.json` (un solo `os.walk` per perimetro, circa 8 secondi in tutto, nessun fork). Definizioni:

- **fm completo**: frontmatter chiuso con tutte e sei le chiavi `title`, `type`, `status`, `updated`, `tags`, `qmd`. **fm difettoso**: assente, non chiuso, o con blocco spurio nel corpo (rilevatore attento ai blocchi di codice, quindi gli esempi di schema non contano).
- **duplicati di contenuto**: stesso hash del corpo senza frontmatter (un hash sul file intero ne vede 3.401, perche' un generatore ha scritto `title` e `qmd` diversi nelle copie). Le copie gia' marcate `superseded` non contano come debito.
- **link rotti**: solo link relativi dentro file indice (nome `00-index*`, `index`, `readme`) a profondita' massima 1, e' un campione e non l'intero albero. Quelli annotati ma ancora irrisolvibili contano ancora.
- **debito** = file in radice + 2 x duplicati + 2 x gruppi di varianti di nome + 10 x (indici attivi - 1) + fm difettosi + 0,25 x fm generici o parziali + link rotti + 2 x lock vecchi + 20 se manca `stories/`. E' un indicatore di priorita' assoluta; la colonna "per 100 file" lo normalizza.
- Esclusioni: `raw/` e `stories/` non vengono mai modificati dallo strumento; i `.md.lock` sono solo elencati.
- Limite noto: `wiki-hygiene.py` risolve i percorsi `app/...` citati nei documenti dal root del repo e non dal modulo, quindi segnala come morti file che esistono (verificato su Seo). Per questo la staleness nei piloti e' stata controllata a mano sul disco.

## Tabella per debito docs

Misura del 2026-10-08 (mattina), dopo i due piloti; il repo cambia mentre si misura perche' altre sessioni scrivono in `User`, `Activity` e altri (i totali si spostano di qualche centinaio tra due letture) (Seo e Meetup mostrano gia' lo stato riordinato; prima: Seo 408, Meetup 60).

| # | Perimetro | .md | in radice | indici attivi in radice | fm completo | fm difettoso | duplicati di contenuto | varianti di nome | link rotti negli indici | lock vecchi | stories/ | debito | per 100 file |
|---|-----------|----:|----------:|-----------------:|------------:|-------------:|-----------------------:|-----------------:|------------------------:|------------:|:--------:|-------:|-------------:|
| 1 | Module User | 3537 | 1546 | 6 | 2% | 819 | 1310 | 288 | 9156/16149 | 1 | si | 15433 | 436 |
| 2 | Module Xot | 7410 | 2452 | 5 | 6% | 1339 | 2655 | 406 | 968/1566 | 9 | si | 12338 | 167 |
| 3 | Module Notify | 3482 | 1552 | 6 | 4% | 921 | 838 | 825 | 394/780 | 0 | si | 6852 | 197 |
| 4 | Module UI | 1331 | 609 | 4 | 4% | 395 | 334 | 253 | 1382/1557 | 0 | si | 3812 | 286 |
| 5 | Module Geo | 2089 | 574 | 4 | 1% | 591 | 390 | 212 | 215/417 | 2 | si | 2987 | 143 |
| 6 | Module Cms | 1003 | 574 | 4 | 2% | 156 | 123 | 179 | 484/1384 | 1 | si | 2057 | 205 |
| 7 | Module Lang | 1059 | 546 | 5 | 3% | 48 | 270 | 182 | 101/279 | 0 | si | 1882 | 178 |
| 8 | Module Activity | 940 | 384 | 6 | 1% | 535 | 200 | 97 | 188/736 | 1 | si | 1852 | 197 |
| 9 | Theme Sixteen | 1481 | 414 | 4 | 2% | 643 | 101 | 31 | 173/1098 | 19 | si | 1764 | 119 |
| 10 | Theme Barthelemy | 1796 | 21 | 1 | 0% | 551 | 41 | 153 | 16/67 | 1 | NO | 1309 | 73 |
| 11 | Module Tenant | 603 | 277 | 4 | 5% | 35 | 169 | 74 | 131/281 | 0 | si | 1093 | 181 |
| 12 | Module Media | 563 | 277 | 5 | 5% | 49 | 156 | 71 | 120/303 | 0 | si | 1062 | 189 |
| 13 | Module Job | 527 | 250 | 4 | 6% | 104 | 162 | 57 | 38/129 | 0 | si | 956 | 181 |
| 14 | Module Rating | 585 | 140 | 4 | 23% | 61 | 202 | 65 | 83/430 | 0 | si | 946 | 162 |
| 15 | Module Gdpr | 388 | 205 | 5 | 4% | 66 | 121 | 35 | 32/237 | 0 | si | 732 | 189 |
| 16 | Theme TwentyOne | 286 | 186 | 1 | 1% | 116 | 51 | 54 | 23/76 | 1 | NO | 599 | 209 |
| 17 | Module AI | 263 | 126 | 4 | 6% | 65 | 51 | 55 | 32/296 | 1 | si | 512 | 195 |
| 18 | Module Fixcity | 477 | 97 | 3 | 22% | 67 | 1 | 1 | 84/185 | 56 | si | 460 | 96 |
| 19 | Module Blog | 157 | 95 | 4 | 3% | 34 | 34 | 26 | 30/63 | 0 | si | 338 | 215 |
| 20 | Module Comment | 173 | 65 | 4 | 5% | 25 | 23 | 25 | 42/211 | 1 | si | 295 | 171 |
| 21 | Module Seo | 205 | 112 | 1 | 92% | 6 | 6 | 30 | 27/321 | 0 | si | 220 | 107 |
| 22 | Theme Meetup | 36 | 13 | 1 | 97% | 0 | 0 | 0 | 13/61 | 0 | si | 26 | 72 |
| 23 | Module Mobile | 16 | 3 | 1 | 12% | 7 | 0 | 0 | 0/6 | 0 | si | 12 | 75 |

Note di lettura:

- `User` pesa per l'indice generato `INDEX.md` (6.277 link) e `index.md` (3.133): oltre meta' dei link e' rotta. Rigenerarli dall'albero vale piu' di correggerli a mano.
- `Xot` ha 2.655 copie di contenuto e 2.452 file in radice: e' il perimetro dove la marcatura `superseded` toglie piu' rumore.
- `Fixcity` ha un debito contenuto ma 56 lock vecchi (tutti tra 10,8 e 11,7 giorni): residuo di un batch interrotto.
- `Barthelemy` e `TwentyOne` non hanno `stories/`.

## Schemi sistemici trovati

1. **Iniettore di frontmatter difettoso.** 4.878 file hanno nel corpo, dopo un `---` (a volte dopo una riga vuota), le 8 righe del template generico. Non contengono informazione. Origine: passaggio in blocco del 2026-09-26 (mtime 22:49-22:51 nei piloti).
2. **Scaffold replicato.** 182 nomi in almeno 10 perimetri, 5.284 file; 4.852 file hanno meno di 500 byte. Molti sono stub con `canonical: ../../docs/shared-components/...`, cartella che non esiste (34 puntatori solo in Seo).
3. **Template wiki a profondita' sbagliata.** Link come `../../docs/wiki/...` che dovevano avere due `../` in piu': il bersaglio esiste ed e' correggibile in modo deterministico.
4. **Regole in conflitto sul nome dell'indice.** `docs-index-file.md` impone `index.md`; `theme-module-docs-readme-mandatory.md` impone `README.md` e vieta `readme.md`; la prassi (22 perimetri su 23) e' `00-INDEX.md`; `index.md` e' l'indice piu' ricco in 12 perimetri su 23. In `docs/wiki/rules` ci sono 59 file di regole sui docs (naming, struttura, README, indici), in parte duplicati (`.md` e `.mdc`) e con soglie diverse per i `.md` in radice (4, poi 6, poi 5).
5. **Indici invecchiati.** Un indice scritto il 2026-09-26 (Seo) elencava file poi rinominati dalla normalizzazione (date tolte dai nomi) e ne ignorava 15 nuovi: gli indici manuali invecchiano in giorni.
6. **Nomi con data** (540 file) in contrasto con la regola 020, e report generati mai rigenerati (es. un report del 2025-10-01 ancora presentato come attuale).

## I 10 interventi successivi piu' redditizi

Ordinati per debito rimosso rispetto al rischio. Tutti usano `docs-organizer.py` (dry-run di default, `--fix` per applicare), modulo per modulo, senza spostare ne' rinominare.

1. **Riparare il frontmatter su larga scala.** `docs-organizer.py frontmatter <docs> --fix`: toglie i blocchi spurii (lossless, riconosce i blocchi di codice), aggiunge `status`, `updated`, `tags`, `qmd` mancanti, crea il frontmatter dove assente. Ordine: Xot (1.339 difettosi), Notify (921), User (819), Sixteen (643), Geo (591), Barthelemy (551), Activity (544), UI (395). Rischio: basso; verificare con `git diff -U0` che le righe rimosse siano solo del template; i 36 non chiusi vanno a mano.
2. **Marcare `superseded` le copie di contenuto.** `docs-organizer.py dups <docs> --fix --index-canonical index.md` (7.238 copie; Xot 2.655, User 1.310, Notify 838, Geo 390, UI 334). Canonico: nome kebab minuscolo, poi minuscolo con underscore, poi maiuscolo; `README.md` batte `readme.md`. Solo gruppi con lo stesso nome normalizzato o nella stessa cartella. Rischio: medio-basso (la scelta del canonico); rivedere l'elenco in dry-run per modulo.
3. **Un indice canonico per `docs/`.** Prima fondere nell'`index.md` i link che esistono solo negli altri indici, poi marcare gli altri `superseded` con nota visibile (63 indici attivi in eccesso). Per `Cms`, `Sixteen`, `AI`, `Activity`, `Geo`, `Lang`, `Media`, `Notify`, `Xot` l'`index.md` esiste ed e' il piu' ricco; `User` ha `INDEX.md` (455 KB) piu' ricco di `index.md`. Rischio: medio, controllare con `rg` chi consuma i nomi (QMD, script, altri agenti) prima.
4. **Riparare i link in modo verificato.** `docs-organizer.py links <docs> --fix --annotate`: corregge solo case, profondita' di `../` e prefisso `./docs/` quando il bersaglio esiste, scarta i rinvii a se stessi o alla copia INDEX/index, annota con `_(target mancante, verificato AAAA-MM-GG)_` quelli inesistenti. Ordine: User (9.157 rotti), UI (1.382), Xot (968), Cms (484), Notify (394). Fare dopo l'intervento 3: gli indici generati si rigenerano, non si rattoppano. Rischio: basso.
5. **Fermare e governare lo scaffold replicato.** Trovare il generatore che ha scritto 182 nomi in 10+ perimetri (mtime di massa 2026-09-26) e fermarlo; poi un solo canonico condiviso (es. `docs/wiki/`) e nei perimetri solo stub `status: stub` con `canonical` esistente. Rischio: medio: richiede scegliere il destinatario dei 34+ puntatori `shared-components`, decisione del proprietario.
6. **Governare le collisioni di nome senza rinominare.** 1.621 collisioni di solo case e 3.119 gruppi di varianti: con l'intervento 2 restano tutte marcate. Un rename (`git mv` con riscrittura dei link) va in una story per modulo, forward-only, dopo che i link sono verificati. Rischio: alto se fatto ora; basso se solo marcato.
7. **Fermare la crescita della radice.** Regola e controllo: nessun nuovo `.md` in radice di `docs/` salvo `README.md` e indice; la radice oggi pesa 10.518 file (Xot 2.452, Notify 1.552, User 1.546). Il controllo fallisce se `root_md` aumenta rispetto all'ultima misura. Nessuno spostamento. Rischio: basso.
8. **Chiudere i lock vecchi uno per uno.** 93 `.md.lock` oltre 7 giorni (Fixcity 56, Sixteen 19, Xot 9): per ciascuno `bashscripts/lock/check.sh`, `git status` pulito sul file e agente non attivo, poi `unlock.sh` singolo. Mai in blocco. Rischio: basso-medio (un agente vivo).
9. **Rendere affidabile la rilevazione dello stantio.** Estendere `bashscripts/tools/wiki-hygiene.py` perche' risolva anche dalla radice del modulo (oggi falsi positivi su `app/...`), poi un passaggio settimanale che propone note `> Nota AAAA-MM-GG (docs-audit)` e `status: stale` (comando `docs-organizer.py stale`). Rischio: basso.
10. **Cancello continuo.** `docs-organizer.py audit` dentro `bashscripts/docs/second-brain-healthcheck.sh` (settimanale): il debito per perimetro non deve salire, ogni nuovo `.md` ha frontmatter completo, nessun nuovo indice attivo oltre il canonico, `stories/` presente dove ci sono story. Rischio: basso; soglie da concordare con il proprietario.

## Regole di ordine proposte

Coerenti con `docs/wiki/rules` e con la policy delle story: nessuna cancellazione o rinumerazione, story collegate o marcate `superseded`.

1. **Un solo indice canonico per `docs/`: `index.md`** (nome previsto da `docs-index-file`). Se per storia l'unico indice ha un altro nome (es. `00-INDEX.md` in Meetup) resta quello, e `canonical: true` nel frontmatter lo identifica. Gli altri indici: `status: superseded` + `superseded_by` + nota visibile. `README.md` e' l'ingresso (regola README obbligatorio), non un secondo indice; `readme.md` minuscolo e' una copia da marcare.
2. **Frontmatter con sei chiavi** (`title`, `type`, `status`, `updated`, `tags`, `qmd`) su ogni `.md`, come da `wiki-markdown-frontmatter-mandatory`. Vocabolario di `status`: `active`, `stub`, `superseded`, `stale`, `draft`; per le story `done`, `in-progress`.
3. **Mai cancellare, spostare, rinominare.** Un duplicato diventa `status: superseded` con `superseded_by` (relativo, verificato). Una story non si rinumera: si collega o si marca `superseded`.
4. **Un nuovo file va in una sottocartella tematica** (`stories/`, `wiki/`, `reports/`, `adr/`, `howto/`); la radice tiene `README.md` e l'indice. Nomi kebab-case minuscoli, senza date (regola 020), nessuna variante di case o separatore.
5. **`raw/` e' immutabile; `stories/` segue la policy story; i log sono append-only.** Gli strumenti li saltano.
6. **Link relativi sempre verificati** con `docs-organizer.py links <docs>` prima del commit. Un link senza bersaglio si annota, non si lascia in silenzio.
7. **Uno stub esiste solo se il suo `canonical` esiste.** Altrimenti il file e' `stale` con nota datata.
8. **Il contenuto stantio non si riscrive alla cieca:** `status: stale` e nota `> Nota AAAA-MM-GG (docs-audit): motivo.` sotto il frontmatter; la riscrittura passa da una story.
9. **Prima di toccare un file:** nessun `.lock` recente (24 ore), file non modificato negli ultimi 10 minuti, rilettura subito prima di scrivere. Un lock vecchio si valuta singolarmente.
10. **Una story per perimetro** (`docs/stories/AAAA-MM-GG-docs-organization-*.story.md` nel modulo o tema gia' esistente) e una memoria nel second brain a fine giro.

## Risultati dei piloti

| Perimetro | file | fm completo | blocchi spurii | copie non marcate | indici attivi | link rotti negli indici | debito |
|-----------|-----:|------------:|---------------:|------------------:|--------------:|------------------------:|-------:|
| Seo prima | 203 | 1 | 55 | 29 | 4 | 56/304 | 408 |
| Seo dopo | 205 | 188 | 6 (tutti in `raw/`) | 6 (copie in `raw/`) | 1 | 27/321 (inesistenti, annotati) | 220 |
| Meetup prima | 34 | 1 | 0 | 0 | 1 | 20/49 | 60 |
| Meetup dopo | 36 | 34 | 0 | 0 | 1 | 13/61 (inesistenti, annotati) | 26 |

Seo: 187 file toccati, indice canonico `index.md` riparato e completo (205 file raggiungibili), 25 file `superseded`, 33 `stub`, 7 `stale`, 2 `draft`. Meetup: 32 file modificati piu' `README.md` e story, indice canonico `00-INDEX.md` riscritto, 10 `stub`, 6 `stale`. Dettagli e decisioni nelle due story. Niente cancellato, spostato o rinominato; `raw/` e story esistenti intatti.

## Appendice: lock `.md.lock` piu' vecchi di 7 giorni (non rimossi)

L'eta' viene dal campo `LOCKED at` quando c'e', altrimenti dall'mtime. Totale 93.

**Module AI** (1)

```
phpstan-fixes-history.md.lock  (11.7 g)
```

**Module Activity** (1)

```
tasks/003-activity-analytics-dashboard.md.lock  (11.7 g)
```

**Module Cms** (1)

```
homepage-parity-notes.md.lock  (11.3 g)
```

**Module Comment** (1)

```
wiki/log.md.lock  (12.7 g)
```

**Module Fixcity** (56)

```
STORY-401-ticket-timeline-audit-trail.md.lock  (11.7 g)
STORY-402-rich-questionnaire-system.md.lock  (11.7 g)
STORY-403-notification-system.md.lock  (11.7 g)
STORY-404-dashboard-analytics.md.lock  (11.7 g)
bmad/README.md.lock  (10.9 g)
bmad/gap-analysis.md.lock  (10.8 g)
bmad/stories/020-merge-conflict-resolution.story.md.lock  (11.4 g)
bmad/stories/021-phpstan-errors-fixcity.story.md.lock  (11.4 g)
bmad/stories/022-auditcoverage-gitignore.story.md.lock  (11.4 g)
bmad/stories/023-wizard-responsive-uiux.story.md.lock  (11.4 g)
bmad/stories/024-basepolicy-layer.story.md.lock  (11.4 g)
bmad/stories/025-usercontract-pattern.story.md.lock  (11.4 g)
bmad/stories/STORY-001-citizen-create-ticket.md.lock  (11.4 g)
bmad/stories/STORY-002-assign-ticket.md.lock  (11.4 g)
bmad/stories/STORY-003-system-notification.md.lock  (11.4 g)
bmad/stories/STORY-004-admin-dashboard.md.lock  (11.4 g)
bmad/stories/STORY-005-citizen-report-flow.md.lock  (11.4 g)
bmad/stories/STORY-006-pa-dashboard-flow.md.lock  (11.4 g)
bmad/stories/STORY-008-citizen-my-tickets.md.lock  (11.4 g)
bmad/stories/STORY-009-pa-operator-flow.md.lock  (11.4 g)
bmad/stories/STORY-010-admin-config-flow.md.lock  (11.4 g)
bmad/stories/STORY-011-comment-system-flow.md.lock  (11.4 g)
bmad/stories/STORY-020-ticket-resource-filters.md.lock  (11.4 g)
bmad/stories/STORY-021-operator-assignment.md.lock  (11.4 g)
bmad/stories/STORY-022-supervisor-escalation.md.lock  (11.4 g)
bmad/stories/STORY-026-bmad-actor-workflows.md.lock  (11.4 g)
bmad/stories/STORY-026-rbac-operator-supervisor.md.lock  (11.4 g)
bmad/stories/STORY-037-public-ticket-tracking-page.md.lock  (11.4 g)
bmad/stories/STORY-499-pa-ticket-assignment.md.lock  (11.7 g)
bmad/stories/STORY-500-vertical-slice-verification.md.lock  (11.7 g)
bmad/stories/STORY-501-foreign-id-for-migrations.md.lock  (11.7 g)
bmad/stories/STORY-502-policy-base-hierarchy.md.lock  (11.7 g)
bmad/stories/STORY-503-direct-action-over-resource.md.lock  (11.7 g)
bmad/stories/STORY-504-dashboard-xotbase-dashboard.md.lock  (11.7 g)
bmad/stories/STORY-505-second-brain-canonicalization.md.lock  (11.7 g)
bmad/stories/STORY-506-docs-redundancy-consolidation.md.lock  (11.7 g)
bmad/stories/STORY-507-actor-flows-and-user-journeys.md.lock  (11.4 g)
bmad/stories/STORY-521-demo-ticket-owner-assignment.md.lock  (10.8 g)
bmad/stories/STORY-FIXCITY-DIRECT-ACTION-OVER-RESOURCE.md.lock  (11.4 g)
bmad/stories/STORY-XXX-complete-implementation-bmad.md.lock  (11.4 g)
bmad/ui-ux-public-guest-correction-plan-2026-09-27.md.lock  (11.0 g)
phpstan-fix-plan.md.lock  (11.7 g)
phpstan-immediate-fixes.md.lock  (11.7 g)
phpstan-level-10-fixes.md.lock  (11.7 g)
ponytail-audit-over-engineering.md.lock  (11.7 g)
r3-one-migration-per-model.md.lock  (11.7 g)
redundancy-audit.md.lock  (11.7 g)
second-brain.md.lock  (11.7 g)
wiki/comparisons/redundancy-overview.md.lock  (11.7 g)
wiki/concepts/ticket-subscribers-vs-comment-subscribers.md.lock  (11.5 g)
wiki/concepts/user-journey-map.md.lock  (11.3 g)
wiki/log.md.lock  (10.9 g)
wiki/overviews/fixcity-module.md.lock  (11.7 g)
wiki/troubleshooting/migration-foreign-key-string-to-bigint.md.lock  (11.7 g)
wizard-architecture.md.lock  (11.7 g)
wizard-system.md.lock  (11.7 g)
```

**Module Geo** (2)

```
claude/framework-specifics.md.lock  (11.7 g)
rules/filament-class-extension-rules.md.lock  (11.7 g)
```

**Module User** (1)

```
bmad/guest-login-italian-translation.md.lock  (11.3 g)
```

**Module Xot** (9)

```
architecture-best-practices.md.lock  (11.7 g)
filament-consolidated.md.lock  (11.7 g)
filament-extension-rules-complete.md.lock  (11.7 g)
filament/standalone-vs-resource-pages.md.lock  (11.7 g)
migration-patterns.md.lock  (11.7 g)
migrations.md.lock  (11.7 g)
phpstan-code-quality-guide.md.lock  (11.7 g)
wiki/concepts/migration-foreign-id-for.md.lock  (11.7 g)
xotbase-extension-rules.md.lock  (11.7 g)
```

**Theme Barthelemy** (1)

```
shared-components/migrations.md.lock  (11.7 g)
```

**Theme Sixteen** (19)

```
architecture/README.md.lock  (14.0 g)
architecture/css-filename-english-naming.md.lock  (14.0 g)
architecture/fo-pa-tokens-uniformity.md.lock  (14.0 g)
architecture/wizard-step-visibility.md.lock  (14.0 g)
bmad/fixcity-ui-ux-runtime-audit.md.lock  (11.1 g)
bmad/public-ticket-listing-visual-contract.md.lock  (11.0 g)
bmad/stories/STORY-SIXTEEN-PUBLIC-LISTING-RUNTIME-DEMO.md.lock  (11.0 g)
design-comuni/TICKET-CREATION-WIZARD.md.lock  (14.0 g)
folio-page-pattern.md.lock  (14.0 g)
wiki/concepts/ai-harness-theme-sixteen.md.lock  (14.0 g)
wiki/concepts/fo-folio-links-multilingua.md.lock  (14.0 g)
wiki/concepts/fo-pa-tokens-uniformity.md.lock  (14.0 g)
wiki/concepts/geo-map-marker-civic-pin-theme-boundary.md.lock  (14.0 g)
wiki/concepts/geo-map-popup-leaflet-boundary.md.lock  (14.0 g)
wiki/concepts/livewire-alpine-esm-order.md.lock  (14.0 g)
wiki/index.md.lock  (14.0 g)
wiki/log.md.lock  (14.0 g)
wiki/rules/header-authenticated-parity.md.lock  (14.0 g)
wiki/rules/no-italian-css-filenames.md.lock  (14.0 g)
```

**Theme TwentyOne** (1)

```
redundancy-audit-2026-05-21.md.lock  (14.0 g)
```


## Appendice: comandi

```bash
python3 bashscripts/tools/docs/docs-organizer.py audit --out /tmp/docs-audit.json
python3 bashscripts/tools/docs/docs-organizer.py frontmatter laravel/Modules/<M>/docs [--fix]
python3 bashscripts/tools/docs/docs-organizer.py dups laravel/Modules/<M>/docs --index-canonical index.md [--fix]
python3 bashscripts/tools/docs/docs-organizer.py links laravel/Modules/<M>/docs [--fix] [--annotate]
python3 bashscripts/tools/docs/docs-organizer.py stale laravel/Modules/<M>/docs --file REL --reason "..." [--status draft|stub] [--fix]
```

Ordine consigliato per modulo: `dups`, poi `frontmatter`, poi `links`, poi `stale`. Con passate in sequenza sullo stesso perimetro usare l'opzione globale `--busy-minutes 0` prima del sottocomando.
