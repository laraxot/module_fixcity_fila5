---
title: "Contratto — TransitionTicketStatusAction"
type: contract
action: "Modules\\Fixcity\\Actions\\Workflow\\TransitionTicketStatusAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, workflow]
created: 2026-11-23
updated: 2026-11-26
---

# TransitionTicketStatusAction

Sorgente: `app/Actions/Workflow/TransitionTicketStatusAction.php`. Primitiva rimasta dalla conversione del vecchio
`WorkflowService` (No-Services).

**Limite noto (verificato con `rg TransitionTicketStatusAction` su `laravel/`, escluse docs e vendor): nessun codice di produzione
la chiama.** Il percorso vero dei cambi di stato e' `ChangeStatus` (vedi `change-status.md`), che rilegge la riga con il blocco,
registra la cronologia e decide la visibilita'. Questa Action non deve essere imitata per codice nuovo.

## Input

- `Ticket $ticket`: la segnalazione, di cui si legge lo stato con `resolveTicketStatusValue()`.
- `string $targetStatus`: valore stringa dello stato destinazione.

## Output

- `true` dopo il salvataggio riuscito.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-TRANSITION-INV-1 | Stato corrente e destinazione devono essere valori di `TicketStatusEnum`; altrimenti `InvalidArgumentException` e nessuna scrittura. | — (non dimostrato) |
| TICKET-TRANSITION-INV-2 | La transizione deve essere in `TicketStatusEnum::allowedTransitions()` (unica fonte, via `assertCanTransitionTo`); altrimenti `DomainException` e lo stato non cambia. | — (non dimostrato) |

## Effetti collaterali

- `UPDATE tickets SET status = <destinazione>` tramite `$ticket->update()`. Nessuna transazione, nessun `FOR UPDATE`,
  nessuna voce di cronologia, nessuna notifica.

## Errori

- `InvalidArgumentException`: stato sconosciuto (`TICKET-TRANSITION-INV-1`).
- `DomainException`: transizione non consentita (`TICKET-TRANSITION-INV-2`).
- Errori di persistenza propagati.

## Non fa

- Non autorizza l'utente (nessun `Gate`).
- Non rilegge la riga con il blocco: due chiamanti concorrenti decidono sullo stesso stato vecchio (il difetto che `ChangeStatus` ha chiuso con DB-07).
- Non registra la cronologia ne' invia notifiche.
- Non e' il punto da cui passano il back office o il listener: quelli usano `ChangeStatus`.
