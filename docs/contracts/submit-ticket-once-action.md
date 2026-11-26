---
title: "Contratto — SubmitTicketOnceAction"
type: contract
action: "Modules\\Fixcity\\Actions\\SubmitTicketOnceAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, idempotenza, cache]
created: 2026-11-23
updated: 2026-11-26
---

# SubmitTicketOnceAction

Sorgente: `app/Actions/SubmitTicketOnceAction.php`. Chiamata dal wizard `CreateTicketWizardWidget` (DCP-286): un doppio
clic su «Invia» o una richiesta Livewire ripetuta creavano due segnalazioni (misurato: ticket 19 e 20, stesso titolo).

## Input

- `string $ownerId`: id del proprietario (sempre parte della chiave e della ricerca).
- `string $token`: token di invio generato dal server al mount del wizard (proprieta' Livewire `#[Locked]`).
- `Closure(): Ticket $create`: crea la segnalazione (e gli allegati); tipicamente `CreateTicketAction`.
- `int $waitSeconds` (default 5): attesa massima del blocco. Esiste anche `existing(ownerId, token): ?Ticket`, che legge senza creare.

## Output

- Il `Ticket` gia' creato da questo invio, oppure quello appena restituito dalla callback.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-SUBMIT-ONCE-INV-1 | Per la stessa coppia (proprietario, token) la callback gira al piu' una volta: un secondo invio trova la segnalazione e la restituisce, senza crearne un'altra ne' ripetere gli allegati. | `TICKET-SUBMIT-ONCE-INV-1` |
| TICKET-SUBMIT-ONCE-INV-2 | Il proprietario e' parte della chiave e della query di `existing()`: lo stesso token di un altro utente non restituisce mai la segnalazione altrui. | `TICKET-SUBMIT-ONCE-INV-2` |
| TICKET-SUBMIT-ONCE-INV-3 | Un token vuoto (componente non montato) non si memorizza ne' si blocca: la callback gira sempre. Una chiave vuota farebbe coincidere tutti gli invii dello stesso utente. | — (non dimostrato) |
| TICKET-SUBMIT-ONCE-INV-4 | Un token il cui invio e' fallito (la callback lancia) non viene ricordato: l'eccezione risale, il blocco viene rilasciato e un nuovo tentativo con lo stesso token crea la segnalazione. | `TICKET-SUBMIT-ONCE-INV-4` |
| TICKET-SUBMIT-ONCE-INV-5 | Se un'altra richiesta tiene il blocco dello stesso token oltre `$waitSeconds`, `execute` lancia `LockTimeoutException` senza eseguire la callback: nessun ticket scritto. | `TICKET-SUBMIT-ONCE-INV-5` |

## Effetti collaterali

In quest'ordine:

1. Token vuoto: esegue la callback e restituisce (nessuna cache); la callback deve restituire un `Ticket`, altrimenti `UnexpectedValueException`.
2. `Cache::get('fixcity:ticket-submission:{owner}:{token}')`, poi `SELECT` del ticket per id e `owner_id`.
3. `Cache::lock('fixcity:ticket-submission-lock:{owner}:{token}', 60 s)` con `block($waitSeconds)`.
4. Seconda lettura (un'altra richiesta puo' aver finito mentre si aspettava il blocco).
5. Esegue la callback (di norma scrive ticket, allegati, cronologia, evento: vedi `create-ticket-action.md`).
6. `Cache::put(...)` dell'id del ticket per 3600 secondi.
7. Rilascia il blocco (`finally`), anche su eccezione.

## Errori

- `LockTimeoutException`: blocco non ottenuto entro l'attesa. Protegge `TICKET-SUBMIT-ONCE-INV-5`.
- `UnexpectedValueException`: la callback non ha restituito un `Ticket`; nulla viene memorizzato.
- Le eccezioni della callback si propagano e non lasciano traccia in cache (`TICKET-SUBMIT-ONCE-INV-4`).
- Limite noto: il blocco dura 60 secondi; una callback piu' lenta lascia scadere il blocco e una seconda richiesta puo' partire
  in parallelo. L'idempotenza vale per la cache condivisa: con driver `array` non attraversa le richieste.

## Non fa

- Non crea direttamente ticket, non valida il modulo e non sostituisce `CreateTicketAction`.
- Non genera il token: lo genera il widget al mount e il client non puo' cambiarlo (`CannotUpdateLockedPropertyException`,
  test `CreateTicketWizardIdempotentSubmitTest`).
- Non usa una tabella: l'esito vive in cache per un'ora, poi un invio con lo stesso token creerebbe una nuova segnalazione.
- Non autorizza: l'accesso al wizard e' del middleware `auth` della pagina `tickets/create`.
