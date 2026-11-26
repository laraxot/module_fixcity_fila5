---
title: "STORY-020 — Filtri operativi per la coda ticket"
type: story
module: Fixcity
status: in_progress
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, fixcity, pa, filters, ux]
qmd: "TicketResource filter status priority type assignee date range rating"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../../../app/Filament/Resources/TicketResource/Tables/TicketsTable.php
  - ../../../app/Filament/Resources/TicketResource/Pages/ListTickets.php
---

# Obiettivo

Permettere a operatori e supervisori di restringere la coda per attributi di workflow e periodo, senza esportare o scorrere ticket fuori contesto.

## Stato nel codice

- [x] Filtri per status, priorità, tipologia e presenza di rating.
- [x] Filtro per responsabile con opzioni ricercabili dal model class configurabile via `XotData`.
- [x] Distinzione esplicita tra ticket assegnati e non assegnati con traduzioni IT/EN.
- [x] Intervallo date di creazione inclusivo.
- [x] Test Feature per assegnatario, assegnati/non assegnati e intervallo date aggiunti.
- [ ] Pest eseguito con DB test disponibile.
- [ ] Verifica browser responsive e accessibilità del pannello filtri.

## Criteri di accettazione

1. I filtri composabili non allargano la query oltre il perimetro di autorizzazione PA.
2. L'intervallo data include entrambi i giorni scelti e gestisce un solo estremo.
3. I filtri responsabile e assegnazione distinguono gli assegnati, i non assegnati e lo specifico responsabile.
4. Le etichette derivano dalle traduzioni del modulo.
5. Pest e browser provano almeno una combinazione di filtri e lo stato vuoto.

## Verifica corrente

Le nuove condizioni sono implementate in `TicketsTable`; tre test Feature coprono responsabile, assegnazione/non assegnazione e intervallo date. PHPStan/Pint passano; la suite Pest necessita delle credenziali isolate MariaDB.
