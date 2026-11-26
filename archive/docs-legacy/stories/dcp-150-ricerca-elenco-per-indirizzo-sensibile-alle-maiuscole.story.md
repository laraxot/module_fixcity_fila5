---
id: dcp-150
title: "DCP-150: la ricerca dell'elenco pubblico per indirizzo distingueva le maiuscole"
type: story
epic: dc-parity-v2
status: done
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "ricerca elenco pubblico indirizzo maiuscole location address json_unquote collation binaria LIKE mariadb ApplyPublicTicketFiltersAction"
related: [dcp-104, dcp-148, dcp-149]
---

# DCP-150: ricerca per indirizzo senza distinzione di maiuscole

## Fase BMAD
Correzione (dev) di un difetto di FUNZIONE dell'elenco pubblico `/it/tickets`, trovato da due test rossi su MariaDB segnalati dall'agente DCP-148 e confermato sul server.

## Difetto misurato (server di sviluppo, MariaDB, 2026-10-14 22:45 UTC)
Ricerca nell'elenco pubblico (`/it/tickets?q=...&view=list`, 11 segnalazioni di prova con indirizzo "Via Solferino - 50100 Firenze"):
| q | prima | dopo |
|---|---|---|
| `Firenze` | 11 | 11 |
| `firenze` | **0** | 11 |
| `FIRENZE` | **0** | 11 |
| `solferino` | 1 (solo dal titolo) | 3 |
| `dalmazia` | 0 | 1 |
Il commento del codice prometteva "senza distinzione di maiuscole", ma valeva solo per titolo e descrizione. Un cittadino che scrive il nome della via in minuscolo non trovava la segnalazione sul proprio indirizzo, cioe' l'uso principale della ricerca (vedere se il problema e' gia' segnalato).
**Causa**: `location->address` e' un campo JSON; su MariaDB `json_unquote(json_extract(...))` restituisce una stringa con collation binaria (`utf8mb4_bin`) e `LIKE` distingue le maiuscole. SQLite (usato dai test di default) no: per questo i test erano verdi in SQLite e rossi nel DB di scratch MariaDB.
Stesso filtro per la mappa: `GET /api/tickets/geojson?q=firenze` ora restituisce 11 feature (prima 0), perche' `ApplyPublicTicketFiltersAction` e' l'unico punto di verita' di elenco e mappa.

## Correzione
`Modules/Fixcity/app/Actions/ApplyPublicTicketFiltersAction.php`: confronto `LOWER(<indirizzo>) like ?` con il termine in minuscolo (`mb_strtolower`). Due espressioni letterali scelte per driver (`sqlite` senza `json_unquote`, gli altri con): niente SQL costruito a pezzi (PHPStan richiede `literal-string` e non ho usato ne' ignore ne' cast).

## Prove
- I due test gia' scritti dall'agente DCP-104/113 erano ROSSI su MariaDB prima della correzione (`ListPublicTicketsActionTest` "searches title, description and address and ignores LIKE wildcards": atteso `['Cestino rotto']`, ottenuto `[]`) e sono diventati verdi: `ListPublicTicketsActionTest` + `TicketListPageTest` su DB scratch MariaDB: **22 passati, 140 asserzioni**; stesso `ListPublicTicketsActionTest` su SQLite: **13 passati**. Eseguiti con `bashscripts/tools/pest-scratch-db.sh` (conteggi del DB di sviluppo invariati).
- PHPStan (config unica) a zero sul file.
- curl sul server di sviluppo: tabella sopra; sentinelle `/it`, `/it/tickets`, `/it/tests/homepage`, `/it/tickets/confirmation`, `/it/tests/permessi-area-personale` a 200.
- Backup: `scratchpad/dcp124-backup/ApplyPublicTicketFiltersAction.before`.

## Lezione
I test di default girano su SQLite e nascondono differenze di collation con MariaDB: per le ricerche testuali eseguire sempre anche su DB scratch MariaDB. Due tentativi di PHPStan respinti (`orWhereRaw` e `Expression` con stringa composta) prima della forma letterale: la regola `literal-string` va rispettata riscrivendo, non aggirando.

## Non coperto
- Accenti: `LOWER()` di MariaDB e `mb_strtolower` trattano le maiuscole accentate, ma "perche'" e "perche" non coincidono (nessuna normalizzazione degli accenti): da decidere se serve (collation `utf8mb4_unicode_ci`).
- Ricerca per `display_name` della geocodifica (oggi si cerca solo `address`).
