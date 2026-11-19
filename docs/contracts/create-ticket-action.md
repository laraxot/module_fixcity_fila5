---
title: "Contratto — CreateTicketAction"
type: contract
action: "Modules\Fixcity\Actions\CreateTicketAction"
tags: [contract, action]
created: 2026-11-18
updated: 2026-11-18
---

# CreateTicketAction

Contratto di comportamento (regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`).
Sorgente: `app/Actions/CreateTicketAction.php`.

## Input

- Dati della segnalazione (`owner_id` facoltativo: altrimenti l'utente autenticato), lingua della conferma, callback eseguita dentro la transazione (allegati), flag della conferma al cittadino.

## Output

- Il Ticket salvato, con `code` di tracciamento univoco.

## Invarianti

- Nessun ticket senza `owner_id` ne' senza `code`.
- Ticket, allegati e prima voce di cronologia ci sono tutti o nessuno (una transazione su `fixcity` e `media`).

## Effetti collaterali

- Voce di cronologia «ricevuta» visibile solo all'autore.
- `TicketCreatedEvent` dopo il commit (la ricevuta parte dal listener).
- Messaggio di conferma in sessione se richiesto.

## Errori

- ValidationException su `owner_id` se nessuno e' autenticato.
- UniqueConstraintViolationException se il codice collide 3 volte di fila.
- Ogni eccezione della callback annulla tutto.

## Non fa

- Non valida i campi del modulo (lo fa il wizard o il form).
- Non manda email (listener).
- Non pubblica il ticket (resta privato finche' l'ufficio non lo prende in carico).
