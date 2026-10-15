---
id: dcp-210
title: "DCP-210: le ore di lavoro dei ticket non si possono registrare (ticket_hours.user_id intero, utenti UUID)"
type: story
epic: dc-parity-v2
status: ready-for-decision
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "ticket_hours user_id bigint uuid varchar 36 ore di lavoro ticket DMO schema migrazione test rossi TicketHour"
related: [dcp-103, dcp-200]
tags: [schema, database, test, decisione]
---

# DCP-210: le ore di lavoro dei ticket non si possono registrare

## Fase BMAD
Analyze e Plan. **Nessuna modifica allo schema finche' non c'e' la decisione DEC-35** del registro [DCP-200](./dcp-200-registro-unico-delle-decisioni-aperte.story.md): cambiare il tipo di una colonna del database dei dati richiede approvazione, anche se la tabella e' vuota.

## Scopo ricostruito
`ticket_hours` doveva registrare le ore di lavoro che un operatore dedica a un ticket (colonne `ticket_id`, `user_id`, `value` in ore, `comment`, `activity_id`). Oggi serve a due cose nel codice: `Ticket::hours()` somma `value` (`Ticket.php:360`) e `Profile::hours()` raccoglie le ore di un utente (`Profile.php:156-159`); la relazione sta in `HasTicketRelations.php:86-92`. Nessuna schermata scrive un `TicketHour`: la funzione e' rimasta a meta' dalla versione precedente del prodotto (le migrazioni che la creavano sono solo in `database/migrations/_bak/*.merged`).

## Prova (misurata il 2026-10-15, sola lettura)
- `fixcity_data.ticket_hours.user_id` e' `bigint(20) unsigned`; `ticket_id` pure. `created_by`, `updated_by`, `deleted_by` della stessa tabella sono invece `uuid`.
- `fixcity_user.users.id` e' `varchar(36)` (UUID). Un `user_id` intero non puo' contenere un id utente: l'inserimento fallisce con `Data truncated for column 'user_id'`, `Incorrect integer value` o `Out of range value`.
- La tabella ha **0 righe**: nessun dato da migrare.
- Il docblock di `TicketHour` dice `@property int $user_id`, coerente con lo schema sbagliato e non con gli utenti.
- Effetto sui test: nella suite intera di Fixcity (990 s, DB di prova) **20 dei 27 rossi** vengono da questa sola causa (`TicketHourTest`, i test di tempo di `Ticket`, la relazione `Profile`/`User` sulle ore, il test sui messaggi e ore dei ticket DMO). Dettaglio in [DCP-103](./2026-10-13-dcp-103-test-rossi-residui-modulo-fixcity.story.md).

## Decisione da prendere (DEC-35)
| Opzione | Cosa comporta | Quando ha senso |
|---|---|---|
| (a) Tenere la funzione e correggere il tipo | migrazione in avanti `user_id` -> `string(36)` (e verifica di `ticket_id` contro `tickets.id`); docblock `@property string`; la factory gia' usa `User::factory()` | le ore di lavoro servono davvero ai ticket DMO |
| (b) Togliere la funzione | rimuovere model, factory, `hours()` in `Ticket` e `Profile`, la somma a `Ticket.php:360` e i test (non si cancellano test senza approvazione); la tabella resta o si rimuove con migrazione | nessuno le usa e non e' in programma una schermata |
Proposta: (a) solo se esiste un caso d'uso scritto, altrimenti (b). La scelta spetta al titolare del prodotto.

## Compiti
Comuni (si possono fare subito, sono sola lettura):
- [ ] Chiedere al titolare del prodotto se le ore di lavoro sono richieste per i ticket DMO.
- [ ] Verificare se altre tabelle del database dei dati hanno lo stesso difetto e se FixCity le usa: oggi risultano con `user_id` intero `comments`, `contacts` (int 11), `posts`, `post_views`, `translations`, `upvote_downvotes`, `ai_messages`, `ai_tool_logs` (bigint). **Non e' provato che siano usate.** Per ciascuna: cercare i punti di scrittura nel codice prima di giudicarla.
- [ ] Controllare il database di produzione prima di scrivere la migrazione (non esaminato).
Se (a):
- [ ] Test rosso prima: un `TicketHour` con un utente UUID deve salvarsi (i test esistenti lo fanno gia').
- [ ] Migrazione forward-only con `hasColumn()` e controllo del tipo, nello stile delle migrazioni del modulo; poi aggiornare il docblock.
- [ ] Rilanciare la suite di Fixcity: i 20 rossi devono diventare verdi senza toccare le asserzioni.
Se (b):
- [ ] Elencare i punti d'uso (3) e i test che cadono; chiedere approvazione per rimuovere i test.
- [ ] Rimuovere in un commit in avanti e rilanciare la suite.

## Criteri di accettazione
- [ ] DEC-35 ha un esito datato nel registro.
- [ ] La suite di Fixcity non ha piu' rossi con `ticket_hours.user_id`.
- [ ] Nessun cambio di schema senza approvazione scritta.

## Come si riproduce la misura
Schema: `information_schema.columns` su `ticket_hours` e su `users.id` (database `fixcity_data` e `fixcity_user`). Test: `bash bashscripts/tools/pest-scratch-db.sh Modules/Fixcity/tests` con `DB_DATABASE_LIMESURVEY=` e `APP_CONFIG_CACHE=/nonexistent/config.php`; leggere la riga `Tests:`, perche' lo script esce 0 anche con test rossi.

## Non verificato
Database di produzione; uso delle altre tabelle con `user_id` intero; se esista una schermata in un altro modulo che scrive ore.
