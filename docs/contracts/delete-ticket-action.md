---
title: "Contratto — DeleteTicketAction"
type: contract
action: "Modules\\Fixcity\\Actions\\DeleteTicketAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, autorizzazione, soft-delete, audit]
created: 2026-11-26
updated: 2026-11-26
---

# DeleteTicketAction

Sorgente: `app/Actions/DeleteTicketAction.php`. Cancella (soft delete) una segnalazione dal back office. Chiamanti:
`Filament/Resources/TicketResource/Pages/ViewTicket.php`, `.../EditTicket.php` e l'azione di gruppo di
`.../Tables/TicketsTable.php`. A differenza delle altre Action non usa `QueueableAction`: e' sincrona.

## Input

- `Ticket $ticket`: la segnalazione da cancellare.
- Implicito: l'utente autenticato (`Filament::auth()->user()` o, in sua assenza, `auth()->user()`), che deve essere un `UserContract`.

## Output

- `void`.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-DELETE-INV-1 | `Gate::authorize('delete', $ticket)` precede ogni scrittura: solo il ruolo `admin` (o `super-admin`) o il permesso `ticket.delete` cancellano; un operatore PA, un supervisore e il proprietario no (regola in `ticket-policy.md`, `TICKET-POLICY-INV-3`). Il pulsante e' nascosto agli altri nella pagina di dettaglio, di modifica e nell'azione di gruppo. | — (non dimostrato: i test provano il pulsante nascosto e la Policy, non il rifiuto dell'Action chiamata direttamente) |
| TICKET-DELETE-INV-2 | La cancellazione e' un soft delete: la riga resta con `deleted_at` valorizzato, la traccia pubblica non si perde. | `TICKET-DELETE-INV-2` |
| TICKET-DELETE-INV-3 | `deleted_by` contiene l'identificativo dell'amministratore che cancella, per la cancellazione singola e per ognuna di quelle in blocco. | `TICKET-DELETE-INV-3` |
| TICKET-DELETE-INV-4 | Impostazione di `deleted_by` e soft delete stanno in una sola transazione sulla connessione del ticket: non esiste una cancellazione senza il suo autore. | — (non dimostrato) |

## Effetti collaterali

In quest'ordine:

1. `Gate::authorize('delete', $ticket)`.
2. Legge l'attore; se non e' un `UserContract` lancia (vedi Errori).
3. Transazione sulla connessione del ticket: `deleted_by` impostato esplicitamente (il modello appena creato puo' non avere ancora
   la colonna negli attributi, quindi il hook generico dell'Updater non basta), `save()`, `delete()`.

Nessuna notifica, nessuna voce di cronologia, nessuna cancellazione di media o iscrizioni.

## Errori

- `AuthorizationException`: abilita' `delete` negata (`TICKET-DELETE-INV-1`).
- `InvalidArgumentException` (`Webmozart\Assert`): nessun utente autenticato o utente non `UserContract`.
- Eccezioni di persistenza: rollback, il ticket resta com'era.
- Limite noto: dopo la cancellazione il codice di tracciamento resta occupato (`AllocateTicketCodeAction` controlla anche le righe
  cancellate) ma `GetPublicTicketByCodeAction` non trova piu' il ticket: il link della ricevuta del cittadino smette di funzionare
  senza avviso. Gli allegati restano sul disco privato.

## Non fa

- Non e' una cancellazione definitiva (`forceDelete`): non esiste un'Action per quella.
- Non avvisa proprietario ne' follower e non registra l'evento nella cronologia pubblica del ticket.
- Non cancella media, iscrizioni (`TicketSubscriber`) ne' attivita' collegate.
- Non gestisce il ripristino: per i ticket non c'e' ne' un'Action ne' un'azione Filament di `restore` (verificato con `rg "restore|forceDelete"` in `app/Actions` e `app/Filament/Resources/TicketResource`).
