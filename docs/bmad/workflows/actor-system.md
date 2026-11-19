---
title: "BMAD actor — Sistema"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, actor, system, events, notifications, queue, fixcity]
module: Fixcity
qmd: "system events notifications queue timeline geo rating fixcity workflow"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-citizen.md
  - actor-operator.md
  - ../stories/STORY-003-system-notification.md
---

# Attore: Sistema

**Scopo:** propagare eventi, timeline, geo e notifiche senza perdere il ticket
se un canale fallisce.

## Happy path

1. Creazione ticket → `CreateTicketAction`; gli avvisi di stato/assegnazione
   sono dispatchati dalle Action di activity.
2. Cambio stato / assign → `RecordTicketActivityAction` → timeline
   (`BuildTicketTimelineAction`) e notifica database; email solo per destinatari
   `UserContract` con indirizzo verificato e preferenza email attiva.
3. Geo pubblico/privato via `BuildTicketsGeoJsonAction` /
   `LoadPublicTicketsGeoJsonAction` secondo policy di visibilità.
4. Rating cittadino aggregato (`GetCitizenRatingAggregateAction`).
5. UI tracking usa i cataloghi owner `ticket.fields` e `ticket.track` in IT/EN;
   lo status display usa `TicketStatusEnum::getLabel()`. Il capability code resta
   omesso al guest e viene mostrato solo al lookup ID autorizzato dell'owner.
6. Le notifiche assegnazione/stato usano `ShouldQueueAfterCommit`: massimo 3
   tentativi, backoff 60/300 secondi. Il delivery oltre il limite entra nel
   backend `failed_jobs` configurato; dashboard e retry manuale non sono previsti.

## Failure path

- errore di invio → ticket e activity restano persistiti; il worker riprova la
  consegna nei tentativi previsti. Esauriti i retry, Laravel può registrare il job
  in `failed_jobs` se il backend è configurato; FixCity non fornisce dashboard,
  alert o retry manuale. Worker, storage failed-jobs e procedura operativa non sono
  ancora verificati in staging;
- geo/media down → fallback, niente perdita segnalazione;
- delivery non riuscito → nessun receipt o stato delivery visibile all'operatore;
  il cittadino vede lo stato del ticket, non la conferma di consegna email.

## Prove

Feature test copre activity→notifica, privacy stato pubblico, email e URL per
utenti verificati/non verificati e preferenza email. Il test unitario costruisce
il job Laravel e verifica retries/backoff/after-commit. Queste prove non dimostrano
che il worker di deploy sia attivo, che il provider SMTP accetti/consegni il messaggio
né che `failed_jobs` sia monitorato; queue worker, SMTP, retention/replay dei job e
push restano da verificare nell'ambiente di deploy.

## Regola

Il sistema non sostituisce la policy umana: ogni payload rispetta isolamento.
