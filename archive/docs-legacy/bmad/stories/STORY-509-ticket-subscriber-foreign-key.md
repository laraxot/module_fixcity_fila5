---
title: STORY-509 — Correggere il riferimento ticket dei follower
type: story
status: done
module: Fixcity
created: 2026-09-26
updated: 2026-09-27
tags:
- bmad
- fixcity
- subscriber
- relation
- notification
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/572
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/573
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
related:
- ../gap-analysis.md
- ../../wiki/concepts/ticket-subscribers-vs-comment-subscribers.md
- ../../wiki/concepts/user-journey-map.md
qmd: STORY 509 ticket subscriber foreign key FixCity BMAD story
---

# STORY-509 — Correggere il riferimento ticket dei follower

## Problema

`TicketSubscriber::ticket()` dichiarava `user_id` come foreign key. Il modello
punta quindi a un ticket con l'identificativo dell'utente e rende inaffidabile
ogni flusso che risolve il ticket dal record follower.

## Criteri di accettazione

- [x] `ticket()` usa la colonna reale `ticket_id`.
- [x] Il tipo documentato per l'utente segue `UserContract`, non la classe concreta.
- [x] Un test verifica il nome della foreign key della relazione.
- [x] Pest su SQLite condiviso in transazione verifica relazione, follow idempotente e autorizzazione.
- [x] Follow/unfollow e notifiche al cittadino implementati nella [STORY-511](STORY-511-citizen-ticket-follow.md); le preferenze email sono state completate nella [STORY-013](STORY-013-ticket-notification-preferences.md), mentre push resta fuori ambito.

## Verifica

PHPStan `analyse Modules`, Pint e Pest Fixcity passano nel worktree corrente.
Il grant `fixcity_data_test` non viene alterato; il collaudo MariaDB/staging resta distinto dalla verifica SQLite.
