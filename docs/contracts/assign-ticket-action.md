---
title: "Contratto — AssignTicketAction"
type: contract
action: "Modules\\Fixcity\\Actions\\AssignTicketAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, autorizzazione]
created: 2026-11-23
updated: 2026-11-26
---

# AssignTicketAction

Sorgente: `app/Actions/AssignTicketAction.php`. Chiamata dalla pagina `ViewTicket` del back office (azione `assign`).

## Input

- `Ticket $ticket`: la segnalazione da assegnare.
- `?string $responsibleId`: id del responsabile PA, oppure `null` per togliere l'assegnazione.

## Output

- Lo stesso ticket, riletto dalla riga bloccata (`refresh()`), con `responsible_id` aggiornato.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-ASSIGN-INV-1 | Chi non ha l'abilita' `assign` della `TicketPolicy` (ruolo PA o permesso `ticket.assign`) viene fermato da `Gate::authorize('assign')` prima di ogni scrittura. | — (non dimostrato) |
| TICKET-ASSIGN-INV-2 | Un `responsibleId` non nullo deve essere uno degli utenti con ruolo PA (`ListAssignableTicketOperatorsAction`, ruoli `BasePolicy::PA_ROLES`); un cittadino come responsabile da' `ValidationException` su `responsible_id` e il ticket resta com'era. Il controllo sta nell'Action, non solo nel form Filament. | `TICKET-ASSIGN-INV-2` |
| TICKET-ASSIGN-INV-3 | Lettura con `lockForUpdate`, scrittura del responsabile e voce di cronologia `Assignment` (visibilita' interna) stanno in una sola transazione: non esiste un cambio di responsabile senza la sua voce. | — (non dimostrato) |
| TICKET-ASSIGN-INV-4 | Se il responsabile non cambia (`previous === nuovo`) non si registra nessuna attivita' e non parte nessuna notifica. | — (non dimostrato) |
| TICKET-ASSIGN-INV-5 | Dopo il commit, l'operatore appena assegnato riceve `TicketAssignedNotification` una volta sola; il proprietario del ticket non la riceve; togliere l'assegnazione non la invia al responsabile uscente. | `TICKET-ASSIGN-INV-5` |
| TICKET-ASSIGN-INV-6 | La voce di cronologia registra come autore l'UUID dell'operatore che agisce (`auth()->id()`), non quello del responsabile. | — (non dimostrato: `TicketActivityActorUuidTest` assegna a un utente senza ruolo PA, che oggi l'invariante 2 respinge; il test va riallineato, vedi Backlog test) |

## Effetti collaterali

In quest'ordine:

1. `Gate::authorize('assign', $ticket)`.
2. Controllo del responsabile contro l'elenco degli operatori assegnabili (una query sugli utenti con ruolo PA).
3. Transazione sulla connessione del ticket: riga riletta con `FOR UPDATE`, `UPDATE tickets SET responsible_id`.
4. Se il responsabile e' cambiato: `RecordTicketActivityAction` (evento `Assignment`, visibilita' `Internal`, stato invariato,
   `payload.responsible_id`).
5. Commit, `refresh()` del ticket.
6. `NotifyTicketAssigneeOfAssignmentAction` (notifica database e, se l'operatore ha l'email verificata e non ha rinunciato,
   anche mail). Un errore di invio viene loggato e non annulla l'assegnazione.

## Errori

- `AuthorizationException`: abilita' `assign` negata. Protegge `TICKET-ASSIGN-INV-1`.
- `ValidationException` (`responsible_id`): responsabile non assegnabile. Protegge `TICKET-ASSIGN-INV-2`.
- Eccezioni di persistenza: rollback, nessuna notifica.
- Limite noto: il testo della cronologia (`Rimossa assegnazione`, `Assegnato a responsabile #…`) e il messaggio
  `validation.in` con attributo `responsabile` sono stringhe italiane nel codice PHP, non chiavi di traduzione.

## Non fa

- Non cambia lo stato del ticket (per quello c'e' `ChangeStatus`) e non decide quali ruoli siano PA (lo decide `BasePolicy::PA_ROLES`).
- Non applica uno scoping per comune o per competenza: il comune non e' una colonna del ticket (DD-1 della story DCP-284,
  vedi il docblock di `NotifyTicketReceivedAction`).
- Non notifica il proprietario della segnalazione.
