---
title: CreateTicketFlow
id: STORY-001
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
qmd: story 001 citizen create ticket FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Descrizione
Il cittadino può creare un ticket segnalazione attraverso il wizard "Create Ticket". Il flusso include:
- Raccolta dati nel wizard (nome, descrizione, tipo)
- Validazione del payload
- Creazione del Ticket con owner_id = utente corrente
- Dispatch di TicketCreatedEvent
- Evento propagato per notifiche

## Attori
- **Cittadino** (citizen) - chi segnala un problema
- **PA Operator** (public administration) - assegna i ticket

## Inputs
- Nome del ticket (obbligatorio)
- Descrizione (obbligatorio)
- Tipo (opzionale, default: "General")

## Outputs
- Ticket creato con status PENDING
- Evento TicketCreatedEvent dispatched
- Notifiche generate (tramite event listener)

## Flusso
1. Cittadino apre il wizard "Create Ticket"
2. Compila i campi (nome, descrizione, tipo)
3. Clicca "Submit"
4. Wizard converte il payload in formato Ticket (GetTicketFormDataForPersistAction)
5. Ticket viene salvato con owner_id = utente corrente
6. TicketCreatedEvent viene dispatch
7. Listener di notifica riceve l'evento e genera notifiche

## Decisione BMAD
- Usare `GetTicketFormDataForPersistAction` per normalizzare il payload (non `PrepareTicketFormDataForPersistAction`)
- Usare `XotBaseWizardWidget` come base (non estendere direttamente `Filament\Widgets\Widget`)
- L'owner del ticket è sempre l'utente corrente (non richiede azione separata)
- Evento `TicketCreatedEvent` per notifiche

## Test
- Test unitario per `CreateTicketAction::execute()`
- Test feature per il wizard (CreateTicketWizardWidget)
- Test che l'owner_id sia l'utente corrente
- Test che l'evento venga dispatched

## Second Brain
- Documentazione in `docs/chat/` con riferimento alle storie BMAD
- Aggiornamento di `docs/wiki/log.md` con link alle storie
- Decisione registrata in `docs/chat/INDEX.md`

## Quality Gates
- PHPStan level 10 su tutti i file modificati
- Test coverage > 90% per il modulo Fixcity
- Checklist BMAD completata
- Second Brain healthcheck pass
