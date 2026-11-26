---
title: "STORY-012 — Valutazione cittadino dopo risoluzione"
id: STORY-012
author: BMAD
status: in-progress
priority: high
type: story
module: Fixcity
tags: [bmad, fixcity, rating, citizen]
created: legacy
updated: 2026-09-27
qmd: "STORY-012 rating cittadino risoluzione ticket"
issues:
  - https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
  - https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

# Valutazione cittadino dopo risoluzione

## Obiettivo

Il proprietario autenticato valuta da 1 a 5 stelle una segnalazione chiusa o
risolta. Un guest o un altro utente non deve poter vedere o inviare il prompt.

## Flusso implementato

1. La pagina CMS `tickets.view` monta `Ticket\\ViewWidget`.
2. Il widget mostra `TicketCitizenRatingPromptWidget` solo se l’utente autenticato
   possiede il ticket e lo stato è `RESOLVED` o `CLOSED`.
3. `SubmitCitizenTicketRatingAction` valida proprietario, stato, unicità e
   intervallo; persiste il voto come `RatingMorph` nel modulo Rating.
4. Il widget conferma l’invio senza ricaricare la pagina.
5. `GetCitizenRatingAggregateAction` calcola l’aggregato usato dal backoffice.

## Criteri di accettazione

- [x] Il prompt appare al proprietario quando il ticket è risolto.
- [x] Il prompt non appare a un altro utente né prima della risoluzione.
- [x] Il voto valido viene persistito e il widget mostra conferma.
- [x] Il valore fuori intervallo è rifiutato.
- [x] L'Action autorizza il voto solo tramite `owner_id`; `created_by` e `updated_by` restano audit metadata.
- [x] Il secondo invio dello stesso proprietario viene rifiutato senza creare un altro `RatingMorph`.
- [x] La persistenza blocca il record Ticket con `lockForUpdate()` durante controllo e scrittura.
- [x] Il widget di riepilogo PA estende `XotBaseStatsOverviewWidget`, è registrato nella coda ticket e mostra media e conteggio con dati presenti.
- [x] Il widget Livewire mostra media `—` e conteggio `0` quando non esistono voti.
- [ ] Browser visuale e tastiera verificati su mobile e desktop.
- [ ] Gara concorrente dimostrata con due transazioni/sessioni su MariaDB pilota; il test SQLite attuale prova solo l'idempotenza sequenziale.
- [ ] Stati vuoti, accessibilità e resa visuale del riepilogo PA verificati in browser.

## Confini e gap espliciti

Non esistono nel flusso corrente `TicketCitizenRatingEvent`, endpoint HTTP
`POST /tickets/{id}/rating` o notifica PA automatica per voto basso: non vanno
descritti come implementati. Il testo libero e le preferenze di notifica non
sono inclusi nei criteri attuali.

## Evidenza

`APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity/tests/Feature/Filament/TicketCitizenRatingPromptWidgetTest.php`:
6 test / 16 asserzioni verdi, inclusi audit-only denial e doppio invio. Il widget
PA è coperto con aggregato popolato (`5/5`, conteggio `1`) e empty state (`—`,
conteggio `0`); usa la base Xot canonica ed è registrato nella coda Ticket.
Test mirato: 2 test / 10 asserzioni. Suite Fixcity completa: 348 test / 1.430
asserzioni; PHPStan globale `analyse Modules` senza errori. Browser/staging, gara
concorrente MariaDB e verifica visuale/accessibile restano aperti.

## Collegamenti Second Brain

- [Journey utenti](../../wiki/concepts/user-journey-map.md)
- [Flusso rating in RatingMorph](../../wiki/concepts/ticket-citizen-rating-via-rating-module.md)
