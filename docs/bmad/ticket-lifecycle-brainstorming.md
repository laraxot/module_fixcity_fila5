---
title: "BMAD Brainstorming — trasparenza e gestione della segnalazione"
type: bmad-brainstorming
status: synthesized
module: Fixcity
created: 2026-10-16
tags: [bmad, brainstorming, scamper, starbursting, reverse-brainstorming]
related: [./ticket-lifecycle-planning.md, ./ticket-lifecycle-blueprint.md]
---

# Domanda guida

Come può il cittadino capire cosa è successo alla sua segnalazione senza
vedere dati interni, mentre il Comune conserva una storia operativa completa?

## 5 Whys

1. Il cittadino chiede aggiornamenti perché non vede il lavoro invisibile.
2. Il lavoro è invisibile perché mail, deleghe e risposte non sono eventi
   uniformi.
3. Gli eventi non sono uniformi perché lo stato è stato usato come sostituto
   dell'intera pratica.
4. Uno stato non descrive destinatari, tentativi, risposte e scadenze.
5. Serve un ledger append-only con proiezioni diverse per cittadino e PA.

## Starbursting

- **Chi:** cittadino proprietario, moderatore, operatore, ente destinatario,
  supervisore.
- **Cosa:** segnalazione, decisione di moderazione, delega, comunicazione,
  risposta, foto aggiuntiva, risoluzione.
- **Quando:** a ogni transizione o tentativo; mai sovrascrivere la storia.
- **Dove:** timeline cittadina filtrata e vista Filament completa.
- **Perché:** fiducia, accountability e riduzione dei solleciti informali.
- **Come:** `TicketActivity`, policy, CommentsWidget e Media Library.

## SCAMPER convergente

| Leva | Idea | Impatto | Fattibilità |
|---|---|---:|---:|
| Substitute | stato unico → eventi append-only | alto | alta |
| Combine | timeline + prossimi passi + scadenza | alto | alta |
| Adapt | cmp-timeline Design Comuni per pratiche | alto | alta |
| Modify | risposta ente come esito operativo, non chiusura automatica | alto | alta |
| Eliminate | tabelle timeline duplicate | alto | alta |
| Reverse | progettare dai dati che non devono uscire | alto | alta |

## Reverse brainstorming: come rompere il sistema

- mostrare a tutti il ticket `pending`;
- permettere `closed` da qualunque stato;
- salvare solo l'ultimo invio email;
- rendere modificabile il `commentableKey` senza policy;
- mostrare al cittadino l'email privata dell'ufficio;
- allegare una foto senza registrare chi/quando.

## Insight selezionati

1. Il contratto di privacy deve essere applicato alla proiezione, non al
   database intero.
2. “Non competente” è informazione utile e deve generare un prossimo passo,
   non una chiusura.
3. La timeline è un audit umano: ogni voce deve avere data, etichetta,
   visibilità e motivazione sicura.
4. Un sollecito è un evento nuovo e misurabile, mai una modifica dell'invio
   originale.

