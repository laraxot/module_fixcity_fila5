---
title: "Contratto — SetTicketSubscriptionAction"
type: contract
action: "Modules\\Fixcity\\Actions\\SetTicketSubscriptionAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, follow, autorizzazione, idempotenza]
created: 2026-11-26
updated: 2026-11-26
---

# SetTicketSubscriptionAction

Sorgente: `app/Actions/SetTicketSubscriptionAction.php`. Fa seguire o smettere di seguire una segnalazione a un utente autenticato (DCP-148).
Chiamanti: `FollowTicketAction` (POST "Segui" del dettaglio pubblico) e `Filament/Widgets/Ticket/ViewWidget.php`. Chi segue riceve le notifiche di cambio
stato (`NotifyTicketSubscribersOfStatusChangeAction`), senza mai vedere il codice di tracciamento.

## Input

- `Ticket $ticket`.
- `UserContract $user`: chi segue; deve avere un identificativo persistito (intero o stringa non vuota).
- `bool $following`: `true` = segui, `false` = smetti.

## Output

- `bool`: lo stato richiesto (`$following`), uguale a quello finale. Chiamate ripetute con lo stesso valore danno lo stesso risultato.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-FOLLOW-INV-1 | Si segue solo un ticket che l'utente puo' vedere: `Gate::forUser($user)->authorize('view', $ticket)` precede ogni scrittura; un ticket privato di un altro cittadino lancia `AuthorizationException` (provato per "segui"; per "smetti" il controllo e' lo stesso nel codice ma nessun test lo esercita). | `TICKET-FOLLOW-INV-1` |
| TICKET-FOLLOW-INV-2 | Idempotenza: seguire due volte lascia una sola riga in `ticket_subscribers` per (ticket, utente); smettere due volte lascia zero righe visibili. | `TICKET-FOLLOW-INV-2` |
| TICKET-FOLLOW-INV-3 | Smettere di seguire e' un soft delete e seguire di nuovo ripristina la stessa riga (`restore`), senza crearne un'altra: l'indice univoco `ticket_subscribers_ticket_user_unique` (ticket, utente) non viene mai violato da un "segui" dopo un "smetti". | — (non dimostrato) |
| TICKET-FOLLOW-INV-4 | Un utente senza identificativo persistito (`getAuthIdentifier()` nullo o stringa vuota) e' rifiutato con `InvalidArgumentException` e non si scrive nulla. | — (non dimostrato) |

## Effetti collaterali

In quest'ordine:

1. `Gate::forUser($user)->authorize('view', $ticket)`.
2. Validazione dell'identificativo utente.
3. Transazione sulla connessione di `ticket_subscribers`: `SELECT` della riga (anche cancellata) per ticket e utente, poi una sola fra: `INSERT`, `restore` o soft delete,
   oppure nessuna scrittura se lo stato richiesto e' gia' quello attuale.

Nessuna notifica e nessuna voce di cronologia: seguire non e' un evento del ticket.

## Errori

- `AuthorizationException`: l'utente non puo' vedere il ticket.
- `InvalidArgumentException`: utente senza identificativo valido (messaggio italiano nel codice PHP).
- Limite noto: due richieste concorrenti "segui" dello stesso utente possono entrambe non trovare la riga e tentare l'`INSERT`; la seconda cade sull'indice univoco
  e propaga l'eccezione di database (non c'e' `lockForUpdate`).
- Limite noto: l'autorizzazione e' `view`, non un permesso dedicato: chi puo' vedere il ticket puo' seguirlo, anche un operatore PA.

## Non fa

- Non avvisa il proprietario, ne' registra l'iscrizione nella cronologia pubblica.
- Non verifica che l'utente abbia un indirizzo email o le preferenze di notifica attive (`SetFixcityEmailNotificationPreferenceAction`).
- Non accetta ospiti: serve un `UserContract` (il POST "Segui" richiede l'accesso).
- Non decide chi riceve le notifiche di cambio stato: lo fa `NotifyTicketSubscribersOfStatusChangeAction`.
