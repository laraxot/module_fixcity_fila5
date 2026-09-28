---
qmd: "definition of done"
title: "Definition of Done — FixCity v1"
type: standard
status: proposed
tags: [completamento, dod, definition-of-done, qualita, rilascio]
created: 2026-09-26
updated: 2026-09-26
owner: opencode-space-bunny
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/497"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/498"
related:
  - ./stories/STORY-495-completion-gap-verified.md
  - ./prd-base_fixcity_fila5-2026-05-28.md
  - ../wiki/rules/post-edit-quality-gate.md
  - ../wiki/memories/03-quality-gates-workflow.md
---

# Definition of Done — FixCity v1

## Perché questo documento esiste

Il PRD ha **44 AC** e nessuna spunta. Non esiste nessun criterio di «fatto» per il
rilascio. Risultato: dopo 4 mesi di sviluppo nessuno può dire se il progetto è
completato, e ogni sessione ri-litiga la stessa domanda.

Una DoD serve a una cosa sola: **rendere «completato» una affermazione verificabile**.
Se un criterio non può essere eseguito da un comando, non è un criterio: è un desiderio.

## Lo stato onesto di oggi

| Dimensione | Stato | Misura |
|------------|-------|--------|
| Funzionalità | 9/10 FR implementati | vedi STORY-495 §2 |
| **Verifica funzionale** | **0/10** | Pest bloccato: 139 test falliscono su credenziali |
| Qualità statica | 🟢 | `phpstan analyse Modules` → `[OK] No errors`; `pint --test` → 0 |
| Parity UI (FR-010) | ⚪ | nessuno strumento esiste |
| Tracciabilità | 0/44 AC | PRD mai aggiornato |
|Igiene del repository | 🟡 | 418 MB duplicati, 72 `.md` vuoti, 1 rebase bloccato |

**Il progetto non è «quasi finito»: è implementato ma non verificato.** La distanza fra
questo e «finito» è quasi interamente lavoro di verifica, non di codice.

## Criteri di completamento

Ogni criterio è **eseguibile**. Lo stato di ogni riga è misurato il 2026-09-26.

### A — Salute del repository (precondizione, non negoziabile)

| # | Criterio | Comando | Stato |
|---|----------|---------|-------|
| A1 | Nessun rebase/merge in corso | `ls laravel/Modules/*/.git/rebase-merge` → vuoto | ❌ **`Cms` bloccato, 663 conflitti** |
| A2 | Nessun marker di merge | `find Modules Themes … grep -E '^(<<<<<<<\|=======\|>>>>>>>)'` | ✅ 0 |
| A3 | Nessun file spazzatura | `bash bashscripts/quality-gates/audit-garbage-filenames.sh` | ❌ 75 |
| A4 | Nessun albero duplicato | nessun path contiene sé stesso | ❌ `Sixteen/Sixteen` 418 MB |
| A5 | `gitmodules.ini` riflette il disco | `comm` su `disk.txt` / `decl.txt` | ❌ mancano `Barthelemy`, `Meetup` |
| A6 | Nessun file temporaneo | `find … -name '*.bak' -o -name '*.old' -o -name '*~HEAD'` | ❌ 25 |
| A7 | `docs/sprint-status.yaml` è YAML valido | `python3 -c "import yaml;yaml.safe_load(open('docs/sprint-status.yaml'))"` | ❌ manca `sprints:` |

### B — Gate automatici (nessuno può essere saltato)

| # | Criterio | Comando | Stato |
|---|----------|---------|-------|
| B1 | PHPStan level max a zero | `cd laravel && ./vendor/bin/phpstan analyse Modules --no-progress --memory-limit=-1` | ✅ **`[OK] No errors`** |
| B2 | Pint pulito sullo scope | `./vendor/bin/pint --test <scope>` | ✅ 0 issue |
| B3 | Nessun baseline | `test -f phpstan-baseline.neon` → assente | ✅ |
| B4 | Nessun silenzio aggiunto | `@phpstan-ignore` in `.php` fuori `docs/`: **234**, nessuno nuovo nella wave | ✅ |
| B5 | PHPMD | `tools/phpmd.sh` | ⚪ `SKIP_ENV=3` — `laravel/tools/` non esiste |
| B6 | Pest verde | `./vendor/bin/pest --no-coverage` | ❌ **139 failed / 1 passed** |

### C — Verifica funzionale (il vero buco)

| # | Criterio | Comando | Stato |
|---|----------|---------|-------|
| C1 | Il DB di test è raggiungibile con diritti | connessione a `fixcity_data_test` | ❌ `Access denied` |
| C2 | La suite passa | `./vendor/bin/pest --no-coverage` | ❌ dipende da C1 |
| C3 | Il wizard creazione è percorso end-to-end | test Playwright `Modules/Fixcity` | ❌ non eseguibile senza C1 |
| C4 | `.env.testing` non ha chiavi duplicate | una sola `DB_DATABASE`, una sola `DB_USERNAME` | ❌ **duplicate** |

### D — Requisiti (tracciabilità)

| # | Criterio | Stato |
|---|----------|-------|
| D1 | Ogni FR del PRD ha evidenza o gap dichiarato | ✅ STORY-495 §2 |
| D2 | Gli AC del PRD sono spuntati **con prova** | ❌ 0/44 |
| D3 | FR-010 ha uno strumento di parity | ❌ `html-structure-compare` assente |
| D4 | Ogni FR ha almeno un test automatico che lo copre | ❌ non verificabile senza C2 |

