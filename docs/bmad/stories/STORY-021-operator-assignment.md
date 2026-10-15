---
title: "STORY-021 — Assegnazione e riassegnazione operatore"
type: story
module: Fixcity
status: in_progress
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, fixcity, pa, assignment, audit]
qmd: "operator assignment responsible_id assign unassign activity notification"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ./STORY-499-pa-ticket-assignment.md
  - ../../../app/Actions/AssignTicketAction.php
  - ../../../app/Actions/AssignTicketAction.php
---

# Obiettivo

Un operatore autorizzato assegna o riassegna una segnalazione e il nuovo responsabile riceve un aggiornamento tracciabile.

## Stato nel codice

- [x] Campo dominio `responsible_id` e relazione `assignee` usati dal modulo.
- [x] Azione di assegnazione disponibile dalla scheda PA e controllata dalla policy.
- [x] Activity `assignment` registrata nella stessa operazione applicativa.
- [x] Notifica database post-commit inviata al nuovo responsabile.
- [ ] Test Feature e notifica eseguiti sul DB isolato.
- [ ] Smoke browser per assegnazione, riassegnazione e rimozione.

## Criteri di accettazione

1. Operatore, supervisore o admin possono assegnare secondo policy; cittadino e guest ricevono deny.
2. L'assegnazione aggiorna `responsible_id` e registra autore/timestamp.
3. La notifica non parte se la transazione fallisce e non contiene note interne al cittadino.
4. Riassegnazione e rimozione aggiornano la cronologia in modo coerente.

## Verifica corrente

Il codice e i test sono presenti (STORY-499, `TicketResourceTest`, test della notifica). Pest non ha raggiunto gli assert perché manca l'account MariaDB dedicato.
