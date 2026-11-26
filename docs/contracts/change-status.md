---
title: "Contratto — ChangeStatus"
type: contract
action: "Modules\\Fixcity\\Actions\\ChangeStatus"
module: Fixcity
status: accepted
tags: [contract, action, ticket, workflow, stato, transazione]
created: 2026-11-26
updated: 2026-11-26
---

# ChangeStatus

Sorgente: `app/Actions/ChangeStatus.php`. E' il percorso vero dei cambi di stato di una segnalazione dal back office (azione Filament
`Filament/Actions/ChangeStatus.php`, che la chiama dopo `->authorize('changeStatus')`). Usa `LockTicketForStatusChangeAction`
(DB-07) per rileggere la riga con il blocco. `ResolveTicketAction` fa `Gate::authorize('changeStatus')` e delega qui;
`ApproveTicketAction`, `RejectTicketAction`, `CloseTicketAction` e `ConfirmTicketResolutionAction` usano lo stesso blocco ma
hanno regole proprie (motivo obbligatorio, transizione fissa, evento dedicato). La primitiva `TransitionTicketStatusAction`
non e' questo percorso (vedi `transition-ticket-status-action.md`).

## Input

- `Ticket $ticket`: la segnalazione (anche una copia vecchia: lo stato si rilegge).
- `string $status`: valore dello stato destinazione (`TicketStatusEnum`).
- `string $reason`: motivo del cambio; spazi ai bordi tolti, vuoto diventa `null`.

## Output

- `void`. L'istanza `$ticket` del chiamante riceve gli attributi salvati (`setRawAttributes`), quindi la pagina mostra lo stato reale.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-STATUS-CHANGE-INV-1 | Lo stato destinazione deve essere un valore di `TicketStatusEnum`; altrimenti `DomainException` e nessuna scrittura. | `TICKET-STATUS-CHANGE-INV-1` |
| TICKET-STATUS-CHANGE-INV-2 | Lo stato corrente si legge con la riga bloccata (`SELECT ... FOR UPDATE`) e la transizione si valida su quello: due operatori sullo stesso ticket (o un doppio clic) non superano entrambi il controllo sullo stesso stato vecchio; il secondo trova lo stato aggiornato e riceve la `DomainException`. | `TICKET-STATUS-CHANGE-INV-2` |
| TICKET-STATUS-CHANGE-INV-3 | Una transizione fuori da `TicketStatusEnum::allowedTransitions()` (unica fonte) lancia `DomainException` e non scrive ne' stato ne' cronologia. | `TICKET-STATUS-CHANGE-INV-3` |
| TICKET-STATUS-CHANGE-INV-4 | Se lo stato destinazione e' gia' quello attuale non succede nulla: nessuna scrittura, nessuna voce di cronologia, nessuna notifica. | — (non dimostrato) |
| TICKET-STATUS-CHANGE-INV-5 | Ogni cambio scrive una voce `StatusChange` con `from`/`to`, il motivo e la visibilita' `Public` se lo stato destinazione e' in `TicketStatusEnum::canViewByAll()`, altrimenti `Internal`; stato e voce stanno nella stessa transazione. | `TICKET-STATUS-CHANGE-INV-5` |
| TICKET-STATUS-CHANGE-INV-6 | L'autore della voce e' l'utente autenticato che agisce, salvato come identificativo stringa (uuid), mai perso. | `TICKET-STATUS-CHANGE-INV-6` |
| TICKET-STATUS-CHANGE-INV-7 | Dopo il commit l'istanza del chiamante riflette lo stato salvato. | `TICKET-STATUS-CHANGE-INV-7` |

## Effetti collaterali

In quest'ordine:

1. `TicketStatusEnum::tryFrom($status)`; visibilita' calcolata dallo stato destinazione; autore = `auth()->id()`.
2. Transazione sulla connessione del ticket (`LockTicketForStatusChangeAction`): riga riletta con `FOR UPDATE`.
3. Sulla riga bloccata: stato corrente letto e validato, `assertCanTransitionTo`, `setStatus($target)`,
   `RecordTicketActivityAction` (evento `StatusChange`).
4. Commit; gli attributi salvati passano all'istanza del chiamante.
5. Dopo il commit, `TicketActivityObserver` sulla voce creata: una voce pubblica di cambio stato avvisa proprietario e follower
   (`NotifyTicketSubscribersOfStatusChangeAction`, vedi il suo contratto); gli eventi `approved`, `closed` ecc. avvisano il proprietario
   (`NotifyTicketOwnerOfLifecycleEventAction`).

## Errori

- `DomainException` `Stato segnalazione non valido: ...`: stato destinazione sconosciuto (`TICKET-STATUS-CHANGE-INV-1`).
- `DomainException` `Lo stato corrente della segnalazione non e' riconosciuto: ...`: riga con uno stato fuori enum.
- `DomainException` `Transizione non consentita: ...`: fuori matrice o stato terminale (`TICKET-STATUS-CHANGE-INV-2`, `-3`).
- `ModelNotFoundException` se la riga e' stata cancellata nel frattempo (`firstOrFail`).
- Limite noto: se lo stato corrente della riga e' vuoto, la transizione non viene validata (`$current?->assertCanTransitionTo`) e
  si scrive qualunque stato valido. E' un ripiego per i dati legacy senza stato.
- Limite noto: i messaggi delle eccezioni sono stringhe italiane nel codice PHP, non chiavi di traduzione.

## Non fa

- **Non autorizza**: nessun `Gate`. Chi la chiama deve aver verificato `changeStatus` (l'azione Filament lo fa con `->authorize('changeStatus')`);
  una chiamata diretta da codice nuovo aggira la Policy.
- Non manda notifiche direttamente: partono dall'observer, dopo il commit.
- Non e' registrata come listener di `TicketCreatedEvent`: `Listeners\TicketCreatedListener`, che la richiama, non e' registrato (commento in `EventServiceProvider`).
- Non decide chi e' operatore PA ne' i permessi (`ticket-policy.md`).