### E — Documentazione

| # | Criterio | Stato |
|---|----------|-------|
| E1 | Second Brain aggiornato su ogni decisione | ✅ |
| E2 | Nessun `.md` di scratch da 0 byte tracciato | ❌ 72 |
| E3 | `docs/sprint-status.yaml` riflette la realtà | ❌ corrotto |
| E4 | Un `.md` vuoto non è indicizzato | ⚠️ 529 file `.md` contengono `@phpstan-ignore`: i campioni di codice dentro la doc rendono il debito illeggibile |

## Sintesi

| Area | Criteri | Passati | Bloccanti |
|------|---------|---------|-----------|
| A — salute repository | 7 | 0 | 7 |
| B — gate automatici | 6 | 4 | 2 (1 env) |
| C — verifica funzionale | 4 | 0 | 4 |
| D — requisiti | 4 | 1 | 3 |
| E — documentazione | 4 | 1 | 3 |
| **Totale** | **25** | **6** | **19** |

**6/25.** E dei 19 bloccanti, **7 dipendono da un umano** (C1, A1, A3, A4, A5, E2, E3) e
non da codice.

## Rivalutazione evidenze — 2026-09-27

La tabella sopra fotografa il 2026-09-26 e resta come storico. Le verifiche successive
correggono alcune misure, ma non dimostrano ancora il completamento del rilascio:

| Criterio | Evidenza aggiornata | Stato attuale |
|----------|---------------------|---------------|
| A1 — rebase Cms | Nessun `rebase-merge`/`rebase-apply`; indice senza conflitti (`git ls-files -u` = 0). | ✅ |
| A2 — marker | Quality gate wiki: nessun marker Markdown. | ✅ nel perimetro del gate |
| A3 — nomi spazzatura | `audit-garbage-filenames.sh --fix-list`: nessun risultato dopo archiviazione forward-only. | ✅ |
| A4 — albero Sixteen | `Themes/Sixteen/Sixteen` esiste ancora (circa 418 MB). | ❌ |
| A7 — YAML sprint | `docs/sprint-status.yaml` è valido; il file è lockato e non è stato modificato in questa verifica. | ✅ validità / stato tracker non aggiornato |
| B1 — PHPStan | `php -d memory_limit=2048M vendor/bin/phpstan analyse Modules --no-progress` → `[OK] No errors`. | ✅ |
| B6/C2 — suite FixCity | SQLite isolato, costruito con il builder del progetto: 380 test / 1.634 asserzioni passano. | ✅ FixCity |
| C1 — MySQL di test | Il precedente tentativo era `Access denied`; SQLite non prova grant o credenziali MySQL. | ❌ MySQL |
| C3 — percorso browser | Chromium/Firefox e Playwright non sono disponibili nell'ambiente verificato. | ❌ non verificato ora |
| C4 — `.env.testing` | `DB_CONNECTION`, `DB_DATABASE` e `DB_USERNAME` risultano uniche; nessun valore è stato letto o cambiato. | ✅ |

La suite verde dimostra i test FixCity sul database SQLite temporaneo, non l'intera
suite applicativa, l'accesso MySQL, l'accodamento in produzione o i flussi UI nel
browser. Restano da chiudere la prova end-to-end browser, la configurazione MySQL, i
dati legali ufficiali dell'ente e la tracciabilità verificata dei 44 AC. Nessun dato
ufficiale o segreto d'ambiente è stato inventato o modificato.

`docs/sprint-status.yaml`, `docs/chat/INDEX.md` e `docs/wiki/log.md` erano lockati:
la rivalutazione è append-only e accompagnata da una nota chat dedicata. `qmd` non è
installato, quindi la nota non può essere indicizzata in questa sessione.

## La sequenza che sblocca di più

Non è un elenco di 19 voci: è una catena, e ogni anello sblocca il successivo.

```
A1  rebase Cms                     ─┐
C1  GRANT su fixcity_data_test     ─┼─→ C2 suite verde ─→ C3 wizard e2e ─→ D2/D4 AC con prova
                                    ─┘
B5  phpmd.phar                     ──→ (nessun blocco downstream)
D3  strumento di parity            ──→ D3 verificabile
A4  git rm --cached Sixteen/       ──→ 418 MB recuperati, A4 verde
E3  sprints: in sprint-status      ──→ A7 verde, E3 verde
```

**Il collo di bottiglia è C1.** Un solo `GRANT` sblocca 4 criteri (C1, C2, C3, D4) e
abilita la prova di 8 dei 10 FR. È il intervento col miglior rapporto valore/sforzo
dell'intero progetto, e costa una riga.

## Regole di questa DoD

1. **Un criterio che non gira è un desiderio.** Se serve un umano, lo dichiariamo come
   tale invece di scrivere «verificato a mano».
2. **Uno `SKIP_ENV` non è un verde.** PHPMD e Pest assenti per ambiente restano ❌ o
   ⚪, mai ✅.
3. **Il numero va dichiarato come è stato ottenuto.** «234 `@phpstan-ignore` in codice»,
   non «2116»: il primo è misurato con `--include='*.php'`, il secondo no.
4. **Forward-only.** Si corregge appendendo, non riscrivendo la storia di un documento.
5. **La DoD si aggiorna quando la realtà cambia**, non quando conviene.

## Change log

- **2026-09-26** — creazione. 25 criteri, 6 passati, 19 bloccanti. Collo di bottiglia:
  credenziali MySQL.
