---
title: "Contratto — ChangeTicketPriorityAction"
type: contract
action: "Modules\\Fixcity\\Actions\\ChangeTicketPriorityAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, autorizzazione, audit]
created: 2026-11-26
updated: 2026-11-26
---

# ChangeTicketPriorityAction

Sorgente: `app/Actions/ChangeTicketPriorityAction.php`. Cambia la priorita' operativa di una segnalazione dal back office
(azione Filament `Filament/Actions/ChangePriority.php`, che dichiara `->authorize('changePriority')`). L'unico test che la esercita e'
il percorso felice di `TicketResourceTest` (`admin can change ticket priority`): rifiuti, valore non valido e voce di audit sono
promesse del codice, non prove.

## Input

- `Ticket $ticket`.
- `string $priority`: uno dei valori di `TicketPriorityEnum` (`low`, `medium`, `high`, `critical`, `urgent`).

## Output

- Il ticket ricaricato (`refresh()`) con la nuova priorita'.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-PRIORITY-INV-1 | `Gate::authorize('changePriority', $ticket)` precede ogni scrittura: solo ruolo PA (`operator`, `supervisor`, `admin`) o permesso `ticket.changePriority` (vedi `ticket-policy.md`, `TICKET-POLICY-INV-1`). | — (non dimostrato) |
| TICKET-PRIORITY-INV-2 | `$priority` deve essere un valore di `TicketPriorityEnum`; altrimenti l'asserzione fallisce e non si scrive nulla. | — (non dimostrato) |
| TICKET-PRIORITY-INV-3 | Un cambio effettivo (`priorita' precedente !== nuova`) scrive una voce di audit `priority_changed` con `from` e `to` sul ticket; se la priorita' non cambia non si scrive nessuna voce. | — (non dimostrato) |
| TICKET-PRIORITY-INV-4 | Con l'abilita' `changePriority` e un valore valido, la nuova priorita' e' salvata sul ticket (`tickets.priority`). | `TICKET-PRIORITY-INV-4` |

## Effetti collaterali

In quest'ordine:

1. `Gate::authorize('changePriority', $ticket)`.
2. Verifica del valore contro `TicketPriorityEnum`.
3. Legge la priorita' precedente da `getRawOriginal('priority')`, poi `forceFill(['priority' => ...])->save()` (`UPDATE tickets`).
   La scrittura avviene anche se il valore e' invariato.
4. Se la priorita' e' cambiata: `LogActivityAction('priority_changed', subject: $ticket, properties: ['from', 'to'])` (modulo Activity).
5. `refresh()`.

## Errori

- `AuthorizationException`: abilita' negata.
- `InvalidArgumentException` (`Webmozart\Assert`): priorita' sconosciuta.
- Eccezioni di persistenza propagate. Non c'e' transazione: scrittura del ticket e voce di audit non sono atomiche.
- Limite noto: il valore precedente viene dall'istanza passata (`getRawOriginal`) senza blocco di riga: con una copia vecchia il
  `from` dell'audit puo' essere sbagliato.
- Limite noto: l'audit va nel registro del modulo Activity, non nella cronologia `ticket_activities`, quindi il cambio di priorita' non
  compare nella timeline del ticket.

## Non fa

- Non notifica proprietario ne' follower e non cambia lo stato del ticket.
- Non e' visibile al cittadino: la priorita' non fa parte del payload pubblico (`build-public-ticket-detail-action.md`).
- Non valida che chi cambia la priorita' sia assegnato al ticket: basta l'abilita'.
