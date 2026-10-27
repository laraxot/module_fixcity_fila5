---
title: "Quality gates — inbound marketing"
type: quality-gates
status: proposed
updated: 2026-10-15
tags: [bmad, ecc, ponytail, filament-blueprint, quality]
qmd: "ECC loop BMAD Ponytail Filament Blueprint inbound quality gates"
issues: []
discussions: []
---
# Quality gates

## Loop ECC + BMAD

1. **Explore:** leggere codice, memoria, pattern AFC/AFerrari/Asso e scopo.
2. **Contract:** aggiornare brief, PRD, architecture, UX, story e dipendenze.
3. **Code only after readiness:** implementare nel solo owner dichiarato.
4. **Check:** PHPStan/Pest/view cache/browser/WCAG secondo lo scope.
5. **Close:** aggiornare story, decision log, memory e indice.

## Filament Blueprint

Il blueprint è una mappa di wiring: `Article`/`ArticleResource` → form/table/query
→ Blog/Cms → tema. Prima si verifica ciò che esiste; si aggiunge solo il minimo
contratto mancante. Nessuna migrazione o Resource nuova senza una prova che il
modello esistente non basti.

## Gate attuale

`CONCERNS`: documentazione completa, ma owner editoriale, privacy evento,
calendario e test di comprensione restano prerequisiti per lo sviluppo.
