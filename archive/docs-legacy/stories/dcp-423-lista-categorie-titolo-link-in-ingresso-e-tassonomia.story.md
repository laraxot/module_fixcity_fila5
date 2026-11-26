---
title: "DCP-423: lista-categorie, titolo, link in ingresso e tassonomia"
type: story
status: draft
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, lista-categorie, link, seo, i18n, decisione]
qmd: "lista-categorie title h1 LC-05 LC-06 LC-09 LC-10 Prenota search-modal blog eventi radici design comuni D-LC-03 D-LC-04"
related:
  - ./dcp-408-lista-categorie-senso-origine-e-difetti-misurati.story.md
issues: []
discussions: []
---

# DCP-423: titolo, link in ingresso, tassonomia

Due parti si possono fare subito (LC-05, LC-10), due aspettano l'utente (LC-09 con D-LC-03, LC-06 con D-LC-04).

## Origine

DCP-408: LC-05 `<title>` «Esplora le segnalazioni» diverso da `h1` «Categorie per le segnalazioni» (sospetto: `lista-categorie.index.json`; sorgente del title non verificata). LC-06: delle 16 radici attive 4 sono «argomenti» Design Comuni (icona `it-arrow-right-circle`, descrizione uguale al nome). LC-09: 4 suggerimenti del search-modal e la CTA «Prenota» di blog ed eventi (`blog.index.json:88`, `events/index.json:89`, `search-modal.json`) puntano a `/lista-categorie`. LC-10: il piano docs di Sixteen descrive un percorso (`[container0]/index` via JSON) che non e' quello vero (Folio page del modulo Fixcity, `name('services.categories')`).

## Acceptance criteria

1. (LC-05) Si trova da dove viene il `<title>` e si sceglie un solo titolo coerente con l'`h1`; per ogni lingua il blocco JSON ha il suo testo, nessuno nel codice.
2. (LC-10) Il piano docs di Sixteen dice come la pagina viene servita davvero; nessun file nuovo nel tema (regola: niente pagine nuove in `Themes/*/resources/views/pages`).
3. (LC-09, dopo D-LC-03) La CTA «Prenota» di blog/eventi e i suggerimenti del search-modal portano dove ha senso e hanno un'etichetta coerente; test o controllo che ogni link risponda 200.
4. (LC-06, dopo D-LC-04) Le 4 radici Design Comuni sono escluse o spostate come deciso; la pagina mostra il numero giusto di card.
5. Le quattro lingue restano allineate (audit con il fallback `it` spento: `bashscripts/tools/i18n/audit-locales.py`).

## Tasks

- [ ] LC-05: leggere come si compone il `<title>` della pagina (JSON, Folio, tema) e correggerlo alla fonte.
- [ ] LC-10: correggere il piano docs di Sixteen (individuarlo con `grep` su `container0` e `lista-categorie`).
- [ ] Chiedere all'utente D-LC-03 e D-LC-04, poi riscrivere i criteri 3 e 4 come tasks.

## Owned file scope

`laravel/config/local/fixcity/database/content/pages/*.json` e `.../content/sections/search-modal.json` (individuare il JSON giusto con `bashscripts/tools/cms/page-json-for-url.sh <url>`, senza inventare chiavi), docs di Sixteen, lang del modulo. Mai una pagina o un alias nel tema.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato; DCP-408 e' in corso di un'altra sessione |
| 2026-10-27 | Claude | LC-10 | il criterio 2 ha il dettaglio con la prova in [DCP-439](../../../../Themes/Sixteen/docs/stories/dcp-439-piano-docs-sixteen-dice-il-falso-su-chi-serve-lista-categorie.story.md); chi chiude una spunta l'altra |
