---
id: story-520-service-report-detail
slug: 520-service-report-detail
title: "STORY-520 — Scheda pubblica del servizio Segnalare un disservizio"
description: "Rendere disponibile, prima del wizard autenticato, una scheda servizio Design Comuni con destinatari, modalità, requisiti, esito, tempi, costi e CTA localizzata."
document_type: story
category: frontend
status: implemented
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: '2026-09-27'
updated_at: '2026-09-27'
author: opencode-space-bunny
epic: EPIC-FRONTOFFICE
points: 8
priority: Must
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/520"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/521"
reference:
  catalog: "https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-dettaglio.html"
  services: "https://italia.github.io/design-comuni-pagine-statiche/sito/servizi.html"
related:
  - ./STORY-517-design-comuni-coverage.md
---

# Scheda servizio: segnalare un disservizio

## Implementazione

- Pagina Folio pubblica: `/{locale}/services/report-issue`.
- Nessun controller e nessun Services layer: la pagina usa la shell Sixteen e la CTA
  localizzata verso `/{locale}/tickets/create`.
- Contenuti localizzati in `Themes/Sixteen/lang/{it,en,de,es}/service_report.php`.
- Sezioni presenti: destinatari, procedura in tre passi, requisiti, risultato, tempi,
  costi, accesso e nota sull’area personale.
- La card “Segnala un problema” del catalogo `/services` punta alla scheda, non al
  wizard diretto.

## Criteri di accettazione

- [x] HTTP 200 in IT/EN/DE/ES per la pagina dettaglio.
- [x] Titolo, descrizione, breadcrumb, dettagli e CTA localizzati.
- [x] CTA verso il percorso canonico del wizard e redirect guest verso login.
- [x] Nessuna chiave `pub_theme::` o `fixcity::` visibile.
- [x] Nessun overflow a viewport mobile 390px.
- [x] Pagina raggiungibile dal catalogo pubblico `/services`.

## Evidenza

Chromium Playwright su 390×844: quattro locali, HTTP 200, zero errori JavaScript,
zero richieste fallite, zero overflow e CTA presente.
