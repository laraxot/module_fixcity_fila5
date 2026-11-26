---
title: "Contratto — NotifyTicketAssigneeOfAssignmentAction"
type: contract
action: "Modules\\Fixcity\\Actions\\NotifyTicketAssigneeOfAssignmentAction"
module: Fixcity
status: accepted
tags: [contract, action, notifiche, assegnazione, operatore]
created: 2026-11-26
updated: 2026-11-26
---

# NotifyTicketAssigneeOfAssignmentAction

Sorgente: `app/Actions/NotifyTicketAssigneeOfAssignmentAction.php`. Avvisa l'operatore a cui e' stata appena assegnata una segnalazione. E' chiamata esplicitamente da
`AssignTicketAction` **dopo** il commit della transazione di assegnazione, con la voce di cronologia `assignment` appena scritta (vedi `assign-ticket-action.md`). Non e' collegata
a un observer.

## Input

- `TicketActivity $activity`: la voce `assignment` a visibilita' `internal`, con `payload.responsible_id`.

## Output

- `void`.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-NOTIFY-ASSIGN-INV-1 | Dopo l'assegnazione riceve una sola notifica `TicketAssignedNotification` l'operatore assegnato; il proprietario del ticket non la riceve. | `TICKET-NOTIFY-ASSIGN-INV-1` |
| TICKET-NOTIFY-ASSIGN-INV-2 | La rimozione dell'assegnazione (`responsible_id` nullo) non invia `TicketAssignedNotification`, nemmeno all'operatore precedente. | `TICKET-NOTIFY-ASSIGN-INV-2` |
| TICKET-NOTIFY-ASSIGN-INV-3 | Una voce che non e' `assignment` a visibilita' `internal`, senza `responsible_id` o con `responsible_id` diverso da quello attuale del ticket (assegnazione gia' superata) e' ignorata. | — (non dimostrato) |
| TICKET-NOTIFY-ASSIGN-INV-4 | Il canale e' deciso dalla notifica: sempre `database`; anche `mail` solo se l'operatore ha un indirizzo email verificato e non ha rinunciato alla posta. | `TICKET-NOTIFY-ASSIGN-INV-4` |
| TICKET-NOTIFY-ASSIGN-INV-5 | Un'eccezione nell'accodamento e' catturata e registrata nel log (id del ticket, id del destinatario, classe dell'eccezione) e non fa fallire l'assegnazione gia' confermata. | — (non dimostrato) |
| TICKET-NOTIFY-ASSIGN-INV-6 | La notifica e' accodata dopo il commit (`ShouldQueueAfterCommit`) con 3 tentativi e attesa di 60 e 300 secondi. | `TICKET-NOTIFY-ASSIGN-INV-6` |

## Effetti collaterali

1. Letture: ticket della voce e relazione `assignee`.
2. `Notification::send` verso l'assegnatario se e' un `UserContract` notificabile: la notifica e' accodata dopo il commit; il job scrive una riga in `notifications` e, se ammesso, manda una mail.
3. `Log::error` se l'accodamento fallisce.

Non scrive sul ticket ne' sulla cronologia.

## Errori

- Nessuna eccezione propagata dall'accodamento; gli errori di consegna avvengono nel job in coda, fuori da questa Action.
- Limite noto: se il processo cade fra il commit dell'assegnazione e questa chiamata, la notifica si perde senza ritentativo.
- Limite noto: la notifica contiene solo l'id del ticket (`TicketAssignedNotification($ticket->id)`), non il titolo.

## Non fa

- Non assegna ne' valida il ruolo dell'operatore: lo fa `AssignTicketAction` (`TICKET-ASSIGN-INV-2`).
- Non avvisa proprietario o follower dell'assegnazione (e' una voce interna).
- Non avvisa l'operatore precedente quando la segnalazione gli viene tolta.
