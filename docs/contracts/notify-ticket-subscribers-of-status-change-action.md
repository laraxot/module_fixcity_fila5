---
title: "Contratto — NotifyTicketSubscribersOfStatusChangeAction"
type: contract
action: "Modules\\Fixcity\\Actions\\NotifyTicketSubscribersOfStatusChangeAction"
module: Fixcity
status: accepted
tags: [contract, action, notifiche, tracking, privacy, follower]
created: 2026-11-26
updated: 2026-11-26
---

# NotifyTicketSubscribersOfStatusChangeAction

Sorgente: `app/Actions/NotifyTicketSubscribersOfStatusChangeAction.php` (DCP-284, DCP-287). Avvisa proprietario e follower quando una segnalazione cambia stato in modo
pubblico. Parte da `TicketActivityObserver::created`, che e' `ShouldHandleEventsAfterCommit`: la notifica esce solo dopo il commit della riga di cronologia scritta da
`ChangeStatus` (vedi `change-status.md`). Qui passa il **codice di tracciamento**, che e' un capability token: lo riceve solo il proprietario.

## Input

- `TicketActivity $activity`: la voce di cronologia appena creata.

## Output

- `void`. Se la voce non e' un cambio di stato pubblico, o mancano le condizioni sotto, non fa nulla.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-NOTIFY-STATUS-INV-1 | Una voce `status_change` con visibilita' `internal` non avvisa nessuno (proprietario compreso). | `TICKET-NOTIFY-STATUS-INV-1` |
| TICKET-NOTIFY-STATUS-INV-2 | Anche con visibilita' `public`, uno stato destinazione fuori da `TicketStatusEnum::canViewByAll()` non avvisa nessuno; altri tipi di evento sono ignorati (di quelli si occupa `NotifyTicketOwnerOfLifecycleEventAction`). | — (non dimostrato) |
| TICKET-NOTIFY-STATUS-INV-3 | Proprietario e follower ricevono una notifica ciascuno, anche quando il proprietario e' fra i follower (deduplica per identificativo). | `TICKET-NOTIFY-STATUS-INV-3` |
| TICKET-NOTIFY-STATUS-INV-4 | Il codice di tracciamento esce solo verso il proprietario (azione `?code=` verso la pagina di tracciamento); il follower riceve titolo e link al dettaglio pubblico `/tickets/{id}`, senza `code=` ne' codice nel payload, nella pagina delle notifiche o nella mail. | `TICKET-NOTIFY-STATUS-INV-4` |
| TICKET-NOTIFY-STATUS-INV-5 | Un ticket senza codice di tracciamento (dati legacy) non produce nessuna notifica, nemmeno ai follower. | `TICKET-NOTIFY-STATUS-INV-5` |
| TICKET-NOTIFY-STATUS-INV-6 | Il canale e' deciso dalla notifica: sempre `database`; anche `mail` solo se l'utente ha un indirizzo email verificato e non ha rinunciato alla posta (`SetFixcityEmailNotificationPreferenceAction`). | `TICKET-NOTIFY-STATUS-INV-6` |
| TICKET-NOTIFY-STATUS-INV-7 | Un'eccezione nell'accodamento verso un destinatario e' catturata e registrata nel log (identificativo e classe dell'eccezione, mai indirizzo ne' codice) e non impedisce l'invio agli altri. | — (non dimostrato) |
| TICKET-NOTIFY-STATUS-INV-8 | La notifica e' accodata dopo il commit (`ShouldQueueAfterCommit`) con 3 tentativi e attesa di 60 e 300 secondi fra l'uno e l'altro. | `TICKET-NOTIFY-STATUS-INV-8` |

## Effetti collaterali

In quest'ordine:

1. Filtri su tipo di evento, visibilita' e stato destinazione (`payload.to`); letture di ticket, iscritti (`ticketSubscriberQuery`), utenti e proprietario.
2. `Log::warning` (solo l'id del ticket) se il codice manca.
3. Per ogni destinatario distinto, `Notification::send`: la notifica e' accodata dopo il commit; il job scrive una riga in `notifications` (database) e, se ammesso, manda una mail
   (3 tentativi, attesa 60 e 300 s, `NotificationRetryPolicyTest`).
4. `Log::error` per ogni accodamento fallito.

Non scrive sul ticket, sull'iscrizione ne' sulla cronologia.

## Errori

- Nessuna eccezione propagata dall'accodamento: viene catturata destinatario per destinatario. Gli errori di consegna avvengono nel job in coda, fuori da questa Action (ritentativi e `failed_jobs`).
- Limite noto: la mancanza del codice blocca anche i follower, che il codice non lo ricevono comunque (usano il link al dettaglio). Un ticket legacy senza codice non avvisa
  nessuno.
- Limite noto: l'invio non e' atomico rispetto al commit dello stato: se il processo cade fra il commit e l'observer, la notifica si perde e non c'e' ritentativo.
- Limite noto: `Notification::send` e' chiamata una volta per destinatario dentro il ciclo, senza raggruppare; con molti follower il costo cresce linearmente.

## Non fa

- Non modifica lo stato del ticket e non scrive la cronologia (lo fa `ChangeStatus`).
- Non avvisa per `approved`, `rejected`, `closed` e simili: `NotifyTicketOwnerOfLifecycleEventAction` ha l'insieme disgiunto di eventi (nessuna voce produce due messaggi, DCP-284).
- Non avvisa l'ente ne' l'assegnatario (`send-ticket-to-ente-action.md`, `notify-ticket-assignee-of-assignment-action.md`).
- Non rivela il codice a chi segue: il codice e' una capability del proprietario.
