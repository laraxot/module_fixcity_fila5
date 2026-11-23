---
title: "Contratto — SubmitTicketOnceAction"
type: contract
action: "Modules\\Fixcity\\Actions\\SubmitTicketOnceAction"
created: 2026-11-23
updated: 2026-11-23
---

# SubmitTicketOnceAction

## Input
- Proprietario, token server del wizard, callback di creazione e attesa del lock.
## Output
- Il ticket già creato o quello restituito dalla callback.
## Invarianti
- Per la stessa coppia proprietario/token la callback è eseguita una sola volta; un token vuoto non viene memorizzato.
## Effetti collaterali
- Usa un lock cache e conserva l'identificativo del ticket per un'ora.
## Errori
- `LockTimeoutException` se il lock non è disponibile; gli errori della callback vengono propagati e il lock viene rilasciato.
## Non fa
- Non crea direttamente ticket, non valida il modulo e non sostituisce `CreateTicketAction`.
