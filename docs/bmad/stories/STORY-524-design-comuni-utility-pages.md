---
id: story-524-design-comuni-utility-pages
slug: 524-design-comuni-utility-pages
title: "STORY-524 — FAQ e mappa del sito pubbliche, utili e localizzate"
description: "Sostituisce le risposte vuote delle utility CMS con pagine Folio veritiere, localizzate e coerenti con il perimetro FixCity."
document_type: story
category: frontend
status: verified
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: 2026-09-27
updated_at: 2026-09-27
author: Codex
epic: EPIC-FRONTOFFICE
points: 5
priority: Must
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/522"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/523"
references:
  design_comuni: "https://italia.github.io/design-comuni-pagine-statiche/index.html"
  service_flows: "https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html"
related:
  - ./STORY-517-design-comuni-coverage.md
---

# STORY-524 — Utility pubbliche

## User story

Come visitatore, voglio trovare risposte affidabili e una mappa delle pagine
pubbliche, così posso capire il servizio e raggiungere la funzione corretta.

## Accettazione

- [x] Le due URL non rendono più il solo guscio CMS con HTTP 200.
- [x] FAQ e mappa sono disponibili in IT/EN/DE/ES.
- [x] Il copy non usa Lorem Ipsum né promette funzioni o tempi non configurati.
- [x] I link della mappa puntano a route reali; l'area personale conserva il login redirect guest.
- [x] Browser Chromium: due URL × IT/EN/DE/ES × 320/390/768/1440 px; titoli, link, console e overflow verificati.
- [x] Redirect guest dell'area personale mantiene la lingua in tutte le quattro località.
- [x] Footer offre collegamenti localizzati a FAQ e mappa del sito.
- [x] `view:cache` e `bash bashscripts/quality-gates/verify-llm-wiki.sh` passano.

## Implementazione

UI nel tema Sixteen, renderizzata dall'entrypoint Folio + Volt `container0.index`
attraverso componenti riusabili e file lingua. Questa scelta conserva il router
CMS canonico: il wildcard directory-index Folio precede le view letterali top-level.
Nessun controller, servizio, endpoint o schema DB nuovo.
Il catalogo completo Design Comuni resta guida di copertura: i flussi non offerti
dal prodotto non vengono presentati come disponibili.
