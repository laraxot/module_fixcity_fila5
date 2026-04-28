# Obsidian skills and ingest checklist

## Scopo

Standardizzare il controllo periodico "skills + Obsidian + ingest" in ottica DRY + KISS.

## Checklist operativa

1. verificare i documenti Obsidian disponibili (`docs/.obsidian/README.md`)
2. allineare i concetti modulo/tema con link bidirezionali
3. aggiornare `index.md` e `log.md` del modulo owner
4. aggiornare rule + memory + skill quando emerge una nuova regola stabile
5. eseguire ingest (`qmd update`) dopo nuove pagine wiki
6. fare una query smoke (`qmd search`) sui nuovi termini chiave

## Boundary

- Obsidian e' strumento di navigazione/knowledge graph
- la fonte di verita' resta la struttura `docs/raw` + `docs/wiki`
- le correzioni runtime non si considerano complete senza aggiornamento wiki+ingest

## False friends

- "ho scritto il documento, quindi e' gia' ingestito"
- "basta aggiornare solo il root wiki"
- "skills aggiornate senza traccia in log/index"

## Riferimenti

- [admin ticket create map visual contract](./admin-ticket-create-map-visual-contract.md)
- [filament admin panel map visibility contract](../../../Geo/docs/wiki/concepts/filament-admin-panel-map-visibility-contract.md)
- [filament admin style ownership boundary](../../../../Themes/Sixteen/docs/wiki/concepts/filament-admin-style-ownership-boundary.md)
