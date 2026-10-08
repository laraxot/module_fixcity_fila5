---
id: design-comuni-confirmation-runtime-2026-09-27
title: "BMAD — Conferma runtime e data bag CMS multilingua"
description: "Confronto, decisione e verifica del flusso di conferma FixCity rispetto al template Design Comuni segnalazione-04-conferma."
document_type: decision
category: frontend
status: implemented
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: '2026-09-27'
updated_at: '2026-09-27'
author: opencode-space-bunny
references:
  design_comuni: "https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-04-conferma.html"
  issue: "https://github.com/laraxot/base_fixcity_fila5/issues/517"
  discussion: "https://github.com/laraxot/base_fixcity_fila5/discussions/518"
---

# Decisione BMAD: conferma runtime

## Problema osservato

La pagina Folio `tickets/confirmation` recupera il payload dalla sessione tramite
`BuildTicketConfirmationDataAction`. Il renderer CMS passava però ai blocchi la sola
configurazione JSON, nascondendo il payload runtime: codice, link di tracking, email e
stato non erano affidabili.

## Decisione architetturale

Il componente theme `x-page` è il punto unico di composizione. Per ogni blocco calcola
`resolvedData = merge(block defaults, page runtime data)` e passa il risultato sia alle
variabili top-level sia alla variabile `data`. Non vengono introdotti controller,
servizi o bypass del data bag CMS.

## Contratto verificato

- `BuildTicketConfirmationDataAction` produce codice, summary, visibilità, receipt,
  tracking URL e area personale.
- `04-conferma.blade.php` rende il codice e il CTA con target esplicito.
- `flow/stepper` usa il namespace dichiarato dal blocco per localizzare titolo e passi.
- rating, breadcrumb e contatti hanno fallback tradotti per IT/EN/DE/ES.

## Evidenza browser

Con Chromium Playwright a viewport 390×844, utente demo autenticato e codice
`DEMO-001`: IT/EN/DE/ES mostrano titolo locale, codice e link di tracking; non c’è
overflow orizzontale e EN/DE/ES non mostrano testo italiano.

## Gate

- PHPStan level 10: `9186/9186`, zero error.
- Blade view cache: pass.
- Seeder demo: pass; codice e GeoJSON rigenerati.
- Il test Pest completo resta dipendente dalle credenziali MariaDB del checkout; non
  vengono inventati grant o modificati segreti d’ambiente.
