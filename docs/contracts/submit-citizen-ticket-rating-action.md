---
title: "Contratto — SubmitCitizenTicketRatingAction"
type: contract
action: "Modules\\Fixcity\\Actions\\SubmitCitizenTicketRatingAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, valutazione, rating, autorizzazione, transazione]
created: 2026-11-26
updated: 2026-11-26
---

# SubmitCitizenTicketRatingAction

Sorgente: `app/Actions/SubmitCitizenTicketRatingAction.php`. Registra la valutazione del cittadino (da 1 a 5) su una segnalazione risolta, nel modulo Rating
(`rating_morph`). Chiamata da `Filament/Widgets/TicketCitizenRatingPromptWidget.php`. La valutazione e' dell'**autore della segnalazione** (il proprietario): i campi di
audit (`created_by`, `updated_by`) non danno diritto di votare.

## Input

- `Ticket $ticket`.
- `int $rating`.
- Implicito: l'utente autenticato (`Auth::user()`), che deve essere un `UserContract`.

## Output

- Il `Ticket` ricaricato (`refresh()`).

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-RATING-INV-1 | Il voto deve stare fra 1 e 5; fuori intervallo lancia `ValidationException` con errore sul campo `rating`. | `TICKET-RATING-INV-1` |
| TICKET-RATING-INV-2 | Serve un utente autenticato con identificativo; altrimenti `ValidationException` sul campo `auth`. | — (non dimostrato) |
| TICKET-RATING-INV-3 | Solo il proprietario (`owner_id` uguale all'utente) puo' votare; `created_by` e `updated_by` non bastano: altrimenti `ValidationException` sul campo `owner` e nessuna riga scritta. | `TICKET-RATING-INV-3` |
| TICKET-RATING-INV-4 | Si vota solo un ticket `resolved` o `closed`; negli altri stati `ValidationException` sul campo `status`. | — (non dimostrato: i test provano che il widget non propone il voto prima della risoluzione, non il rifiuto dell'Action) |
| TICKET-RATING-INV-5 | Un cittadino vota una sola volta per ticket: il secondo tentativo lancia `ValidationException` sul campo `rating` e la tabella conserva una sola riga con il primo voto. | `TICKET-RATING-INV-5` |
| TICKET-RATING-INV-6 | Controlli ed `INSERT` stanno in una transazione sulla connessione del ticket con la riga del ticket bloccata (`lockForUpdate`): due richieste concorrenti dello stesso utente non producono due voti. | — (non dimostrato) |
| TICKET-RATING-INV-7 | Il voto valido e' scritto in `rating_morph` con `model_type` = morph del ticket, `model_id`, `user_id` e `value`. | `TICKET-RATING-INV-7` |

## Effetti collaterali

In quest'ordine:

1. Validazione del voto e dell'utente (nessun accesso al database).
2. Transazione sulla connessione del ticket: `SELECT ... FOR UPDATE` del ticket.
3. Letture: voto gia' dato (`GetTicketCitizenRatingMorphAction`), stato, proprietario.
4. `EnsureTicketCitizenRatingDefinitionAction`: crea, se manca, la definizione di valutazione (una scrittura sul modulo Rating, al primo voto).
5. `INSERT` in `rating_morph`.

Nessuna notifica, nessuna voce di cronologia.

## Errori

- `ValidationException` con messaggi tradotti (`fixcity::ticket_citizen_rating.validation.*`), campo per campo: `rating` (fuori intervallo o gia' votato), `auth`, `status`, `owner`.
- Limite noto: la transazione e' sulla connessione del ticket, mentre `rating_morph` sta sulla connessione `rating` (altro database): il blocco serializza i voti dello stesso ticket ma
  non rende atomica la scrittura sul database del Rating. Se l'`INSERT` fallisce dopo la creazione della definizione, quest'ultima resta.
- Limite noto: l'unicita' "un voto per utente e ticket" e' nel codice (lettura sotto blocco), non in un indice univoco: le migrazioni di `rating_morph` in `Modules/Rating/database/migrations`
  (`2023_01_01_000005`, `2026_07_15_120003`, `2026_07_27_100005`) non dichiarano `unique` (controllo `rg -n "unique|primary"`).
- Limite noto: la definizione `fixcity-ticket-citizen-satisfaction` ha il titolo italiano nel codice (`EnsureTicketCitizenRatingDefinitionAction`).
- Limite noto: lo stato si legge da `currentStatus()` del ticket bloccato, con ripiego sul valore grezzo originale.

## Non fa

- Non modifica ne' cancella un voto esistente.
- Non cambia lo stato del ticket e non notifica l'ente o il proprietario.
- Non decide quando proporre il voto: lo fa il widget (`TicketCitizenRatingPromptWidget`).
- Non accetta voti dai follower o da utenti diversi dal proprietario.
