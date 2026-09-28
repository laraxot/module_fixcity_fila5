---
title: SystemNotificationFlow
id: STORY-003
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
qmd: story 003 system notification FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Descrizione
Il sistema gestisce le notifiche per i vari eventi del modulo Fixcity. Il flusso include:
- Creazione del ticket → notifica al PA
- Assegnazione → notifica all'assegnatario
- Aggiornamento stato → notifica agli utenti iscritti
- Commento → notifica agli interessati

## Attori
- **System** - gestisce le notifiche
- **PA Operator** - riceve notifiche di nuovi ticket
- **Citizen** - riceve notifiche sugli aggiornamenti del proprio ticket

## Inputs
- Event type (TicketCreatedEvent, TicketAssignedEvent, StatusChangedEvent, CommentCreatedEvent)
- Event data (ticket details, user info)

## Outputs
- Notifica inviata via canale appropriato (email, in-app, push)
- Log della notifica registrato
- Stato della notifica tracciato

## Flusso
1. Evento viene dispatch (es. TicketCreatedEvent)
2. Listener del modulo Fixcity riceve l'evento
3. Notifica viene generata tramite NotificationService
4. Notifica viene inviata tramite il canale configurato
5. Log della notifica viene registrato nel database
6. Stato della notifica viene aggiornato (sent, delivered, read)

## Decisione BMAD
- Usare `Event` e `Listener` pattern per la gestione delle notifiche
- Usare `NotificationService` per la generazione
- Usare `Notification` model per il tracking
- Non usare HTTP controllers per le notifiche (solo Folio pages + Actions)
- Usare `SafeStringCastAction` per la validazione dei dati

## Second Brain
- Documentazione in `docs/chat/` con riferimento alle storie BMAD
- Aggiornamento di `docs/wiki/log.md` con link alle storie
- Decisione registrata in `docs/chat/INDEX.md`
