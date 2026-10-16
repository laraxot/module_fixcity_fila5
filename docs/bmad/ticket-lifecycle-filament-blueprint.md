---
title: "Filament Blueprint — operazioni ticket e timeline"
type: filament-blueprint
status: ready-for-dev
module: Fixcity
created: 2026-10-16
tags: [filament, blueprint, ticket, moderation, communication]
related: [./ticket-lifecycle-planning.md, ../../Comment/docs/bmad/fo-comments/filament-blueprint.md]
---

# Superficie Filament

## ViewTicket

Azioni autorizzate con `TicketPolicy`: approva, scarta con motivo, modifica,
assegna a ente/responsabile, invia comunicazione, registra risposta, sollecita,
segna risolto e chiudi. L'azione di chiusura deve chiamare `ChangeStatus` e
fallire se lo stato corrente non è `resolved`.

## Schema operativo

- Tab riepilogo: dati ticket, stato, priorità, responsabile.
- Tab passaggi: `TicketActivity` ordinati per `created_at`, con payload sicuro.
- Tab media: Media Library `attachments`/`ticket`.
- Tab commenti: view canonica del modulo Comment.
- Form comunicazione: destinatario configurato, oggetto, testo, allegati,
  idempotency key; dopo l'invio registra `mail_sent` o l'errore.

## Regole Filament

- usare `XotBaseViewRecord`, `XotBaseResourceInfolist` e action class;
- niente logica di dominio solo nel callback Blade/Filament;
- policy prima del mount e nuovamente nel metodo Livewire;
- payload email non serializzato in componenti FO;
- ogni action mostra esito e conserva l'attività storica.

## Gate di accettazione

Un operatore può ricostruire la pratica completa da una sola scheda; il
cittadino riceve solo la proiezione autorizzata; una chiusura prematura genera
un errore di dominio e nessun record parzialmente aggiornato.

