---
title: "Contratto — TransitionTicketStatusAction"
type: contract
action: "Modules\\Fixcity\\Actions\\Workflow\\TransitionTicketStatusAction"
created: 2026-11-23
updated: 2026-11-23
---

# TransitionTicketStatusAction

## Input
- Un `Ticket` e il valore stringa dello stato destinazione.
## Output
- `true` dopo il salvataggio riuscito.
## Invarianti
- Origine e destinazione sono valori di `TicketStatusEnum`; la matrice `allowedTransitions()` è la fonte delle transizioni.
## Effetti collaterali
- Aggiorna lo stato del ticket.
## Errori
- `InvalidArgumentException` per stati sconosciuti, `DomainException` per transizioni non consentite, errori di persistenza propagati.
## Non fa
- Non autorizza l'utente, non registra la cronologia e non invia notifiche.
