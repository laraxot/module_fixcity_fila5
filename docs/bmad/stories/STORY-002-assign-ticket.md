---
title: AssignTicketFlow
id: STORY-002
author: BMAD
status: done
priority: high
type: story
module: Fixcity
tags:
- bmad
- fixcity
created: legacy
updated: 2026-09-26
qmd: story 002 assign ticket FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Descrizione
Il PA Operator assegna un ticket a un utente specifico o rimuove l'assegnazione. Il flusso include:
- List of tickets for assignment
- Selezione del ticket da assegnare
- Ricaricamento della lista dopo l'assegnazione
- Corretta normalizzazione del payload tramite GetTicketFormDataForPersistAction

## Attori
- **PA Operator** (assigner) - l'utente che assegna il ticket
- **System** - handler delle notifiche

## Inputs
- Ticket ID (required)
- Responsible user ID (optional, null per rimozione)

## Outputs
- Ticket aggiornato con il nuovo responsible_id
- Notifica di assegnazione inviata
- Modello timeline ricostruito

## Flusso
1. PA Operator apre la dashboard dei ticket
2. Seleziona un ticket dalla lista
3. Clicca su "Assign"
4. Seleziona o inserisce il responsabile
5. Conferma l'assegnazione
6. AssignTicketAction viene eseguito
7. Ticket viene salvato e timeline ricostruita
9. Notifica di assegnazione viene inviata

## Decisione BMAD
- Usare `AssignTicketAction` per la logica di business
- Usare `TicketModel::assignee()` relation (HasTicketRelations) per accesso
- Usare `GetTicketFormDataForPersistAction` per normalizzare il payload in caso di aggiornamento
- Usare `QueueableAction` per ogni operazione che modifica il database
- Notifica tramite listener di evento

## Test
- Test unitario per `AssignTicketAction::execute()`
- Test feature per l'assignazione del ticket tramite interfaccia utente
- Test che il ticket viene aggiornato correttamente
- Test che la notifica viene inviata

## Second Brain
- Documentazione in `docs/chat/` con riferimento alle storie BMAD
- Aggiornamento di `docs/wiki/log.md` con link alle storie
- Decisione registrata in `docs/chat/INDEX.md`

## Quality Gates
- PHPStan level 10 su tutti i file modificati
- Test coverage > 90% per il modulo Fixcity
- Checklist BMAD completata
- Second Brain healthcheck pass
