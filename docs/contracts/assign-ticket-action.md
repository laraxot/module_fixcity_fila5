---
title: "Contratto — AssignTicketAction"
type: contract
action: "Modules\\Fixcity\\Actions\\AssignTicketAction"
created: 2026-11-23
updated: 2026-11-23
---

# AssignTicketAction

## Input
- Un `Ticket` e l'identificativo opzionale del responsabile PA.
## Output
- Il ticket aggiornato e ricaricato.
## Invarianti
- Gate `assign` autorizzato; il responsabile presente appartiene agli operatori assegnabili; cambio e cronologia sono transazionali.
## Effetti collaterali
- Aggiorna `responsible_id`, registra un'attività interna e notifica l'assegnatario quando cambia.
## Errori
- Autorizzazione negata, `ValidationException` per responsabile non assegnabile o eccezioni di persistenza.
## Non fa
- Non cambia lo stato del ticket e non decide quali ruoli siano PA.
